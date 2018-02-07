<?php

namespace common\models\adv;

use common\models\user\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "share_log".
 *
 * @property integer $id            ID
 * @property integer $uid           分享的用户id
 * @property string  $shareUrl      分享url
 * @property string  $scene         分享场景  session/聊天  timeline/朋友圈
 * @property string  $createdAt     分享日期
 * @property string  $ipAddress     分享用户的ip地址
 */
class ShareLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'share_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid','shareUrl','scene'], 'required'],
            [['createdAt'], 'integer'],
            [['shareUrl'], 'string', 'max' => 255],
            [['scene'], 'string', 'max' => 10],
            [['ipAddress'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'scene' => '分享场景',
            'shareUrl' => '分享的url',
            'ipAddress' => 'ip地址',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 根据配置获得一条分享记录
     *
     * @param User $user 用户对象
     * @param string $scene 场景
     * @param string $urlLike shareUrl查询匹配条件
     * @param null|\DateTime $shareLogTime
     *
     * @return null|ActiveRecord
     */
    public static function fetchByConfig(User $user, $scene, $urlLike, $shareLogTime = null)
    {
        if (null === $shareLogTime) {
            $shareLogTime = new \DateTime();
        }

        return ShareLog::find()
            ->where(['userId' => $user->id])
            ->andWhere(['scene' => $scene])
            ->andWhere(['like', 'shareUrl', $urlLike])
            ->andWhere(['createdAt' => $shareLogTime->format('Y-m-d')])
            ->one();
    }
}
