<?php

namespace app\modules\user\controllers\qpay;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use common\models\user\RechargeRecord;
use common\service\AccountService;

/**
 * 绑卡回调控制器4.2
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class QpaynotifyController extends Controller
{
    /**
     * 快捷充值前台通知地址
     */
    public function actionFrontend()
    {        
        $data = Yii::$app->request->get();
        try {
            $ret = $this->processing($data);
            if ($ret instanceof RechargeRecord) {
                return $this->redirect('/user/user');
            } else {
               Yii::trace('非recharge对象;'. $data['service'] . ":" . http_build_query($data), 'umplog'); 
            }
        } catch (Exception $ex) {
            Yii::trace($ex->getMessage() .';'. $data['service'] . ":" . http_build_query($data), 'umplog');
        }
        return $this->redirect('/user/userbank/qpayres');
    }

    /**
     * 快捷充值后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $ret = $this->processing($data);
            if ($ret instanceof RechargeRecord) {
                $err = "0000";
            } else {
               $errmsg = '非recharge对象;';
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
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'recharge_notify' === $data['service']
        ) {
            $rc = RechargeRecord::findOne(['sn' => $data['order_id']]);
            if (null !== $rc) {
                $acc_ser = new AccountService();
                $is_success = $acc_ser->confirmRecharge($rc);                
                if ($is_success) {
                    return $rc;
                } else {
                    throw new Exception($data['order_id'] . '充值失败');
                }
            } else {
                throw new NotFoundHttpException($data['order_id'] . ':无法找到申请数据');
            }            
        } else {
            throw new Exception($data['order_id'] . '处理失败');
        }
    }


}
