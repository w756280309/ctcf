<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午4:39
 */

namespace frontend\modules\user\controllers;


use common\action\user\IdentityVerifyAction;
use common\service\BankService;
use frontend\controllers\BaseController;

class IdentityController extends BaseController
{
    public function actions()
    {
        return[
            'verify' => IdentityVerifyAction::className(),
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'main';
        $user = $this->getAuthedUser();
        $data = BankService::check($user, BankService::IDCARDRZ_VALIDATE_Y);
        if ($data['code'] === 1) {
            return $this->redirect('/user/user/index');
        }
        return $this->render('index', $data);
    }
}