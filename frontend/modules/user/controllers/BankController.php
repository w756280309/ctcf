<?php
/**
 * Created by ShiYang.
 * Date: 17-1-6
 * Time: 下午2:19
 */

namespace frontend\modules\user\controllers;


use common\action\user\BankCheckAction;
use common\action\user\BankUpdateAction;
use common\action\user\BankUpdateVerifyAction;
use common\action\user\BankVerifyAction;
use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\models\user\QpayBinding;
use common\service\BankService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;

class BankController extends BaseController
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
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

    public function actions()
    {
        return [
            'check' => BankCheckAction::className(),//根据卡号匹配开户行
            'verify' => BankVerifyAction::className(),//绑卡表单提交页面
            'update' => BankUpdateAction::className(),//换卡页面
            'update-verify' => BankUpdateVerifyAction::className(),//换卡表单提交页面
        ];
    }

    //绑卡页面
    public function actionIndex()
    {
        //检查是否已绑卡
        $cond = 0 | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->redirect('/user/bank/card');
        }

        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            Yii::$app->session->set('to_url', '/user/bank/card');

            return $this->render('@frontend/modules/user/views/userbank/identity.php', [
                'title' => '绑定银行卡',
            ]);
        }

        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE_N;
        $data = BankService::check($this->user, $cond);

        $banks = BankManager::getQpayBindBanks();

        return $this->render('index', [
            'banklist' => $banks,
            'data' => $data,
        ]);
    }

    //我的银行卡页面
    public function actionCard()
    {
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->render('@frontend/modules/user/views/userbank/identity.php', [
                'title' => '我的银行卡',
            ]);
        }

        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE_N;
        $data = BankService::check($this->user, $cond);

        $user = $this->getAuthedUser();
        $user_bank = $user->qpay;
        $binding = null;
        $bankcardUpdate = null;

        if ($user_bank) {
            $bankcardUpdate = BankCardUpdate::find()
                ->where(['oldSn' => $user_bank->binding_sn, 'uid' => $user->id])
                ->orderBy('id desc')->one();
        } else {
            $binding = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);
        }

        return $this->render('card', [
            'user_bank' => $user_bank,
            'data' => $data,
            'binding' => $binding,
            'bankcardUpdate' => $bankcardUpdate,
        ]);
    }
}