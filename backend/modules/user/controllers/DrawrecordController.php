<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\models\order\OnlineFangkuan;
use common\models\product\OnlineProduct;
use common\models\user\DrawRecord;
use common\models\user\User;
use common\utils\SecurityUtils;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class DrawrecordController extends BaseController
{
    /**
     * 融资方提现流水明细.
     */
    public function actionDetail($id)
    {
        if (empty($id)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $user = $this->findOr404(User::class, $id);
        $isOrg = $user->isOrgUser();

        //提现明细页面的搜索功能
        $status = intval(Yii::$app->request->get('status'));
        $time = Yii::$app->request->get('time');

        $query = DrawRecord::find()
            ->where(['uid' => $id]);

        if (!$isOrg && $status > 0) {
            $query->andWhere(['status' => $status - 1]);
        }

        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time.' 23:59:59')]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time.' 0:00:00')]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $fangkuan = null;

        if ($isOrg) {
            $l = OnlineProduct::tableName();
            $f = OnlineFangkuan::tableName();

            $fangkuan = OnlineFangkuan::find()
                ->leftJoin($l, "$f.online_product_id = $l.id")
                ->where(["$f.sn" => ArrayHelper::getColumn($model, 'orderSn')])
                ->indexBy('sn')
                ->select("$l.title, $f.*")
                ->asArray()
                ->all();
        }

        $moneyTotal = 0;  //提现总额
        $successNum = 0;  //成功笔数
        $failureNum = 0;  //失败笔数

        $numdata = DrawRecord::find()
            ->where(['uid' => $id])
            ->all();

        foreach ($numdata as $data) {
            if (DrawRecord::STATUS_SUCCESS === $data->status) {
                $moneyTotal = bcadd($moneyTotal, $data->money, 2);
                ++$successNum;
            } elseif (DrawRecord::STATUS_FAIL === $data->status) {
                ++$failureNum;
            }
        }

        return $this->render('list', [
            'status' => $status,
            'time' => $time,
            'model' => $model,
            'fangkuan' => $fangkuan,
            'pages' => $pages,
            'user' => $user,
            'moneyTotal' => $moneyTotal,
            'successNum' => $successNum,
            'failureNum' => $failureNum,
        ]);
    }

    /**
     * 查询提现记录在联动一侧的状态.
     */
    public function actionUmpStatus()
    {
        $id = Yii::$app->request->get('id');
        $draw = DrawRecord::findOne($id);

        if (null !== $draw) {
            $res = Yii::$container->get('ump')->getDrawInfo($draw);

            if ($res->isSuccessful()) {
                $tranState = $res->get('tran_state');
                $status = [
                    0 => '初始',
                    1 => '受理中',
                    2 => '成功',
                    3 => '失败',
                    4 => '不明',
                    5 => '交易关闭',
                    6 => '其他',
                    12 => '已冻结（正常，等待处理）',
                    13 => '待冻结',
                    14 => '财务已审核',
                    15 => '财务审核失败',
                ];

                if (isset($status[$tranState])) {
                    return ['code' => 0, 'message' => $status[$tranState]];
                }

                return ['code' => 1, 'message' => '返回信息不明确'];
            }

            return ['code' => 1, 'message' => '['.$res->get('ret_code').']'.$res->get('ret_msg')];
        }

        return ['code' => 1, 'message' => '订单不存在'];
    }

    /*
     * 会员管理 提现申请页面.
     */
    public function actionApply()
    {
        $query = User::find();
        $request = Yii::$app->request->get();

        if (!empty($request['name'])) {
            $query->andFilterWhere(['like', 'real_name', $request['name']]);
        }
        if (!empty($request['mobile'])) {
            if (strlen(trim($request['mobile'])) < 11){
                $query->andFilterWhere(['like', 'mobile', trim($request['mobile'])]);
            } else {
                $query->andFilterWhere(['safeMobile'=>SecurityUtils::encrypt(trim($request['mobile']))]);
            }
        }

        $tzUser = $query->andWhere('type=1')->asArray()->all();
        $res = [];
        foreach ($tzUser as $k => $v) {
            $res[$v['id']] = $v;
        }
        $arr = [];
        foreach ($tzUser as $k => $v) {
            $arr[] = $v['id'];
        }

        $draw = DrawRecord::find()->where(['in', 'uid', $arr]);
        if (!empty($request['starttime'])) {
            $draw->andFilterWhere(['>=', 'created_at', strtotime($request['starttime'])]);
        }
        if (!empty($request['endtime'])) {
            $draw->andFilterWhere(['<=', 'created_at', strtotime($request['endtime']) + 24 * 60 * 60]);
        }

        $pages = new Pagination(['totalCount' => $draw->count(), 'pageSize' => '10']);
        $model = $draw->offset($pages->offset)->limit($pages->limit)->orderBy('created_at DESC')->all();

        return $this->render('apply', [
            'res' => $res,
            'model' => $model,
            'category' => 1,
            'pages' => $pages,
            'request' => $request,
        ]);
    }
}
