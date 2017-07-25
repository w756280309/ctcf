<?php

namespace common\models\code;

use common\models\user\User;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class Voucher
 *
 * @property int         $id           主键
 * @property null|string $goodsType_sn 商品编号
 * @property string      $ref_type     关联类型（原因）
 * @property null|int    $ref_id       关联ID（原因）
 * @property null|int    $card_id      卡密ID
 * @property null|int    $promo_id     活动ID
 * @property int         $user_id      用户ID
 * @property bool        $isRedeemed   是否领取
 * @property null|string $redeemTime   领取时间
 * @property null|string $redeemIp     领取人IP
 * @property string      $createTime   创建时间
 * @property string      $orderNum     兑吧订单ID, 是虚拟商品充值ID, 和扣除积分的orderNum 没有关系
 * @property null|string $expireTime   过期时间
 * @property bool        $isOp         是否为运营
 * @property string      $amount       面值
 */
class Voucher extends ActiveRecord
{
    const REF_TYPE_DUIBA = 'duiba_order'; //兑吧

    public function rules()
    {
        return [
            [['goodsType_sn', 'user_id'], 'required'],
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
            'redeemTime' => '领取时间',
            'redeemIp' => '领取人IP',
            'createTime' => '创建时间',
            'orderNum' => '兑吧订单号',
            'expiryTime' => '过期时间',
            'isOp' => '是否运营',
            'amount' => '面值',
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

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //尝试更新voucher的状态，若更新失败，直接抛异常
            $sql = 'update voucher set isRedeemed = :isRedeemed,redeemTime = :redeemTime,redeemIp = :redeemIp where id = :id and isRedeemed = :isRedeemedFalse';
            $affected_rows = $db->createCommand($sql, [
                'isRedeemed' => true,
                'redeemTime' => date('Y-m-d H:i:s'),
                'redeemIp' => $voucher->redeemIp,
                'id' => $voucher->id,
                'isRedeemedFalse' => false,
            ])->execute();

            if (!$affected_rows) {
                throw new \Exception('发奖失败');
            }

            //更新当前voucher信息
            $voucher->refresh();

            //获得用户信息和商品信息
            $goodsType = $voucher->goodsType;
            $user = $voucher->user;

            //暂时只支持兑换记录为代金券的立即发放
            if (GoodsType::TYPE_COUPON === $goodsType->type) {
                $couponType = CouponType::findOne($goodsType->sn);
                if (null === $couponType) {
                    throw new \Exception('发送失败，未找到合适的代金券');
                }
                UserCoupon::addUserCoupon($user, $couponType)->save();
            }
            //todo elseif 库存及卡密的奖励发放

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
     * @param array     $ref       关联信息 ['type' => '', 'id' => '']
     * @param string    $orderNum   兑吧订单号
     *
     * @return Voucher
     */
    public static function initNew(GoodsType $goodsType, User $user, $ref, $orderNum = null)
    {
        return new self([
            'goodsType_sn' => $goodsType->sn,
            'orderNum' => $orderNum,
            'ref_type' => isset($ref['type']) ? $ref['type'] : null,
            'ref_id' => isset($ref['id']) ? $ref['id'] : null,
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
