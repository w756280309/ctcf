<?php

namespace common\models\invite;

use common\models\user\User;

/**
 * 邀请好友.
 */
class Invite extends \yii\db\ActiveRecord
{
    /**
     * 定义表名.
     */
    public static function tableName()
    {
        return 'Invite';
    }

    public function rules()
    {
        return [
            [['uid', 'code'], 'required'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public static function initNew(User $user)
    {
        return new self([
            'uid' => $user->getId(),
            'code' => \Yii::$app->security->generateRandomString(10), //单元测试使用报错
        ]);
    }
}
