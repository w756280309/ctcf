<?php

namespace app\modules\user\controllers\qpay;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use common\models\user\DrawRecord;
use common\models\draw\DrawManager;

/**
 * 提现申请回调控制器4.2
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class DrawnotifyController extends Controller
{
    /**
     * 提现申请前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $ret = $this->processing($data);
            if ($ret instanceof DrawRecord) {
                return $this->redirect('/user/user');
            } else {
               Yii::trace('非DrawRecord对象;'. $data['service'] . ":" . http_build_query($data), 'umplog'); 
            }
        } catch (Exception $ex) {
            Yii::trace($ex->getMessage() .';'. $data['service'] . ":" . http_build_query($data), 'umplog');
        }
    }

    /**
     * 提现申请后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $ret = $this->processing($data);
            if ($ret instanceof DrawRecord) {
                $err = "0000";
            } else {
               $errmsg = '非DrawRecord对象;';
            }
        } catch (Exception $ex) {
            $errmsg = $ex->getMessage() .';';            
        }
        Yii::trace($errmsg . $data['service'] . ":" . http_build_query($data), 'umplog');
        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);
        
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
    
    /**
     * 
     * @param array $data
     * @return type
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function processing(array $data = [])
    {
        Yii::trace('【提现申请返回通知】' . $data['service'] . ":" . http_build_query($data), 'umplog');
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'withdraw_apply_notify' === $data['service']
        ) {
            $draw = DrawRecord::findOne(['sn' => $data['order_id']]);
            return DrawManager::ackDraw($draw);           
        } else {
            throw new Exception($data['order_id'] . '处理失败');
        }
    }

    /**
     * 
     * @param array $data
     */
    public function apply()
    {
        $data = Yii::$app->request->get();
        Yii::trace('【提现申请成功后】' . $data['service'] . ":" . http_build_query($data), 'umplog');
    }

}
