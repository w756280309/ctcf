<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-6
 * Time: 下午5:58
 */
namespace common\models\wechat;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Reply extends ActiveRecord
{
    public $media;  //资源
    public static function tableName() {
        return 'wechat_reply';
    }

    public function rules()
    {
        return [
            [['keyword', 'type'], 'required'],
            ['keyword', 'unique', 'message'=>'关键字已占用'],
            [['isDel', 'content'], 'safe'],
        ];
    }

    public function behaviors() {
        return [
            new TimestampBehavior([
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ]),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'keyword' => '关键字',
            'content' => '回复内容',
            'isDel' => '是否删除',
            'updatedAt' => '更新时间',
            'createdAt' => '创建时间',
        ];
    }

}