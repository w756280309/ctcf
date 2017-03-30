<?php

namespace console\controllers;

use common\models\promo\InviteRecord;
use common\models\user\User;
use common\models\user\UserInfo;
use yii\console\Controller;
use Yii;

class UserInfoController extends Controller
{
    public function actionInit()
    {
        $db = Yii::$app->db;
        $userCount = User::find()->count();
        $userInfoCount = User::find()->count();

        if ($userInfoCount !== $userCount) {
            $userInfo = UserInfo::find()->select('user_id')->column();
            $users = User::find()->select('id')->column();
            //待新增初始化的UserInfo
            $userIds = array_diff($users, $userInfo);
            $queryUserIds = [];
            foreach ($userIds as $k => $userId) {
                $queryUserIds[$k] = [$userId];
            }
            $num = $db->createCommand()->batchInsert('user_info', ['user_id'], $queryUserIds)->execute();
            if ($num <= 0) {
                $this->stdout('初始化UserInfo失败');
                return self::EXIT_CODE_ERROR;
            }
        }

        //获得所有的被邀请的用户ID集合
        $invitedUsers = InviteRecord::find()
            ->select('invitee_id')
            ->distinct()
            ->column();
        $usersIsAffiliator = UserInfo::find()->select('user_id')->where(['isAffiliator' => true])->column();

        if (!empty(array_diff($invitedUsers, $usersIsAffiliator))) {
            //更新isAffiliator字段为true
            $num = $db->createCommand()->update('user_info', ['isAffiliator' => true], ['in', 'user_id', $invitedUsers])->execute();
            if ($num <= 0) {
                $this->stdout('更新失败');
                return self::EXIT_CODE_ERROR;
            }
        }

        $this->stdout('UserInfo被邀请人初始化完毕');
        return self::EXIT_CODE_NORMAL;
    }
}
