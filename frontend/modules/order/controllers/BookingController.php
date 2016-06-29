<?php

namespace frontend\modules\order\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\booking\BookingProduct;
use common\models\booking\BookingLog;
use yii\filters\AccessControl;

class BookingController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [    //登录控制,如果没有登录,则跳转到登录页面
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionBook($pid)
    {
        if (empty($pid)) {
            throw $this->ex404();
        }

        $product = BookingProduct::findOne($pid);
        if (null === $product) {
            throw $this->ex404();
        }

        $now = time();
        $count = BookingLog::find()->where(['pid' => $pid, 'uid' => $this->getAuthedUser()->id])->count();

        if ($product->is_disabled || (!empty($product->start_time) && $now < $product->start_time) || (!empty($product->end_time) && $now > $product->end_time) || $count) {
            return $this->redirect('introduction?pid='.$pid);
        }

        $model = new BookingLog([
            'uid' => $this->getAuthedUser()->id,
            'pid' => $pid,
        ]);


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->fund < $product->min_fund) {
                $model->addError("fund", '您预约的金额小于项目的起投金额');
            }

            if ($model->fund > $product->total_fund) {
                $model->addError("fund", '您预约的金额大于项目的总金额');
            }

            //没有验证错误的情况
            if (!$model->hasErrors()) {
                if ($model->save()) {
                    return ['code' => 0, 'message' => '预约申请成功'];
                }
                return ['code' => 4, 'message' => '预约申请失败'];
            }

            $message = $model->firstErrors;
            $key = array_keys($message)[0];
            if ('name' === $key) {
                $code = 1;
            } else if ('mobile' === $key) {
                $code = 2;
            } else if ('fund' === $key) {
                $code = 3;
            }
            return ['code' => $code, 'message' => current($message)];
        }

        return $this->render('wengutou', ['model' => $model, 'product' => $product]);
    }

    public function actionIntroduction($pid)
    {
        if (empty($pid)) {
            throw $this->ex404();
        }

        $model = BookingProduct::findOne($pid);
        if (null === $model) {
            throw $this->ex404();
        }

        $now = time();
        $end_flag = false;
        $count = BookingLog::find()->where(['pid' => $pid, 'uid' => $this->getAuthedUser()->id])->count();
        if ($model->is_disabled || (!empty($model->start_time) && $now < $model->start_time) || (!empty($model->end_time) && $now > $model->end_time)) {
            $end_flag = true;
        }

        return $this->render('introduction', ['model' => $model, 'exist_flag' => $count, 'end_flag' => $end_flag]);
    }
}
