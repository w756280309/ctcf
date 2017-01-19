<?php

namespace common\models\growth;

use yii\db\ActiveRecord;
use yii\web\Request;

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
     * @param  Request     $request Request对象
     *
     * @return null|static
     */
    public static function getMeta(Request $request)
    {
        $url = $request->absoluteUrl;
        $url = trim(trim($url, '?'), '\/');
        //先尝试全部匹配是否能找到meta信息
        if (null !== ($pageMeta = PageMeta::findOne(['url' => $url]))) {
            return $pageMeta;
        }
        //去掉query后是否能找到meta信息
        $relativeUrl = trim(trim($request->hostInfo . '/' . $request->getPathInfo(), '?'), '\/');

        return PageMeta::findOne(['url' => $relativeUrl]);
    }
}
