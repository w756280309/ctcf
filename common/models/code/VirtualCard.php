<?php
namespace common\models\code;

use common\models\user\User;
use yii\db\ActiveRecord;

class VirtualCard extends ActiveRecord
{
    public function rules()
    {
        return [
            ['serial', 'required', 'message' => '请填写兑换码'],
            ['goodsType_id', 'required'],
            [['serial', 'secret'], 'match', 'pattern' => '/^([0-9a-z]+)*$/i', 'message' => '{attribute}必须是字母与数字的组合'],
            [['serial', 'secret'], 'unique', 'message' => '兑换码及密码应唯一'],
            [['isPull', 'isUsed', 'user_id'], 'integer'],
            [['pullTime', 'usedTime', 'createTime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serial' => '券码',
            'secret' => '密码',
            'user_id' => 'User ID',
            'isPull' => '已领取',
            'pullTime' => '领取时间',
            'isUsed' => '已使用',
            'usedTime' => '使用时间',
            'createTime' => '创建时间',
            'goodsType_id' => '商品ID',
            'affiliator_id' => '商家ID',
            'expiredTime' => '过期时间',
            'usedMobile' => '使用者手机号',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(GoodsType::className(), ['id' => 'goodsType_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getStatusLabel()
    {
        $label = '--';
        $goods = $this->goods;
        $isExpired = false;
        if ($this->isUsed) {
            $label = '已使用';
        } else {
            if (null !== $this->expiredTime) {
                if ($this->expiredTime < date('Y-m-d H:i:s')) {
                    $isExpired = true;
                }
            }
            if (!$this->isPull) {
                $label = '未发放';
            } else {
                if ($isExpired) {
                    $label = '已过期';
                } else {
                    $label = '已发放';
                }
            }
        }

        return $label;
    }
}
