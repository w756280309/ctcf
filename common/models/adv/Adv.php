<?php

namespace common\models\adv;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "adv_pos".
 */
class Adv extends ActiveRecord
{
    //0显示，1隐藏
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;

    //0正常，1删除
    const DEL_STATUS_SHOW = 0;
    const DEL_STATUS_DEL = 1;

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
            'update' => ['id', 'sn', 'title', 'pos_id', 'image', 'show_order', 'link', 'description', 'del_status', 'isDisabledInApp', 'showOnPc'],
            'create' => ['pos_id', 'sn', 'title', 'image', 'show_order', 'link', 'description', 'del_status', 'isDisabledInApp', 'showOnPc'],
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
            ['showOnPc', 'default', 'value' => 0],
            [['image', 'description'], 'required', 'on' => ['create', 'update']],
            ['status', 'default', 'value' => self::STATUS_SHOW, 'on' => ['create']],
            ['del_status', 'default', 'value' => self::DEL_STATUS_SHOW, 'on' => ['create']],
            [['link', 'link'], 'match', 'pattern' => '/^((\w\.)+)|[^\d]$/', 'message' => '网址格式错误'], //一些简单的验证，不能为中文、不能为纯数字

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => '描述',
            'image' => '图片',
            'pos_id' => '位置id',
            'status' => '是否显示',
            'isDisabledInApp' => '',
            'showOnPc' => '',
            'link' => '链接',
            'del_status' => '是否删除',
            'creator_id' => '创建者管理员id',
            'updated_at' => '更新时间',
            'created_at' => '添加时间',
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
}
