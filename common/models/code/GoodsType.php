<?php

namespace common\models\code;

use common\models\user\User;
use Yii;
use yii\db\ActiveRecord;

class GoodsType extends ActiveRecord
{
    const TYPE_COUPON = 1;
    const TYPE_GOODS = 2;
    const TYPE_VIRTUAL_CARD = 3;

    const REF_TYPE_MALL_ORDER = 'ref_type_mall_order';

    public function rules()
    {
        return [
            ['sn', 'string'],
            ['name', 'required'],
            ['name', 'string', 'max' => 15],
            ['sn', 'unique', 'message' => '此代金券已经被商品添加！'],
            [['type', 'effectDays', 'affiliator_id'], 'integer'],
            ['createdAt', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '商品sn',
            'type' => '商品类型',
            'createdAt' => '创建时间',
            'effectDays' => '有效期天数',
            'affiliator_id' => '合作方ID',
            'isSkuEnabled' => '是否开启SKU',
            'stock' => '库存数量',
        ];
    }

    public function getCode()
    {
        return $this->hasMany(Code::className(), ['goodsType_sn' => 'sn']);
    }

    /**
     * 生成实体商品sn
     */
    public static function createGiftSn()
    {
        return 'GIFT' . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 根据sn获得商品
     *
     * @param string $sn 商品编号
     *
     * @return null|GoodsType
     */
    public static function fetchOneBySn($sn)
    {
        //传送给兑吧的商品sn添加了'duiba_'，故在此做过滤处理
        $sn = str_replace('duiba_', '', $sn);

        return empty($sn) ? null : GoodsType::findOne(['sn' => $sn]);
    }

    /**
     * 获得以兑吧为前缀的sn
     */
    public static function getSnForDuiBa($sn)
    {
        return 'duiba_' . $sn;
    }

    /**
     * 根据sn及用户获得一个待发放的Voucher
     *
     * @param string     $sn
     * @param User       $user
     * @param null|array $ref
     *
     * @return Voucher
     * @throws \Exception
     */
    public static function issuerVoucher($sn, User $user, $ref = null)
    {
        $goodsType = GoodsType::fetchOneBySn(['sn' => $sn]);

        //判断商品是否存在
        if (null === $goodsType) {
            throw new \Exception('商品不存在');
        }

        //检测是否为已发过的voucher
        if (null !== $ref) {
            $voucher = Voucher::find()
                ->where(['ref_type' => $ref['type']])
                ->andWhere(['ref_id' => $ref['id']])
                ->one();
            if (null !== $voucher) {
                throw new \Exception('该订单已存在，不能重复发放');
            }
        }
        //todo elseif 使用库存和卡密的判断

        //初始化一条Voucher
        $voucher = Voucher::initNew($goodsType, $user, $ref);

        return $voucher;
    }
}
