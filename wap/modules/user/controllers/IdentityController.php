<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午2:14
 */

namespace app\modules\user\controllers;


use app\controllers\BaseController;
use common\action\user\IdentityResultAction;
use common\action\user\IdentityVerifyAction;
use common\models\user\OpenAccount;
use common\service\BankService;

class IdentityController extends BaseController
{
    public function actions()
    {
        return[
            'verify' => IdentityVerifyAction::className(),//开户表单提交页面
            'res' => IdentityResultAction::className(),//开户结果查询
        ];
    }

    /**
     * 开户页面.
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/fe';
        $data = BankService::check($this->getAuthedUser(), BankService::IDCARDRZ_VALIDATE_Y);
        $lastOpenAccountRecord = OpenAccount::find()->where([
            'status' => OpenAccount::STATUS_INIT,
            'user_id' => \Yii::$app->getUser()->getIdentity()->getId(),
        ])->orderBy(['id' => SORT_DESC])->one();

        $data['lastRecord'] = $lastOpenAccountRecord;
        return $this->render('index', $data);
    }
}