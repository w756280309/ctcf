<?php

namespace frontend\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\service\BankService;
use common\models\user\DrawRecord;
use common\models\draw\DrawException;
use common\models\draw\DrawManager;

class DrawController extends BaseController
{
    public $layout = '@app/views/layouts/footer';

    public function beforeAction($action)
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;

        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $this->redirect('/user/useraccount/accountcenter');
        }

        return parent::beforeAction($action);
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $uid = $user->id;

        $user_acount = $user->lendAccount;
        $user_bank = $user->qpay;

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            try {
                $drawres = DrawManager::initDraw($user_acount, $draw->money, \Yii::$app->params['drawFee']);
                $next = Yii::$container->get('ump')->initDraw($drawres, 'pc');
                return $this->redirect($next);
            } catch (DrawException $ex) {
                if (DrawException::ERROR_CODE_ENOUGH === $ex->getCode()) {
                    $draw->addError('money', '您的账户余额不足,仅可提现' . $ex->getMessage() . '元');
                } else {
                    $draw->addError('money', $ex->getMessage());
                }
            } catch (\Exception $ex) {
                $draw->addError('money', $ex->getMessage());
            }
        }

        return $this->render('tixian', ['bank' => $user_bank, 'user_account' => $user_acount, 'draw' => $draw]);
    }

    /**
     * 提现返回页面.
     */
    public function actionDrawNotify($flag)
    {
        if (!in_array($flag, ['err', 'succ'])) {
            exit('参数错误');
        }

        return $this->render('drawnotify', ['flag' => $flag]);
    }
}
