<?php

namespace frontend\modules\user\controllers;

use common\models\user\EditpassForm;
use common\service\BankService;
use frontend\controllers\BaseController;
use yii\filters\AccessControl;
use Yii;

class SecuritycenterController extends BaseController
{
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

    public function actionIndex()
    {
        $this->layout = 'main';

        $user = $this->getAuthedUser();
        $model = new EditpassForm();

        return $this->render('index', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    public function actionResetUmpPass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if (1 === $data['code']) {
            return $data;
        }

        $ump = Yii::$container->get('ump');

        $resp = $ump->resetTradePass($this->getAuthedUser());

        if ($resp->isSuccessful()) {
            return ['code' => 0, 'message' => '重置后的密码已经发送到您的手机'];
        } else {
            return ['code' => 1, 'message' => '当前网络异常，请稍后重试'];
        }
    }

    public function actionResetPass()
    {
        $model = new EditpassForm();
        $model->scenario = 'edituserpass';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->edituserpass()) {
                \Yii::$app->user->logout();

                return ['code' => 0, 'tourl' => '/site/login'];
            }
        }

        if ($model->hasErrors()) {
            return ['code' => 1, 'error' => $model->firstErrors];
        }
    }
}
