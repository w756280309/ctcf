<?php

namespace common\models\adv;

use common\models\user\User;
use yii\db\ActiveRecord;

class Session extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['userId', 'integer'],
            ['batchSn', 'string'],
            ['createTime', 'string'],
        ];
    }

    /**
     * 获得当前Query对象
     *
     * @param User      $user
     * @param \DateTime $joinTime
     *
     * @return $this
     */
    public static function findByCreateTime(User $user, \DateTime $joinTime)
    {
        return Session::find()
            ->where(['userId' => $user->id])
            ->andWhere(['date(createTime)' => $joinTime->format('Y-m-d')]);
    }

    /**
     * 根据用户和sn新建一个session对象
     *
     * @param User $user
     * @param string $sn
     *
     * @return Session
     */
    public static function initNew(User $user, $sn)
    {
        return new self([
            'userId' => $user->id,
            'batchSn' => $sn,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }
}