<?php

namespace common\models\growth;

use yii\helpers\Html;
use yii\db\ActiveRecord;

class PageMeta extends ActiveRecord
{
    public function rules()
    {
        return [
            [['alias', 'url', 'title', 'keywords', 'description'], 'required'],
            [['alias', 'url', 'title', 'keywords', 'description', 'href'], 'string'],
            [['alias', 'url', 'title', 'keywords', 'description'], 'trim'],
            ['url', 'url'],
            ['url', 'unique', 'message' => '链接地址应唯一'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => '别名',
            'url' => '链接地址',
            'title' => '页面标题',
            'keywords' => '关键词',
            'description' => '描述',
        ];
    }


    /**
     * 根据url获得Meta信息
     *
     * @param $url
     *
     * @return null|static
     */
    public static function getMeta($url)
    {
        $url = trim(Html::encode($url), '?');
        $url = trim($url, '\/');
        if (false !== strpos($url, '?')) {
            $urlArr = explode('?', $url);
            $url = $urlArr[0];
        }
        return PageMeta::findOne(['url' => $url]);
    }
}
