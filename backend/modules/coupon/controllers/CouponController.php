<?php

namespace backend\modules\coupon\controllers;

use backend\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\lib\user\UserStats;
use common\utils\SecurityUtils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class CouponController extends BaseController
{
    /**
     * 代金券添加.
     */
    public function actionAdd()
    {
        $model = new CouponType([
            'sn' => null,
            'isDisabled' => 0,
            'isAudited' => 0,
            'isAppOnly' => 0,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $this->preprocess($model)) {
            if (!$model->save(false)) {
                throw new \Exception('数据库错误');
            } else {
                return $this->redirect('list');
            }
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * 代金券添加编辑预处理.
     */
    private function preprocess(CouponType $obj)
    {
        if ($obj->isAudited) {
            throw new \Exception();
        }

        if (empty($obj->expiresInDays) && empty($obj->useEndDate)) {
            $obj->addErrors(['expiresInDays' => '有效天数与截止日期不能都为空', 'useEndDate' => '有效天数与截止日期不能都为空']);

            return false;
        }

        if (!empty($obj->expiresInDays) && !empty($obj->useEndDate)) {
            $obj->addErrors(['expiresInDays' => '有效天数与截止日期不能同时填写', 'useEndDate' => '有效天数与截止日期不能同时填写']);

            return false;
        }

        if ($obj->issueEndDate < $obj->issueStartDate) {
            $obj->addErrors(['issueEndDate' => '发放结束日期必须大于等于发放开始日期', 'issueStartDate' => '发放结束日期必须大于等于发放开始日期']);

            return false;
        }

        return true;
    }

    /**
     * 代金券修改.
     */
    public function actionEdit($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(CouponType::class, $id);

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $this->preprocess($model)) {
            if (!$model->save(false)) {
                throw new \Exception('数据库错误');
            } else {
                return $this->redirect('list');
            }
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * 代金券列表.
     */
    public function actionList()
    {
        $query = CouponType::find()->where(['isDisabled' => 0]);

        $name = Yii::$app->request->get('name');
        if (!empty($name)) {
            $query->andFilterWhere(['like', 'name', $name]);
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render('list', ['model' => $model, 'name' => $name, 'pages' => $pages]);
    }

    /**
     * 根据时间查询产生列表数据
     *
     */
    public function actionMonthList()
    {
        $listTime = $this->getListTime(Yii::$app->request->get());
        $listTimeStr = $listTime['str'];
        $listTimeEnd = $listTime['end'];
        $query = $this->getData(Yii::$app->request->get());
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('month_list', [
            'listTimeStr' =>$listTimeStr,
            'listTimeEnd' =>$listTimeEnd,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 根据时间查询月代金券数据，列表显示导出数据
     */
    public function actionExport()
    {
        $listTime = $this->getListTime(Yii::$app->request->get());
        $query = $this->getData(Yii::$app->request->get());
        $exportData[] = [
            '用户名称',
            '用户手机号',
            '单张代金券使用金额',
            '单张代金券使用时间',
        ];
        $allData  =  $query->all();
        foreach ($allData as  $v ){
            $user = $v->user;
            $exportData[] =[
                isset($user->real_name) ? $user->real_name : '---',
                isset($user->safeMobile) ? SecurityUtils::decrypt($user->safeMobile) : '---',
                isset($v->couponType->amount) ? floatval($v->couponType->amount) : '---',
                date('Y-m-d H:i:s',$v->order->created_at),
            ];
        }
        $exporName = '月度代金券'.date('Ymd',strtotime($listTime['str'])).'-'.date('Ymd',strtotime($listTime['end'])).'.xlsx';
        UserStats::exportAsXlsx($exportData, $exporName);
    }

    /**
     * 返回查询出的月度代金券数据对象
     */
    private function getData()
    {
        $listTime = $this->getListTime(Yii::$app->request->get());
        $UserCouponTbale = UserCoupon::tableName();
        $OnlineOrderTable = OnlineOrder::tableName();
        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->innerJoinWith('order')
            ->innerJoinWith('user')
            ->where("$UserCouponTbale.isUsed = 1")
            ->andWhere(["$OnlineOrderTable.status" => 1])
            ->andWhere(['>=', "date(from_unixtime($OnlineOrderTable.created_at))", $listTime['str']])
            ->andWhere(['<=', "date(from_unixtime($OnlineOrderTable.created_at))", $listTime['end']])
            ->orderBy("$OnlineOrderTable.created_at desc");
        return $query;
    }

    /**
     * 返回处理开始结束时间，判断时间并交换时间大小顺序
     */
    private function getListTime()
    {
        $listTime = [];
        if(Yii::$app->request->get('listTimeStr') && Yii::$app->request->get('listTimeEnd')){
            $str = strtotime(Yii::$app->request->get('listTimeStr'));
            $end = strtotime(Yii::$app->request->get('listTimeEnd'));
            $listTime['str'] = $end > $str ? Yii::$app->request->get('listTimeStr') : Yii::$app->request->get('listTimeEnd');
            $listTime['end'] = $end > $str ? Yii::$app->request->get('listTimeEnd') : Yii::$app->request->get('listTimeStr');
        } else {
            $listTime['str'] = date('Y-m-01', strtotime('-1 month'));
            $listTime['end'] = date('Y-m-t', strtotime('-1 month'));
        }
        return $listTime;
    }

    /**
     * 用户代金券列表.
     * 1. 每页最多显示15条记录;
     * 2. 按照代金券发放时间的降序排列;
     */
    public function actionListForUser($uid)
    {
        $user = $this->findOr404(User::class, $uid);
        $u = UserCoupon::tableName();
        $isUsed = Yii::$app->request->get('isUsed');
        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->joinWith('admin')
            ->where(['user_id' => $uid]);
        if ($isUsed == 2) {
            $query->andWhere(['isUsed' => 1]);
        } elseif ($isUsed == 1) {
            $query->andWhere(['isUsed' => 0])->andWhere(['>' , 'expiryDate' , date('Y-m-d')]);
        } elseif ($isUsed == 3) {
            $query->andWhere(['isUsed' => 0])->andWhere(['<' , 'expiryDate' , date('Y-m-d')]);
        }
        $query->orderBy(["$u.created_at" => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $sumCoupon = UserCoupon::findCouponInUse($user->id , date('Y-m-d'))->sum('amount');
        $CouponUsed = UserCoupon::findCouponUsed($user->id)->sum('amount');

        return $this->renderFile('@backend/modules/coupon/views/coupon/user_list.php', ['dataProvider' => $dataProvider, 'user' => $user, 'sumCoupon' => $sumCoupon, 'CouponUsed' =>$CouponUsed]);
    }

    /**
     * 获取可以发放的代金券信息.
     *
     * 1. 按面值的升序为第一序列,起投金额的升序为第二序列排序;
     */
    public function actionAllowIssueList($uid, $cid = null)
    {
        $nowDate = date('Y-m-d');

        $query = CouponType::find()
            ->where(['<=', 'issueStartDate', $nowDate])
            ->andWhere(['>=', 'issueEndDate', $nowDate])
            ->andWhere(['isDisabled' => 0, 'isAudited' => 1]);

        if (!empty($cid)) {
            $query->andWhere(['id' => $cid]);
        }

        if (Yii::$app->request->isAjax) {
            return ['code' => 0, 'data' => $query->asArray()->all()];
        }

        $query->orderBy(['amount' => SORT_ASC, 'minInvest' => SORT_ASC]);

        $this->layout = false;

        return $this->render('issue_list', ['model' => $query->all(), 'uid' => $uid]);
    }

    /**
     * 为个人发放代金券.
     */
    public function actionIssueForUser($uid, $cid)
    {
        $user = $this->findOr404(User::class, $uid);
        $coupon = $this->findOr404(CouponType::class, $cid);

        $res = 1;
        $mess = '发券失败';

        if ($coupon->allowIssue()) {
            try {
                $userCoupon = UserCoupon::addUserCoupon($user, $coupon);

                $userCoupon->admin_id = $this->admin_id;
                $userCoupon->ip = Yii::$app->request->getUserIP();

                if ($userCoupon->save()) {
                    $res = 0;
                    $mess = '发券成功';
                }
            } catch (\Exception $ex) {
                $mess = $ex->getMessage();
            }
        }

        return ['code' => $res, 'message' => $mess];
    }

    /**
     * 代金券审核.
     */
    public function actionAudit($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(CouponType::class, $id);

        $model->isAudited = 1;

        if (!$model->save(false)) {
            return ['code' => 1];
        }

        return ['code' => 0];
    }

    /**
     * 代金券领取记录.
     */
    public function actionOwnerList($id)
    {
        $status = Yii::$app->request->get('status');

        if (empty($id) || !preg_match('/^[0-9]+$/', $id)) {
            throw $this->ex404();
        }

        if (!empty($status) && !in_array($status, ['', 'a', 'b', 'c'])) {
            throw $this->ex404();
        }

        $uc = UserCoupon::tableName();
        $u = User::tableName();

        $query = UserCoupon::find()
            ->innerJoin($u, "$uc.user_id = $u.id")
            ->where(["$uc.couponType_id" => $id]);

        if (!empty($status)) {
            if ('a' === $status) {
                $query->andWhere(["$uc.isUsed" => false]);
                $query->andWhere(['>=', "$uc.expiryDate", date('Y-m-d')]);
            } elseif ('b' === $status) {
                $query->andWhere(["$uc.isUsed" => true]);
            } elseif ('c' === $status) {
                $query->andWhere(["$uc.isUsed" => false]);
                $query->andWhere(['<', "$uc.expiryDate", date('Y-m-d')]);
            }
        }

        $query->orderBy(["$uc.created_at" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $this->render('owner_list', ['coupon_id' => $id, 'status' => $status, 'dataProvider' => $dataProvider]);
    }
}
