<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午2:14
 */

namespace app\modules\user\controllers;


use app\controllers\BaseController;
use common\action\user\IdentityVerifyAction;
use common\service\BankService;

class IdentityController extends BaseController
{
    public function actions()
    {
        return[
            'verify' => IdentityVerifyAction::className(),
        ];
    }

    //为开户的开户页面
    public function actionIndex()
    {
        $data = BankService::check($this->getAuthedUser(), BankService::IDCARDRZ_VALIDATE_Y);
        return $this->render('index', $data);
    }
}