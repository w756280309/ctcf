<?php
namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\booking\BookingProduct;
use common\models\booking\BookingLog;

class BookingController extends BaseController
{

    /**
     * 客户预约申请
     */
    public function actionBooking($pid)
    {
        if (empty($pid)) {   //参数无效时,抛出404异常
            throw new \yii\web\NotFoundHttpException();
        }

        $product = BookingProduct::findOne($pid);
        if (null === $product) {
            throw new \yii\web\NotFoundHttpException('booking production is not existed.');
        }

        $now = time();
        $count = BookingLog::find()->where(['pid' => $pid, 'uid' => $this->getAuthedUser()->id])->count();
        if ($product->is_disabled || (!empty($product->start_time) && $now < $product->start_time) || (!empty($product->end_time) && $now > $product->end_time) || $count) {
            return $this->redirect('detail?pid='.$pid);
        }

        $model = new BookingLog([
            'uid' => $this->getAuthedUser()->id,
            'pid' => $pid,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->fund < $product->min_fund) {
                return ['code' => 1, 'message' => '您预约的金额小于项目的起投金额'];
            }
            if ($model->fund > $product->total_fund) {
                return ['code' => 1, 'message' => '您预约的金额大于项目的总金额'];
            }
            if (!$model->save()) {
                return ['code' => 1, 'message' => '预约申请失败'];
            }
            return ['tourl' => '/', 'code' => 1, 'message' => '预约申请成功'];
        }

        if ($model->hasErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('booking', ['model' => $model, 'product' => $product]);
    }

    public function actionDetail($pid)
    {
        if (empty($pid)) {
            throw new \yii\web\NotFoundHttpException();   //参数无效时,抛出404异常
        }

        $model = BookingProduct::findOne($pid);
        if (null === $model) {
            throw new \yii\web\NotFoundHttpException('booking production is not existed.');
        }

        $now = time();
        $end_flag = false;
        $count = BookingLog::find()->where(['pid' => $pid, 'uid' => $this->getAuthedUser()->id])->count();
        if ($model->is_disabled || (!empty($model->start_time) && $now < $model->start_time) || (!empty($model->end_time) && $now > $model->end_time)) {
            $end_flag = true;
        }

        return $this->render('detail', ['model' => $model, 'exist_flag' => $count, 'end_flag' => $end_flag]);
    }

    public function actionProductend()
    {
        return $this->render('productend');
    }

    /**
     * 预约须知页面
     */
    public function actionBookingNotes($pid)
    {
        if (empty($pid) || 1 !== (int)$pid) {
            throw new \yii\web\NotFoundHttpException();
        }
        return $this->render('booking_notes', ['pid' => $pid]);
    }
}