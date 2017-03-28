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
            [['isPull', 'isUsed', 'user_id', 'isReserved'], 'integer'],
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
            'isReserved' => '积分商城预留',
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

    /**
     * 根据当前VitrualCard获得该兑换码的状态
     * 目前O2O商家管理-兑换码列表在使用
     * code   label
     * 1      未发放
     * 2      已发放
     * 3      已使用
     * 4      已过期
     *
     * @return array ['code' => '', status => '']
     */
    public function getStatus()
    {
        $label = null;
        $code = null;
        $isExpired = false;
        if ($this->isUsed) {
            $label = '已使用';
            $code = 3;
        } else {
            if (null !== $this->expiredTime) {
                if ($this->expiredTime < date('Y-m-d H:i:s')) {
                    $isExpired = true;
                }
            }
            if (!$this->isPull) {
                $label = '未发放';
                $code = 1;
            } else {
                if ($isExpired) {
                    $label = '已过期';
                    $code = 4;
                } else {
                    $label = '已发放';
                    $code = 2;
                }
            }
        }

        return ['code' => $code, 'label' => $label];
    }
}
