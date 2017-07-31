<?php

namespace common\models\adv;

use common\models\media\Media;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "adv".
 */
class Adv extends ActiveRecord
{
    public $canShare = false;
    public $imageUri = null;

    //0显示，1隐藏
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;

    //0正常，1删除
    const DEL_STATUS_SHOW = 0;
    const DEL_STATUS_DEL = 1;

    const TYPE_LUNBO = 0;
    const TYPE_KAIPING = 1;

    const UPLOAD_PATH = '/upload/adv/';

    public static function getStatusList()
    {
        return array(
            self::STATUS_SHOW => '显示',
            self::STATUS_HIDDEN => '隐藏',
        );
    }

    public static function getDelStatusList()
    {
        return array(
            self::DEL_STATUS_SHOW => '正常',
            self::DEL_STATUS_DEL => '删除',
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['imageUri', 'image', 'extensions' => 'png, jpg'],
            [
                'imageUri',
                'image',
                'maxHeight' => 800,
                'overHeight' => '{attribute}的高度应为800px',
                'minHeight' => 800,
                'underHeight' => '{attribute}的高度应为800px',
                'maxWidth' => 600,
                'overWidth' => '{attribute}的宽度应为600px',
                'minWidth' => 600,
                'underWidth' => '{attribute}的宽度应为600px',
                'maxSize' => 1048576,
                'tooBig' => '图片大小不能超过1M',
                'whenClient' => "function (attribute, value) {
                    return '1' === $('#advType').val();
                }",
            ],
            [
                'imageUri',
                'image',
                'maxHeight' => 340,
                'overHeight' => '{attribute}的高度应为340px',
                'minHeight' => 340,
                'underHeight' => '{attribute}的高度应为340px',
                'maxWidth' => 1920,
                'overWidth' => '{attribute}的宽度应为1920px',
                'minWidth' => 1920,
                'underWidth' => '{attribute}的宽度应为1920px',
                'maxSize' => 2097152,
                'tooBig' => '图片大小不能超过2M',
                'whenClient' => "function (attribute, value) {
                    return '0' === $('#advType').val() && '1' === $('#showOnPc').val();
                }",
            ],
            [
                'imageUri',
                'image',
                'maxHeight' => 350,
                'overHeight' => '{attribute}的高度应为350px',
                'minHeight' => 350,
                'underHeight' => '{attribute}的高度应为350px',
                'maxWidth' => 750,
                'overWidth' => '{attribute}的宽度应为750px',
                'minWidth' => 750,
                'underWidth' => '{attribute}的宽度应为750px',
                'maxSize' => 204800,
                'tooBig' => '图片大小不能超过200k',
                'whenClient' => "function (attribute, value) {
                    return '0' === $('#advType').val() && '0' === $('#showOnPc').val();
                }",
            ],
            [['link', 'description'], 'string'],
            ['link', 'match', 'pattern' => '/^[a-zA-Z0-9.:\/?%&=_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等'],
            [['show_order', 'isDisabledInApp', 'showOnPc'], 'integer'],
            ['show_order', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => '{attribute}不能为负数'],
            ['title', 'string', 'max' => 15],
            ['share_id', 'integer'],
            ['canShare', 'boolean'],
            [['start_date', 'timing'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'description' => '描述',
            'imageUri' => '图片',
            'status' => '',
            'isDisabledInApp' => '不在APP上显示',
            'showOnPc' => '在PC端显示',
            'link' => '链接',
            'del_status' => '是否删除',
            'creator_id' => '创建者管理员id',
            'updated_at' => '更新时间',
            'created_at' => '添加时间',
            'canShare'  => '页面可分享',
            'show_order'  => '显示顺序',
            'start_date' => '开始时间',
            'timing' => '定时',
        ];
    }

    /**
     * 创建sn.
     */
    public static function create_code($pre = 'SYLB')
    {
        $last = self::find()->select('sn')->orderBy('id desc')->one();
        $sn = $pre;
        if (!$last || empty($last['sn'])) {
            //没有编号的
            $sn .= '0001';
        } else {
            $step = \Yii::$app->functions->autoInc(substr($last['sn'], 4, 4));
            if (9999 < (int) $step) {
                $step = '0001';
            }

            $sn .= $step;
        }

        return $sn;
    }

    public function beforeSave($insert)
    {
        $host = parse_url($this->link, PHP_URL_HOST);
        if ($host) {
            $baseDomain = \Yii::$app->params['base_domain'];
            if (false !== strpos($host, 'm.' . $baseDomain)) {
                $target = 'm.' . $baseDomain;
            } elseif (false !== strpos($host, 'app.' . $baseDomain)) {
                $target = 'app.' . $baseDomain;
            }
            if (isset($target)) {
                $pos = strpos($this->link, $target);
                if (false !== $pos) {
                    $this->link = '/' . ltrim(substr($this->link, $pos + strlen($target)), '/');
                }
            }
        }
        return true;
    }

    public function getShare()
    {
        return $this->hasOne(Share::className(), ['id' => 'share_id']);
    }

    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }

    //获取banner图链接
    public function getLinkUrl()
    {
        $link = $this->link;
        $parseUrl = parse_url($link);
        $shareKey = $this->share ? $this->share->shareKey : '';
        $token = \Yii::$app->request->get('token');
        $queryData = isset($parseUrl['query']) ? explode('&', $parseUrl['query']) : [];
        $params = [];
        foreach ($queryData as $item) {
            list($key, $value) = explode('=', $item);
            $params[$key] = $value;
        }
        if ($shareKey) {
            $params['wx_share_key'] = $shareKey;
        }
        if ($token && defined('IN_APP')) {
            $params['token'] = $token;
        }

        $link = '';
        if (isset($parseUrl['scheme'])) {
            $link .= $parseUrl['scheme'] . '://';
        }
        if (isset($parseUrl['host'])) {
            $link .= $parseUrl['host'];
        }

        return trim($link.$parseUrl['path'].'?'.http_build_query($params), '?');
    }

    public static function initNew($adminId, $type)
    {
        return new self([
            'sn' => self::create_code(),
            'type' => $type,
            'creator_id' => $adminId,
            'status' => self::STATUS_HIDDEN,
            'del_status' => self::DEL_STATUS_SHOW,
            'showOnPc' => false,
            'created_at' => time(),
        ]);
    }

    /*
     * 首页轮播图
     */
    public function fetchHomeBanners($is_m = false)
    {

        $now = date('Y-m-d H:i:s');
        $where = "status = 0 and del_status = 0
                and (timing = 0 or (timing = 1 and start_date <= '$now'))";
        if ($is_m) {
            if (defined('IN_APP')) {
                $where .= " and isDisabledInApp = 1";
            }
            $where .= " and showOnPc = 0";
        } else {
            $where .= " and showOnPc = 1";
        }

        return Adv::find()
            ->where("$where")
            ->orderBy(['show_order' => SORT_ASC, 'id' => SORT_DESC])
            ->limit(5)
            ->all();

    }
}
