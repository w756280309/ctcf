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
            [['keyword', 'type', 'style'], 'required'],
            ['keyword', 'unique', 'message'=>'关键字已占用'],
            [['isDel', 'content'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return [
            'auto_reply' => ['keyword', 'type', 'isDel', 'content', 'style', 'createdAt'],
            'whole_message' => ['content', 'isDel', 'style', 'createdAt']
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
            'style' => '消息类型',
        ];
    }
    //回复类型
    public static function types()
    {
        return [
            'text' => '文本',
            'image' => '图片',
            'layout' => '模板消息',
        ];
    }
    //消息类型
    public static function styles()
    {
        return [
            'whole_message' => '全体消息',
            'auto_reply' => '自动回复',
        ];
    }

}