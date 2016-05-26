<?php

namespace backend\modules\coupon\controllers;

use backend\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use Yii;
use yii\data\Pagination;
use yii\web\Response;

class CouponController extends BaseController
{
    public function init()
    {
        parent::init();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 代金券添加
     */
    public function actionAdd()
    {
        $model = new CouponType([
            'isDisabled' => 0,
            'isAudited' => 0,
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
     * 代金券添加编辑预处理
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

        return true;
    }

    /**
     * 代金券修改
     */
    public function actionEdit($id)
    {
        if (empty($id)) {
            $this->ex404();
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
     * 代金券列表
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
     * 代金券发放
     */
    public function actionIssue()
    {
        return ['code' => 0];
    }

    /**
     * 代金券审核
     */
    public function actionAudit($id)
    {
        if (empty($id)) {
            $this->ex404();
        }

        $model = $this->findOr404(CouponType::class, $id);

        $model->isAudited = 1;

        if (!$model->save(false)) {
            return ['code' => 1];
        }

        return ['code' => 0];
    }

    /**
     * 代金券领取记录
     */
    public function actionOwnerList($id)
    {
        $status = Yii::$app->request->get('status');

        if (empty($id) || !preg_match('/^[0-9]+$/', $id)) {
            $this->ex404();
        }

        if (!empty($status) && !in_array($status, ['', 'a', 'b'])) {
            $this->ex404();
        }

        $uc = UserCoupon::tableName();
        $u = User::tableName();

        $query = (new \yii\db\Query())
            ->from($uc)
            ->innerJoin($u, "$uc.user_id = $u.id")
            ->where(["$uc.couponType_id" => $id])
            ->select("$u.*, $uc.created_at as collectDateTime, $uc.isUsed");

        if (!empty($status)) {
            if ('a' === $status) {
                $query->andWhere(["$uc.isUsed" => 0]);
            } else {
                $query->andWhere(["$uc.isUsed" => 1]);
            }
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render('owner_list', ['model' => $model, 'coupon_id' => $id, 'status' => $status, 'pages' => $pages]);
    }
}