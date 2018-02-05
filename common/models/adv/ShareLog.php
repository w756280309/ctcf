<?php

namespace common\models\adv;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "share_log".
 *
 * @property integer $id            ID
 *  @propertyinteger $uid           分享的用户id
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
}
