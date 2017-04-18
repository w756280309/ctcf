<?php

namespace common\models\code;

use common\models\user\User;
use yii\db\ActiveRecord;

/**
 * Class Voucher
 */
class Voucher extends ActiveRecord
{
    public function rules()
    {
        return [
            [['goodsType_sn', 'user_id', 'ref_type', 'ref_id'], 'required'],
            [['goodsType_sn', 'redeemIp'], 'string'],
            [['card_id', 'promo_id', 'user_id'], 'integer'],
            ['isRedeemed', 'boolean'],
            [['redeemTime', 'createTime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goodsType_sn' => '商品编号',
            'ref_type' => '关联类型',
            'ref_id' => '关联编号',
            'card_id' => '卡密ID',
            'promo_id' => '活动ID',
            'user_id' => '用户ID',
            'isRedeemed' => '是否领取',
            'redeemTime' => '领奖时间',
            'redeemIp' => '领取人IP',
            'createTime' => '创建时间',
        ];
    }

    //todo 初始化代码等待合并
    public static function initNew(GoodsType $goodsType, User $user, $refData)
    {
        return new self();
    }

    //todo 领奖代码等待合并
    public static function redeem(Voucher $voucher)
    {

    }

    /**
     * 收回voucher
     *
     * @param Voucher $voucher
     * @throws \Exception
     */
    public static function rollback(Voucher $voucher)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //todo 减少　ＧｏｏdsType 的库存，　目前代金券不涉及库存

            if ($voucher->isRedeemed) {
                //todo 当奖励已经被领取时候需要有相关逻辑 此方法目前只有　温都扣积分成功但是兑吧扣积分失败　之后回调温都系统，才会调用，并且温都扣积分时候没有发奖励
            }
            $voucher->delete();

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();

            throw $ex;
        }
    }
}
