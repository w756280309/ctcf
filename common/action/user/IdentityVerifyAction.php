<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午5:42
 */

namespace common\action\user;

use common\models\user\UserIdentity;
use common\service\BankService;
use common\utils\SecurityUtils;
use Ding\DingNotify;
use yii\base\Action;

//实名认证表单提交公共action
class IdentityVerifyAction extends Action
{
    public function run()
    {
        if (\Yii::$app->request->isPost) {
            $user = $this->controller->getAuthedUser();
            $data = BankService::check($user, BankService::IDCARDRZ_VALIDATE_Y);
            if (isset($data['code']) && $data['code'] === 1) {
                return $data;
            }
            $model = new UserIdentity();
            if ($model->load(\Yii::$app->request->post(), 'User') && $model->validate()) {
                try {
                    $user->setIdentity($model);
                    if (CLIENT_TYPE === 'pc') {
                        $toUrl = '/info/success?source=tuoguan';
                    } else {
                        $toUrl = '/user/user/mianmi';
                    }

                    return [
                        'tourl' => $toUrl,
                        'code' => 0,
                        'message' => '您已成功开户'
                    ];
                } catch (\Exception $ex) {
                    (new DingNotify('wdjf'))->sendToUsers('用户[' . SecurityUtils::decrypt($user->safeMobile) . ']，于' . date('Y-m-d H:i:s') . ' 进行开户操作，操作失败，联动开户失败，' . $ex->getMessage());
                    return [
                        'code' => 1,
                        'message' => 1 === $ex->getCode() ? $ex->getMessage() : '系统繁忙，请稍后重试！',
                    ];
                }
            } else {
                if ($model->getErrors()) {
                    return ['code' => 1, 'message' => current($model->firstErrors)];
                }
            }
        }
        return ['code' => 1, 'message' => '开户失败'];
    }
}