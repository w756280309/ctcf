<?php

namespace common\models\product;

use yii\db\ActiveRecord;

/**
 * Class JxPage
 * @package common\models\product
 *
 * @property integer      $id         主键
 * @property integer      $issuerId   发行方ID
 * @property string       $title      页面标题
 * @property string       $content    页面内容
 * @property null|string  $createTime 创建时间
 * @property null|integer $admin_id   后台登陆ID
 */
class JxPage extends ActiveRecord
{
    public function rules()
    {
        return [
            ['issuerId', 'unique'],
            ['title', 'string', 'max' => 13],
            [['issuerId', 'title', 'content'], 'required'],
            [['issuerId', 'admin_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'issuerId' => '发行方ID',
            'title' => '页面标题',
            'content' => '页面内容',
            'createTime' => '创建时间',
            'admin_id' => 'AdminID',
        ];
    }

    public function getIssuer()
    {
        return $this->hasOne(Issuer::class, ['id' => 'issuerId']);
    }
}
