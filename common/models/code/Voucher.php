<?php

namespace common\models\code;

use common\models\user\User;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class Voucher
 */
class Voucher extends ActiveRecord
{
    const REF_TYPE_DUIBA = 'duiba_order'; //兑吧

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
            //todo 增加　GoodsType 的库存，目前代金券不涉及库存

            if ($voucher->isRedeemed) {
                //todo 当奖励已经被领取时候需要有撤销奖励相关逻辑, 此方法目前只有　温都扣积分成功但是兑吧扣积分失败　之后回调温都系统，才会调用，并且温都扣积分时候没有发奖励
            }
            $voucher->delete();

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();

            throw $ex;
        }
    }

    /**
     * 领奖
     *
     * @param Voucher $voucher
     *
     * @return Voucher
     * @throws \Exception
     */
    public static function redeem(Voucher $voucher)
    {
        //检测当前voucher领取状态
        if ($voucher->isRedeemed) {
            throw new \Exception('本订单奖励已被领取');
        }

        //检查此订单是否重复发放了
        if ($voucher->ref_id && $voucher->ref_type) {
            $vou = Voucher::find()
                ->where(['ref_type' => $voucher->ref_type])
                ->andWhere(['ref_id' => $voucher->ref_id])
                ->andWhere(['isRedeemed' => true])
                ->one();
            if (null !== $vou) {
                throw new \Exception('同一订单奖励已被领取后，无法再次领取');
            }
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //获得用户信息和商品信息
            $goodsType = $voucher->goodsType;
            $user = $voucher->user;

            //暂时只支持兑换记录为代金券的立即发放
            if (GoodsType::TYPE_COUPON === $goodsType->type) {
                $couponType = CouponType::findOne($goodsType->sn);
                if (null === $couponType) {
                    throw new \Exception('发送失败，未找到合适的代金券');
                }
                UserCoupon::addUserCoupon($user, $couponType);
            }
            //todo elseif 库存及卡密的奖励发放

            $voucher->isRedeemed = true;
            $voucher->redeemTime = date('Y-m-d H:i:s');
            $voucher->save();
            $transaction->commit();

            return $voucher;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * 初始化Voucher
     *
     * @param GoodsType $goodsType 商品
     * @param User      $user      用户
     * @param array     $ref       关联信息 ['ref_type' => '', 'ref_id' => '']
     *
     * @return Voucher
     */
    public static function initNew(GoodsType $goodsType, User $user, $ref)
    {
        return new self([
            'goodsType_sn' => $goodsType->sn,
            'ref_type' => isset($ref['ref_type']) ? $ref['ref_id'] : null,
            'ref_id' => isset($ref['ref_id']) ? $ref['ref_id'] : null,
            'user_id' => $user->id,
            'isRedeemed' => false,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 获得商品
     */
    public function getGoodsType()
    {
        return $this->hasOne(GoodsType::className(), ['sn' => 'goodsType_sn']);
    }

    /**
     * 获得用户
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
