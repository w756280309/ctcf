<?php

namespace backend\modules\o2o\controllers;

use backend\controllers\BaseController;
use common\filters\MyReadFilter;
use common\lib\user\UserStats;
use common\models\affiliation\Affiliator;
use common\models\code\GoodsType;
use common\models\code\VirtualCard;
use common\models\offline\ImportForm;
use common\utils\SecurityUtils;
use yii\data\ActiveDataProvider;

class CardController extends BaseController
{
    private function getCardListQuery(array $params)
    {
        if (!isset($params['affId']) || $params['affId'] <= 0) {
            throw $this->ex404();
        }
        $params = array_replace([
            'goodsType_name' => null,
            'card' => null,
            'pullDate' => null,
            'status' => null,
        ], $params);
        $g = GoodsType::tableName();
        $v = VirtualCard::tableName();
        $query = VirtualCard::find()
            ->innerJoinWith('goods')
            ->joinWith('user')
            ->select('*')
            ->addSelect('expiredTime > CURTIME() as isExpired')
            ->addSelect('(isPull = 1 and (expiredTime = null or expiredTime <= CURTIME())) as isSend')
            ->where(["$g.type" => 3, "$v.affiliator_id" => $params['affId']]);

        if (isset($params['goodsType_name']) && !empty($params['goodsType_name'])) {
            $query->andWhere(['like', "$g.name", $params['goodsType_name']]);
        }

        if (isset($params['serial']) && !empty($params['serial'])) {
            $query->andWhere(['like', "$v.serial", $params['serial']]);
        }

        if (isset($params['pullDate']) && !empty($params['pullDate'])) {
            $query->andWhere(['date(pullTime)' => $params['pullDate']]);
        }

        if (isset($params['status']) && !empty($params['status'])) {
            switch ($params['status']) {
                case 1:
                    $query->andWhere(['isPull' => false]);
                    break;
                case 2:
                    $query->andWhere(['isPull' => true, 'isUsed' => false]);
                    $query->andFilterWhere(['>', 'expiredTime', date('Y-m-d H:i:s')]);
                    $query->orWhere([
                        'isPull' => true,
                        'isUsed' => false,
                        'expiredTime' => null,
                        "$g.type" => 3,
                        "$v.affiliator_id" => $params['affId'],
                    ]);
                    break;
                case 3:
                    $query->andWhere(['isUsed' => true]);
                    break;
                case 4:
                    $query->andWhere(['isPull' => true, 'isUsed' => false]);
                    $query->andFilterWhere(['<', 'expiredTime', date('Y-m-d H:i:s')]);
                    break;
            }
        }
        $query->orderBy([
            'isPull' => SORT_ASC,
            'isSend' => SORT_ASC,
            'isUsed' => SORT_DESC,
            'isExpired' => SORT_DESC,
            "$v.id" => SORT_DESC,
        ]);

        return $query;
    }

    /**
     * 兑换码列表页面
     */
    public function actionList()
    {
        $search = \Yii::$app->request->get();
        $affiliator = Affiliator::findOne($search['affId']);
        if (null === $affiliator) {
            throw $this->ex404('商家不存在');
        }
        $query = $this->getCardListQuery($search);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        return $this->render('list', ['dataProvider' => $dataProvider, 'request' => $search, 'affiliatorName' => $affiliator->name]);
    }

    /**
     * 兑换码列表导出
     */
    public function actionExport()
    {
        $query = $this->getCardListQuery(\Yii::$app->request->get());
        $cards = $query->all();

        $exportData[] = [
            '兑换码',
            '商品名称',
            '客户手机号',
            '发放时间',
            '使用时间',
            '状态',
        ];
        foreach ($cards as $card) {
            $exportData[] = [
                $card->serial,
                $card->goods->name,
                isset($card->user_id) ? SecurityUtils::decrypt($card->user->safeMobile) : '',
                null !== $card->pullTime ? $card->pullTime : '',
                null !== $card->usedTime ? $card->usedTime : '',
                $card->getStatusLabel(),
            ];
        }
        $name = 'O2O兑换码列表' . time() . rand(1000, 9999) .'.xlsx';
        UserStats::exportAsXlsx($exportData, $name);
    }

    /**
     * 补充兑换码页面
     */
    public function actionSupplement($affId)
    {
        $aff = $this->findOr404(Affiliator::class, $affId);
        $goods = $aff->goods;
        $model = new ImportForm();

        if (empty($goods)) {
            return $this->redirect(['card/list', 'affId' => $affId]);
        }
        $infoPost = \Yii::$app->request->post();
        if (!empty($infoPost['flag'])) {
            $filename = $_FILES['ImportForm']['name']['excel'];
            if (!isset($filename) || empty($filename)) {
                $model->addError('excel', '未选择文件');
            }
            if (substr($filename, -4, 4) !== '.xls' && substr($filename, -5, 5) !== '.xlsx') {
                $model->addError('excel', '上传的文件为非.xlsx或.xls文件');
            }
            $filepath = $_FILES['ImportForm']['tmp_name']['excel'];
            $goodsType = GoodsType::findOne($infoPost['gid']);
            if (null === $goodsType) {
                $model->addError('excel', '兑换码对应的商品不存在');
            }

            try {
                $arr = $this->readExcelToArray($filepath);
            } catch (\Exception $ex) {
                $model->addError('excel', $ex->getMessage());
            }
            if (!$model->hasErrors()) {
                $db = \Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    foreach ($arr as $key => $data) {
                        if (empty(array_filter($data))) {
                            continue;
                        }
                        $newCard = new VirtualCard();
                        $newCard->goodsType_id = $goodsType->id;
                        $newCard->createTime = date('Y-m-d H:i:s');
                        $newCard->affiliator_id = $affId;
                        $newCard->serial = trim($data[0]);
                        if (!empty(trim($data[1]))) {
                            $newCard->secret = trim($data[1]);
                        }
                        if ($newCard->validate()) {
                            $newCard->save();
                        } else {
                            $error_index = $key + 1;
                            throw new \Exception('第' . $error_index . '行：' . current($newCard->firstErrors));
                        }
                    }
                    $effectDays = $infoPost['effectDays'] > 0 ? (int) $infoPost['effectDays'] : null;
                    if ($goodsType->effectDays !== $effectDays) {
                        $goodsType->effectDays = $effectDays;
                        if (!$goodsType->save()) {
                            throw new \Exception('商品有效期天数更新失败');
                        }
                    }
                    $transaction->commit();
                    return $this->redirect(['card/list', 'affId' => $affId]);
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                    $model->addError('excel', $ex->getMessage());
                }
                //删除临时文件
                @unlink($filepath);
            }
        }
        return $this->render('supplement', ['model' => $model, 'affId' => $affId, 'goods' => $goods]);
    }

    /**
     * excel的数据读取到二维数组中
     *
     * @param $filePath
     * @return array
     * @throws \Exception
     */
    private function readExcelToArray($filePath)
    {
        $filterSubset = new MyReadFilter(['A', 'B']);
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                throw new \Exception('读取文件错误');
            }
        }

        $PHPReader->setReadFilter($filterSubset);
        $PHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $currentSheet = $PHPExcel->getSheet(0);
        $row = $currentSheet->getHighestRow();
        $max_read_line = MyReadFilter::MAX_READ_LINE;
        if ($row > $max_read_line) {
            throw new \Exception('该excel文件行数超出' . $max_read_line . '行');
        }

        $content = $currentSheet->toArray('', true, true);
        return $content;
    }
}

