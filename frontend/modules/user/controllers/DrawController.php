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

    public function beforeAction($action)
    {
        if (Yii::$app->controller->action->id == 'tixian') {
            //记录目标url
            Yii::$app->session->set('to_url', '/user/draw/tixian');
        }

        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $this->redirect('/user/userbank/identity');
        }

        return parent::beforeAction($action);
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $this->layout = 'main';

        $user = $this->getAuthedUser();
        $uid = $user->id;

        $user_acount = $user->lendAccount;
        $user_bank = $user->qpay;
        
        if(Yii::$app->request->isPost) {
            $draw = new DrawRecord();
            $draw->uid = $uid;
            if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
                try {
                    $drawres = DrawManager::initDraw($user_acount, $draw->money, Yii::$app->params['drawFee']);
                    $option = array();
                    if (null != Yii::$app->request->get('token')) {
                        $option['app_token'] = Yii::$app->request->get('token');
                    }
                    $next = Yii::$container->get('ump')->initDraw($drawres, 'pc', $option);

                    return ['code' => 0, 'message' => '', 'tourl' => $next];
                } catch (DrawException $ex) {
                    if (DrawException::ERROR_CODE_ENOUGH === $ex->getCode()) {
                        return ['code' => 1, 'message' => '您的账户余额不足,仅可提现'.$ex->getMessage().'元', 'money' => $ex->getMessage()];
                    } else {
                        return ['code' => 1, 'message' => $ex->getMessage()];
                    }
                } catch (\Exception $ex) {
                    $draw->addError('money', $ex->getMessage());
                }
            }

            if ($draw->getErrors()) {
                $message = $draw->firstErrors;

                return ['code' => 1, 'message' => current($message)];
            }
        } else {
            //检查是否开通免密
            $cond = 0 | BankService::MIANMI_VALIDATE;
            $data = BankService::check($this->user, $cond);
            return $this->render('tixian', [
                'user_bank' => $user_bank,
                'user_acount' => $user_acount,
                'data' => $data,
            ]);
        }
    }

    /**
     * 提现返回页面.
     */
    public function actionDrawNotify($flag)
    {
        $this->layout = 'main';
        if (!in_array($flag, ['err', 'succ'])) {
            exit('参数错误');
        }

        return $this->render('drawnotify', ['flag' => $flag]);
    }
}
