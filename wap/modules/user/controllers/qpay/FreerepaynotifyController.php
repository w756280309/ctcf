<?php
namespace app\modules\user\controllers\qpay;

use Yii;
use common\models\user\Borrower;
use common\models\epay\EpayUser;
use common\models\user\UserAccount;
use common\service\BankService;
use common\models\user\User;

use yii\web\Controller;
use common\models\TradeLog;


/**
 * 免密还款回调地址
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class FreerepaynotifyController extends Controller
{
    /**
     * 前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data);
            $ret = BankService::checkKuaijie($user);
            if (1 === (int)$ret['code']) {
                return $this->redirect($ret['tourl']);
            }
        } catch (\Exception $ex) {
        }
        return $this->redirect('/user/user');
    }

    /**
     * 后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data);
            $ret = BankService::checkKuaijie($user);
            if (1 === (int)$ret['code']) {
                $errmsg = $data['message'];
            } else {
                $err = '0000';
            }
        } catch (\Exception $ex) {
            $errmsg = $ex->getMessage();
        }
        $content = Yii::$container->get('ump')->buildQuery([
            'reg_code' => $err,
        ]);
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }


    public static function processing($data)
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'mer_bind_agreement_notify' === $data['service']
        ) {
            $epayUser = EpayUser::findOne(['epayUserId' => $data['user_id']]);
            $sql = "update user set `org_name` = `real_name` where `id` = :uid and `real_name` is not null";
            $userRows = Yii::$app->db->createCommand($sql, [
                'uid' => $epayUser->appUserId,
            ])->execute();
            if(!$userRows){
                $sql = Yii::$app->db->createCommand($sql, [
                    'uid' => $epayUser->appUserId,
                ])->getRawSql();
                Yii::info('sql语句:'.$sql, 'notify');
            }

            $user = User::findOne($epayUser->appUserId);
            $borrowerinfo = Borrower::findOne(['userId'=> $epayUser->appUserId]);
            if(empty($borrowerinfo)){
                $borrower = new Borrower();
                $borrower->userId = $epayUser->appUserId;
                $borrower->allowDisbursement = 1;
                $borrower->type = $user->type;
                if(!$borrower->save()){
                    Yii::info('插入Borrower表'.current($borrower->firstErrors), 'notify');
                }
            }else{
                Yii::info('Borrower表用户'.$epayUser->appUserId.'已存在', 'notify');
            }

            $userAccountinfo = UserAccount::findOne(['uid'=> $epayUser->appUserId, 'type'=>UserAccount::TYPE_LEND]);
            if(empty($userAccountinfo)){
                $userAccount = new UserAccount();
                $userAccount->type = UserAccount::TYPE_LEND;
                $userAccount->uid = $epayUser->appUserId;
                if(!$borrower->save()){
                    Yii::info('插入useraccount表'.current($borrower->firstErrors), 'notify');
                }
            }else{
                Yii::info('useraccount表用户'.$epayUser->appUserId.'已存在', 'notify');
            }
            return $user;
        } else {
            throw new \Exception($data['order_id'] . '处理失败');
        }

    }
}