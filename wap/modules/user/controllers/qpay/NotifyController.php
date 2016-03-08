<?php

namespace app\modules\user\controllers\qpay;

use Yii;
use yii\web\Controller;
use common\models\user\QpayBinding;
use common\models\user\UserBanks;
use yii\helpers\ArrayHelper;
use common\models\TradeLog;

/**
 * 绑卡回调控制器4.2
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class NotifyController extends Controller
{
    /**
     * 绑卡前台通知地址【绑卡申请的通知地址】
     * 允许访问如下地址访问：
     *   ?sign=oKvSXTpQXzRTA7a62ORXunGDrkl%2F5RTTMbwPz6LMiV6QgIBy1hxe2W%2BZTkSe4IpdhiQ8WGPbX7AYQ8fMaomp8qAzCy%2FGtAUi1YBSxTuBJpw%2FRLdnpJTtwFgqju%2FQstQ%2BZo54bgaVJUrmS2z7dXnVke%2Bg2yPARq7dBRGAZBJV1go%3D&ret_code=0000&mer_date=20160216&mer_id=7050209&sign_type=RSA&ret_msg=%E7%BB%91%E5%8D%A1%E5%8F%97%E7%90%86%E6%88%90%E5%8A%9F&service=mer_bind_card_apply_notify&charset=UTF-8&user_id=UB201602161353010000000000043914&order_id=B1602161649239508021864&version=4.0
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (Yii::$container->get('ump')->verifySign($data) && '0000' === $data['ret_code']) {
            $bind = QpayBinding::findOne(['binding_sn' => $data['order_id'], 'status' => QpayBinding::STATUS_INIT]);
            if (null !== $bind) {
                $bind->status = QpayBinding::STATUS_ACK;//处理中
                if ($bind->save(false)) {
                    return $this->redirect('/user/userbank/accept?ret=success');
                } else {
                    return $this->redirect('/user/userbank/accept');
                }
            } else {
                throw new \yii\web\NotFoundHttpException($data['order_id'] . ':无法找到申请数据');
            }
        } else {
            return $this->redirect('/user/userbank/accept');
        }
    }

    /**
     * 绑卡后台通知地址【绑卡结果通知地址】
     * 后台通知地址会接收到绑卡申请的后台通知以及绑卡结果的后台通知，需要判断返回结果的服务类型必须是mer_bind_card_notify【4.2.6】
     * 允许访问如下地址访问：
     *   ?charset=UTF-8&gate_id=ICBC&last_four_cardid=6783&mer_date=20160217&mer_id=7050209&order_id=B1602170904368901397519&ret_code=0000&service=mer_bind_card_notify&user_bind_agreement_list=ZKJP0700%2C0000&user_id=UB201602170902110000000000043969&version=4.0&sign=EebPL%2FbQNP8%2FRy8JXgXHQQ74BXAD4IWPx7A9gUDIRXuwtfVE6tYYBiNe1%2FphInWaF7GVoUUP6rk9GzyvuzHBaoaioFb2tnzJnCE7yJvVgHP74VXjg3bmuy%2FRa46qDiEYbuulm9F%2FqUh4WB%2BHpJ74j2fT%2F5hV4wkEKbxiX1Q%2BjXg%3D&sign_type=RSA
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();

        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && 'mer_bind_card_notify' === $data['service']
        ) {
            $bind = QpayBinding::findOne(['binding_sn' => $data['order_id']]);
            if (null !== $bind) {
                if ('0000' === $data['ret_code']) {
                    if (null === UserBanks::findOne(['binding_sn' => $data['order_id']])) {
                        if (true === self::processing($bind)) {
                            $err = '0000';
                        } else {
                            $errmsg = "数据修改失败";
                        }
                    }
                } else {
                    $bind->status = QpayBinding::STATUS_FAIL;
                    $bind->save(false);
                    $err = '0000';
                }
                
                $content = Yii::$container->get('ump')->buildQuery([
                    'order_id' => $data['order_id'],
                    'mer_date' => $data['mer_date'],
                    'reg_code' => $err,
                ]);

                return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
            } else {
                throw new \Exception('无法找到记录');
            }
        }
        
    }

    public static function processing(QpayBinding $bind)
    {
        if(QpayBinding::STATUS_SUCCESS === (int)$bind->status || QpayBinding::STATUS_FAIL === (int)$bind->status) {
            return true;
        }
        
        if (null === UserBanks::findOne(['binding_sn' => $bind->binding_sn])) {
            $bind->status = QpayBinding::STATUS_SUCCESS;
            $data = ArrayHelper::toArray($bind);
            unset($data['id']);
            unset($data['status']);
            $userBanks = new UserBanks($data);
            $userBanks->setScenario('step_first');
            $transaction = Yii::$app->db->beginTransaction();
            if ($userBanks->save() && $bind->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return false;
            }
        }
    }
}
