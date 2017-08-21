<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午5:42
 */

namespace common\action\user;

use common\models\user\OpenAccount;
use common\models\user\UserIdentity;
use common\service\BankService;
use console\command\OpenAccountJob;
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
                $openAccountRecord = OpenAccount::initNew($user, $model);
                $openAccountRecord->ip = ip2long(\Yii::$app->request->getUserIP());
                $openAccountRecord->save(false);
                $job = new OpenAccountJob([
                    'openAccountRecordId' => $openAccountRecord->id,
                ]);
                \Yii::$container->get('db_queue')->pub($job, 5);

                return ['code' => 0, 'message' => '正在进行开户', 'id' => $openAccountRecord->id];
            } else {
                if ($model->getErrors()) {
                    return ['code' => 1, 'message' => current($model->firstErrors)];
                }
            }
        }
        return ['code' => 1, 'message' => '开户失败'];
    }
}