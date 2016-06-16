<?php

namespace frontend\modules\user\controllers;


use common\service\BankService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;

class UserbankController extends BaseController
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

    /**
     * 实名认证表单页.
     */
    public function actionIdcardrz()
    {
        $this->layout = 'main';
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_Y;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isPost) {
                return $data;
            } else {
                return $this->redirect('/user/user/index');
            }
        }
        if (Yii::$app->request->isPost) {
            $model = $this->getAuthedUser();
            $model->scenario = 'idcardrz';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $umpService = new \common\service\UmpService();
                try {
                    $umpService->register($model);
                    return ['tourl' => '/user/userbank/rzres?ret=success', 'code' => 0, 'message' => '您已成功开户'];
                } catch (\Exception $ex) {
                    return ['code' => 1, 'message' => $ex->getMessage()];
                }
            } else {
                $err = $model->getSingleError();
                return ['code' => 1, 'message' => $err['message']];
            }
        } else {
            return $this->render('idcardrz', []);
        }
    }

}