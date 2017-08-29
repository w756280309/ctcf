<?php

namespace common\action\user;


use common\models\user\OpenAccount;
use common\models\user\User;
use yii\base\Action;

class IdentityResultAction extends Action
{
    public function run()
    {
        /**
         * @var User $user
         * @var OpenAccount $model
         */
        $id = \Yii::$app->request->get('id');
        $user = \Yii::$app->user->getIdentity();
        if (!is_null($user) && !empty($id) && \Yii::$app->request->isAjax) {
            $model = OpenAccount::find()->where(['user_id' => $user->id, 'id' => $id])->one();
            if (is_null($model)) {
                return ['code' => 1, 'message' => '', 'toUrl'=> ''];
            }
            if ($model->status === OpenAccount::STATUS_SUCCESS) {
                if (CLIENT_TYPE === 'pc') {
                    $toUrl = '/info/success?source=tuoguan';
                } else {
                    $toUrl = '/user/user/mianmi';
                }
                return ['code' => 0, 'message' => '您已成功开户', 'tourl' => $toUrl];
            } elseif ($model->status === OpenAccount::STATUS_FAIL) {
                if (in_array($model->code, ['00060022'])) {
                    $message = $model->message;
                } else {
                    $message = '';
                }
                return ['code' => 2, 'message' => $message];
            }
        }
        return ['code' => 1, 'message' => '', 'toUrl'=> ''];
    }
}