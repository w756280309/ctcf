<?php
/**
 * Created by ShiYang.
 * Date: 17-1-10
 * Time: 下午1:26
 */

namespace common\action\user;

use common\service\BankService;
use Yii;
use yii\base\Action;

//免密
class PayAgreementAction extends Action
{
    public function run()
    {
        $from = Yii::$app->request->get('from');
        if (CLIENT_TYPE === 'pc' && $from && filter_var($from, FILTER_VALIDATE_URL)) {
            Yii::$app->session->set('to_url', $from);
        }
        $user = $this->controller->getAuthedUser();
        if (!$user->isIdVerified()) {
            return $this->controller->redirect('/user/identity');
        }
        if ($user->mianmiStatus) {
            return $this->controller->redirect('/user/user');
        }
        return $this->controller->redirect(Yii::$container->get('ump')->openmianmi($this->controller->getAuthedUser()->epayUser->epayUserId, CLIENT_TYPE));
    }
}