<?php
namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\booking\BookingProduct;
use common\models\booking\BookingLog;

class BookingController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->layout = '@app/modules/order/views/layouts/buy';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 客户预约申请
     */
    public function actionBooking($pid)
    {
        $product = BookingProduct::findOne($pid);
        if (empty($product)) {
            throw new \yii\web\NotFoundHttpException('booking production is not existed.');
        }
        $now = time();
        if ($product->is_disabled || $now < $product->start_time || $now > $product->end_time) {
            return $this->redirect('detail?pid='.$pid);
        }

        $model = new BookingLog([
            'uid' => $this->user->id,
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
        $model = BookingProduct::findOne($pid);
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('booking production is not existed.');
        }
        
        $count = BookingLog::find()->where(['pid' => $pid, 'uid' => $this->user->id])->count();
        
        return $this->render('detail', ['model' => $model, 'status' => $count]);
    }

    public function actionProductend()
    {
        return $this->render('productend');
    }
}
