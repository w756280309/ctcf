<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Response;
use frontend\controllers\BaseController;
use common\models\user\EditpassForm;
use common\models\user\UserAccount;
use common\service\BankService;
use common\models\city\Region;
use common\models\draw\Draw;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;

class UseraccountController extends BaseController
{
    public $layout = '@app/views/layouts/main';

    /**
     * 账户中心展示页.
     */
    public function actionAccountcenter()
    {
        $uid = $this->user->id;
        $check_arr = BankService::checkKuaijie($this->user);

        if ($check_arr['code'] === 1) {
            $errflag = 1;
            $errmess = $check_arr['message'];
        }

        $account = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $uid]);

        return $this->render('accountcenter', ['model' => $account, 'username' => $this->user->real_name, 'errflag' => $errflag, 'errmess' => $errmess]);
    }

    /**
     * 提现页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $user_bank = $this->user->qpay;
        $user_acount = $this->user->lendAccount;
        $province = Region::find()->where(['province_id' => 0])->select('id,name')->asArray()->all();

        $check_arr = BankService::checkKuaijie($user);
        if ($check_arr[code] === 1) {
            return $this->goHome();
        }

        $model = new EditpassForm();
        $draw = new Draw();
        $model->scenario = 'checktradepwd';
        if ($draw->load(Yii::$app->request->post()) && $draw->validate() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                DrawManager::init($user, $draw->money, \Yii::$app->params['drawFee']);

                return $this->redirect('/user/useraccount/tixianback?flag=succ');
            } catch (DrawException $ex) {
                $draw->addError('money', $ex->getMessage());
            }
        }
        return $this->render('tixian', ['model' => $model, 'bank' => $user_bank, 'user_account' => $user_acount, 'draw' => $draw, 'province' => $province]);
    }

    /**
     * 补充银行信息.
     */
    public function actionEditbank()
    {
        $res = false;
        $message = '操作失败';
        $check_arr = BankService::checkKuaijie($this->user);
        if ($check_arr['code'] === 1) {
            $this->goHome();
        }

        $bank = $this->user->bank;
        $bank->scenario = 'step_second';
        if ($bank->load(Yii::$app->request->post()) && $bank->validate()) {
            $res = $bank->save();
            if ($res) {
                $message = '操作成功';
            }
        }

        if ($bank->hasErrors()) {
            $message = current($bank->firstErrors);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['res' => $res, 'message' => $message];
    }

    /**
     * 提现返回页面.
     */
    public function actionTixianback($flag)
    {
        if (!in_array($flag, ['err', 'succ'])) {
            exit('参数错误');
        }

        return $this->render('tixianback', ['flag' => $flag]);
    }

    /**
     * 查询省份对应的城市
     */
    public function actionCity($pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $city = Region::find()->where(['province_id'=>$pid])->select('name')->asArray()->all();

        return ['name' => $city];
    }
}
