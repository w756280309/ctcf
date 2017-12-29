<?php

namespace common\models\adv;

use common\models\media\Media;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "splash".
 */
class Splash extends ActiveRecord
{
    const UNPUBLISHED = 0;     //不发布状态
    const PUBLISHED = 1;       //发布状态
    const AUTO_PUBLISH_ON = 1; //自动发布状态
    const AUTO_PUBLISH_OFF = 0;//取消自动发布状态
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'splash';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            [['img640x960', 'img640x1136', 'img750x1334', 'img1242x2208', 'img1080x1920'], 'image', 'extensions' => 'png, jpg'],
            [
                'img640x960',
                'image',
                'maxHeight' => 960,
                'overHeight' => '{attribute}的高度应为960px',
                'minHeight' => 960,
                'underHeight' => '{attribute}的高度应为960px',
                'maxWidth' => 640,
                'overWidth' => '{attribute}的宽度应为640px',
                'minWidth' => 640,
                'underWidth' => '{attribute}的宽度应为640px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
            ],
            [
                'img640x1136',
                'image',
                'maxHeight' => 1136,
                'overHeight' => '{attribute}的高度应为1136px',
                'minHeight' => 1136,
                'underHeight' => '{attribute}的高度应为1136px',
                'maxWidth' => 640,
                'overWidth' => '{attribute}的宽度应为640px',
                'minWidth' => 640,
                'underWidth' => '{attribute}的宽度应为640px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
            ],
            [
                'img750x1334',
                'image',
                'maxHeight' => 1334,
                'overHeight' => '{attribute}的高度应为1334px',
                'minHeight' => 1334,
                'underHeight' => '{attribute}的高度应为1334px',
                'maxWidth' => 750,
                'overWidth' => '{attribute}的宽度应为750px',
                'minWidth' => 750,
                'underWidth' => '{attribute}的宽度应为750px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
            ],
            [
                'img1242x2208',
                'image',
                'maxHeight' => 2208,
                'overHeight' => '{attribute}的高度应为2208px',
                'minHeight' => 2208,
                'underHeight' => '{attribute}的高度应为2208px',
                'maxWidth' => 1242,
                'overWidth' => '{attribute}的宽度应为1242px',
                'minWidth' => 1242,
                'underWidth' => '{attribute}的宽度应为1242px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
            ],
            [
                'img1080x1920',
                'image',
                'maxHeight' => 1920,
                'overHeight' => '{attribute}的高度应为1920px',
                'minHeight' => 1920,
                'underHeight' => '{attribute}的高度应为1920px',
                'maxWidth' => 1080,
                'overWidth' => '{attribute}的宽度应为1080px',
                'minWidth' => 1080,
                'underWidth' => '{attribute}的宽度应为1080px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
            ],
            ['title', 'string', 'max' => 60],
            ['isPublished', 'boolean'],
            ['publishTime', 'required', 'when' => function($model) {
                return $model->isPublished == 1;
            }, 'whenClient' => "function(attribute, value) {
                return $('#splash-ispublished').attr('checked') == 'checked'
            }"]
            ];
    }
   //通过media_id获取图片的路径
    public function getMediaUri($media_id)
    {
        $media = Media::findOne(['id' => $media_id]);
        return $media['uri'];
    }
    //配置图片名称及属性
    public static function getSplashImages()
    {
        return [
            ['name' => 'img640x960', 'width' => '640', 'height' => '960'],
            ['name' => 'img640x1136', 'width' => '640', 'height' => '1136'],
            ['name' => 'img750x1334', 'width' => '750', 'height' => '1334'],
            ['name' => 'img1080x1920', 'width' => '1080', 'height' => '1920'],
            ['name' => 'img1242x2208', 'width' => '1242', 'height' => '2208'],
        ];
    }
    //获取5张图片的名称
    public static function getSplashImageName()
    {
        $splashImages = self::getSplashImages();
        return ArrayHelper::getColumn($splashImages, 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'img640x960' => '690*960图片',
            'img640x1136' => '640*1136图片',
            'img750x1334' => '750*1334图片',
            'img1080x1920' => '1080*1920图片',
            'img1242x2208' => '1242*2208图片',
            'publishTime' => '发布时间',
            'isPublished' => '是否发布',
            'createTime' => '添加时间',
        ];
    }

    /**
     * 创建sn.
     */
    public static function create_code()
    {
        $sn = time() . bin2hex(random_bytes(2));
        return $sn;
    }
    //初始化
    public static function initNew($adminId)
    {
        return new self([
            'sn' => self::create_code(),
            'isPublished' => self::UNPUBLISHED,
            'creator_id' => $adminId,
            'createTime' => time(),
            'auto_publish' => self::AUTO_PUBLISH_OFF,
        ]);
    }
}
