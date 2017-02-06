<?php

namespace common\models\adv;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "adv_pos".
 */
class Adv extends ActiveRecord
{
    public $canShare = false;

    //0显示，1隐藏
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;

    //0正常，1删除
    const DEL_STATUS_SHOW = 0;
    const DEL_STATUS_DEL = 1;

    const TYPE_LUNBO = 0;
    const TYPE_KAIPING = 1;

    //1轮播 2首页开屏
    const POS_ID_LUNBO = 1;
    const POS_ID_KAIPING = 2;

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

    public function scenarios()
    {
        return [
            'update' => ['id', 'sn', 'title', 'pos_id', 'image', 'show_order', 'link', 'description', 'del_status', 'isDisabledInApp', 'showOnPc', 'canShare'],
            'create' => ['pos_id', 'sn', 'title', 'image', 'show_order', 'link', 'description', 'del_status', 'isDisabledInApp', 'showOnPc', 'canShare'],
            'kaiping' => ['id', 'sn', 'title', 'pos_id', 'image', 'show_order', 'status', 'link', 'description', 'del_status', 'isDisabledInApp', 'showOnPc', 'canShare'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['showOnPc', 'default', 'value' => 0],
            [['image', 'description'], 'required', 'on' => ['create', 'update']],
            ['status', 'default', 'value' => self::STATUS_SHOW, 'on' => ['create']],
            ['del_status', 'default', 'value' => self::DEL_STATUS_SHOW, 'on' => ['create']],
            ['image', 'image', 'skipOnEmpty' => true, 'maxHeight' => 800, 'overHeight' => '{attribute}的高度应为800px', 'on' => ['kaiping']],
            ['image', 'image', 'skipOnEmpty' => true, 'minHeight' => 800, 'underHeight' => '{attribute}的高度应为800px', 'on' => ['kaiping']],
            ['image', 'image', 'skipOnEmpty' => true, 'maxWidth' => 600, 'overWidth' => '{attribute}的宽度应为600px', 'on' => ['kaiping']],
            ['image', 'image', 'skipOnEmpty' => true, 'minWidth' => 600, 'underWidth' => '{attribute}的宽度应为600px', 'on' => ['kaiping']],
            ['image', 'file', 'skipOnEmpty' => true, 'maxSize' => 1048576, 'tooBig' => '图片大小不能超过1M', 'on' => ['kaiping']],
            ['link', 'string', 'on' => ['kaiping']],
            ['link', 'match', 'pattern' => '/^[a-zA-Z0-9.:\/?&=_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等', 'on' => ['kaiping']],
            ['show_order', 'integer', 'on' => ['kaiping']],
            ['title', 'string', 'max' => 15, 'on' => ['kaiping']],
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
            'image' => '图片',
            'pos_id' => '位置id',
            'status' => '',
            'isDisabledInApp' => '',
            'showOnPc' => '',
            'link' => '链接',
            'del_status' => '是否删除',
            'creator_id' => '创建者管理员id',
            'updated_at' => '更新时间',
            'created_at' => '添加时间',
            'canShare'  => '页面可分享',
            'show_order'  => '显示顺序',
        ];
    }

    public function getPosAdv($code = null)
    {
        $pos = AdvPos::findOne(['code' => $code, 'del_status' => AdvPos::DEL_STATUS_SHOW]);
        if (empty($pos)) {
            return array();
        }
        $adv = self::find()->OrderBy(['id' => SORT_DESC])->select(['image', 'link', 'description'])->andWhere(['pos_id' => $pos->id, 'status' => self::STATUS_SHOW, 'del_status' => self::DEL_STATUS_SHOW])->limit($pos->number)->all();
        $adv_list = array();
        foreach ($adv as $key => $val) {
            $adv_list[$key]['image'] = self::UPLOAD_PATH.$val->image;
            $adv_list[$key]['link'] = $val->link;
            $adv_list[$key]['description'] = $val->description;
        }

        return array(
            'number' => $pos->number,
            'width' => $pos->width,
            'height' => $pos->height,
            'adv' => $adv_list,
        );
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
        if ($token && defined('IN_APP')) {
            $params['token'] = $token;
        }
        if ($shareKey) {
            $params['wx_share_key'] = $shareKey;
        }

        $link = '';
        if (isset($parseUrl['scheme'])) {
            $link .= $parseUrl['scheme'] . '://';
        }
        if (isset($parseUrl['host'])) {
            $link .= $parseUrl['host'];
        }

        return $link . $parseUrl['path'] . '?' . http_build_query($params);
    }
}
