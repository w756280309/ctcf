<?php

namespace backend\modules\growth\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\code\Code;
use common\models\coupon\CouponType;
use common\models\code\GoodsType;
use common\models\user\User;
use common\utils\StringUtils;
use common\utils\TxUtils;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class CodeController extends BaseController
{
    /**
     * 添加兑换码和导出txt显示页面
     */
    public function actionAdd()
    {
        $goods = GoodsType::find()->all();

        return $this->render('add', ['goods' => $goods]);
    }

    /**
     * 下载中间页
     */
    public function actionRefer()
    {
        $id = trim(Yii::$app->request->post('gid'));
        $num = Yii::$app->request->post('num');
        $expiresAt = Yii::$app->request->post('expiresAt');

        return $this->render('export', ['id' => $id, 'num' => $num, 'expiresAt' => $expiresAt]);
    }


    /**
     * 生成并导出txt
     */
    public function actionCreate()
    {
        $id = trim(Yii::$app->request->get('gid'));
        $num = Yii::$app->request->get('num');
        $expiresAt = Yii::$app->request->get('expiresAt');
        $codes = [];
        $goods = GoodsType::findOne($id);

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $div = floor($num / 100);
            $mod = $num % 100;
            if ($div > 0) {
                for ($i = 0; $i < $div; $i++) {
                    $codes = ArrayHelper::merge($codes, $this->insertCodes($db, 100, $goods, $expiresAt));
                }
            }
            if ($mod > 0) {
                $codes = ArrayHelper::merge($codes, $this->insertCodes($db, $mod, $goods, $expiresAt));
            }
            $transaction->commit();
            if (!empty($codes)) {
                $this->exportTxt($codes, $goods->name);
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }
    }


    /**
     * 导出txt,数据量较小可暂时不考虑导出的内存影响
     */
    private function exportTxt($codes, $fileName)
    {
        $str = '';
        $fileName .= date('Ymd');
        $br = preg_match('/win/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : PHP_EOL;
        foreach ($codes as $code) {
            $str .= $code . $br;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$fileName.'.txt"');
        echo $str;
        exit;
    }

    /**
     * 导出该商品类型下的所有商品
     */
    public function actionExportAll($sn)
    {
        $codeArr = Code::find()->where(['goodsType_sn' => $sn])->asArray()->all();
        $codes = ArrayHelper::getColumn($codeArr, 'code');
        $goodName = GoodsType::find()->where(['sn' => $sn])->column();
        $fileName = $goodName['name'];
        $this->exportTxt($codes, $fileName);
    }

    /**
     * 插入指定条数的兑换码（根据商品信息和兑换截止时间）
     */
    private function insertCodes($db, $num, $goods, $expiresAt)
    {
        $codes = [];
        for ($j = 0; $j < $num; $j++) {
            $codes[$j] = [
                $goods->sn,
                $goods->type,
                date('Y-m-d H:i:s'),
                $expiresAt,
                'code' => Code::createCode(),
            ];
        }
        $db->createCommand()->batchInsert('code', ['goodsType_sn', 'goodsType', 'createdAt', 'expiresAt', 'code'], $codes)->execute();
        return ArrayHelper::getColumn($codes, 'code');
    }

    /**
     * 查看兑换码列表
     */
    public function actionList($sn = '', $code = '')
    {
        //页面的搜索功能
        if (empty($sn) && empty($code)) {
            throw $this->ex404();
        }
        $query = Code::find()->where($code ? ['code' => $code] : ['goodsType_sn' => $sn])->orderBy(['isUsed' => SORT_ASC]);
        $sn = $code ? Code::findOne(['code' => $code])->goodsType_sn : $sn;
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        $goods = GoodsType::findOne(['sn' => $sn]);

        return $this->render('list', ['model' => $model, 'pages' => $pages,'goods' => $goods]);
    }

    /**
     * 补充领取人页面
     */
    public function actionPullUser($id)
    {
        $model = $this->findOr404(Code::class, $id);
        return $this->render('pull-user', ['model' => $model]);
    }

    /**
     * 实体商品需补充领取人(兑吧已领取验证码)
     */
    public function actionDraw($cid)
    {
        $mobile = trim(Yii::$app->request->post('mobile'));
        $usedAt = Yii::$app->request->post('usedAt');

        if (empty($cid) || ($code = Code::findOne($cid)) === null) {
            return ['code' => 1, 'message' => '未找到该条兑换码记录'];
        }

        if (null === ($user = User::findOne(['mobile' => $mobile]))) {
            return ['code' => 1, 'message' => '未找到对应手机号的领取人'];
        }

        if ($code->isUsed) {
            return ['code' => 1, 'message' => '已领取'];
        }

        $code->user_id = $user->id;
        $code->isUsed = true;
        $code->usedAt = $usedAt;
        try {
            if ($code->save()) {
                AdminLog::initNew($code)->save(false);
                return ['code' => 0, 'message' => '补充领取成功'];
            }
        } catch (\Exception $ex) {
            return ['code' => 1, 'message' => '请联系相关技术人员，'.$ex->getMessage()];
        }
    }

    /**
     * 商品列表
     */
    public function actionGoodsList()
    {
        $c = Code::tableName();
        $g = GoodsType::tableName();
        $query = (new Query())
            ->select("$g.name, count($c.id) as total, $c.goodsType, $g.createdAt, $g.sn")
            ->from($g)
            ->leftJoin($c, "$c.goodsType_sn = $g.sn");

        $query->orderBy(["$g.createdAt" => SORT_DESC])->groupBy('sn');
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('goods-list', ['model' => $model, 'pages' => $pages]);
    }

    /**
     * 添加商品
     */
    public function actionGoodsAdd()
    {
        $goodsType = new GoodsType();
        if ($goodsType->load(Yii::$app->request->post())
        ) {
            if (2 === (int)$goodsType->type) {
                $goodsType->sn = TxUtils::generateSn('SP');
            }
            if ($goodsType->validate()
                && $this->validateGoodstype($goodsType)
                && $this->tianJia($goodsType)) {
                $this->redirect('/growth/code/goods-list');
            }
        }
        $nowDate = date('Y-m-d');
        $query = CouponType::find()
            ->where(['<=', 'issueStartDate', $nowDate])
            ->andWhere(['>=', 'issueEndDate', $nowDate])
            ->andWhere(['isDisabled' => 0, 'isAudited' => 1]);
        $data = $query->orderBy('id desc')->all();

        $model = ['' => '--请选择--'];
        foreach ($data as $key => $val) {
            $model[$val['id']] = $val['name'].'  面值:'.StringUtils::amountFormat2($val['amount']).'元  起投金额:'.StringUtils::amountFormat2($val['minInvest']).'元';
        }
        return $this->render('goods-add', ['goodsType' => $goodsType, 'model' => $model]);
    }

    /**
     * 验证添加信息.
     */
    private function validateGoodstype($goodsType)
    {
        if ((1 === (int)$goodsType->type) && empty($goodsType->sn)) {
            $goodsType->addError('sn', '请选择代金券!');
            return false;
        }
        return true;
    }

    private function tianJia(GoodsType $goodsType)
    {
        $goodsType->createdAt = date("Y-m-d H:i:s");
        $goodsType->save(false);

        return true;
    }
}
