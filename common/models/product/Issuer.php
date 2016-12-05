<?php

namespace common\models\product;

use common\models\media\Media;
use yii\db\ActiveRecord;

/**
 * 发行方（项目）.
 *
 * @property string $id
 * @property string $name           发行方名称
 * @property string $mediaTitle     视频名称
 * @property int    $video_id       视频对应的mediaID
 * @property int    $videoCover_id  视频示例图对应的mediaID
 */
class Issuer extends ActiveRecord
{
    public $videoUrl;   //存放发行方对应的视频地址
    public $imgUrl;     //存放发行方对应的图片地址

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['name', 'mediaTitle'], 'string'],
            [['name', 'mediaTitle'], 'trim'],
            ['videoUrl', 'url'],
            ['videoUrl', 'match', 'pattern' => '/^[a-zA-Z0-9.:\/_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等'],   //链接可以包含数字,字母,和一些特殊字符,如.:/_-
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'maxHeight' => 420, 'overHeight' => '{attribute}的高度应为420px'],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'minHeight' => 420, 'underHeight' => '{attribute}的高度应为420px'],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'maxWidth' => 750, 'overWidth' => '{attribute}的宽度应为750px'],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'minWidth' => 750, 'underWidth' => '{attribute}的宽度应为750px'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '发行方ID',
            'name' => '发行方名称',
            'mediaTitle' => '视频名称',
            'videoUrl' => '视频地址',
            'imgUrl' => '视频示例图',
        ];
    }

    /**
     * 获取发行商视频信息.
     */
    public function getVideo()
    {
        return $this->hasOne(Media::class, ['id' => 'video_id']);
    }

    /**
     * 获取发行商视频示例图.
     */
    public function getVideoImg()
    {
        return $this->hasOne(Media::class, ['id' => 'videoCover_id']);
    }
}
