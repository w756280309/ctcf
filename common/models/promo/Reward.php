<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

class Reward extends ActiveRecord
{
    const TYPE_PIKU = 'PIKU'; //实物奖品
    const TYPE_POINT = 'POINT'; //积分
    const TYPE_COUPON = 'COUPON'; //代金券
    const TYPE_RED_PACKET = 'RED_PACKET'; //红包

    public function rules()
    {
        return [
            ['sn', 'unique'],
            ['name', 'string', 'max' => 100],
            [['limit', 'promo_id', 'ref_id'], 'integer'],
            [['ref_type', 'path'], 'string'],
            ['ref_amount', 'number'],
            ['createTime', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '奖品sn',
            'name' => '奖品名称',
            'limit' => '奖品数量',
            'ref_type' => '类型',
            'ref_amount' => '面值',
            'path' => '图片路径',
            'promo_id' => '活动ID',
            'createTime' => '创建时间',
            'ref_id' => '奖品关联ID',
        ];
    }

    /**
     * 根据奖池设置数组进行一次抽奖，返回奖品设置数组的key或抽奖失败（false）
     *
     * @param array $poolSetting 奖池设置数组(需要额外验证)
     * [
     *      'packet_520' => '0.001',
     *      'packet_5.20' => '0.1',
     *      ...
     * ]
     *
     * @return string|bool
     */
    public static function draw(array $poolSetting)
    {
        //验证$poolSetting是否为空，为空返回false
        if (empty($poolSetting)) {

            return false;
        }

        //验证每抽奖概率的值必须为数值类型的字符串，且相加的和<=1，且过滤掉小于等于0的概率，否则返回false
        foreach ($poolSetting as $k => $gaiLv) {
            if (!is_numeric($gaiLv) || !is_string($gaiLv)) {

                return false;
            }
            if ($gaiLv < 0) {

                return false;
            }
        }

        if (array_sum($poolSetting) <= 0 || array_sum($poolSetting) > 1) {

            return false;
        }

        //构造奖池数组（若抽奖概率为0，不会出现在该奖池中）
        $pool = [];
        $minGaiLv = min($poolSetting);
        $base = false !== strpos($minGaiLv, '.') ? strlen($minGaiLv) - (strpos($minGaiLv, '.') + 1) : 4;
        foreach ($poolSetting as $item => $gv) {
            $num = $gv * pow(10, $base);
            for ($i = 0; $i < $num; $i++) {
                array_push($pool, $item);
            }
        }

        //获得一个key
        $poolLen = count($pool) - 1;

        return $pool[mt_rand(0, $poolLen)];
    }

    /**
     * 根据奖品sn减少对应奖品的库存（若库存limit为null，表示该库存量不限制）
     *
     * @param string $sn 奖品sn(需要额外验证，sn是否存在于数据库)
     *
     * @return bool
     */
    public static function decStoreBySn($sn)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $sql = "select * from reward where sn = :sn FOR UPDATE";
            $data = $db->createCommand($sql, ['sn' => $sn])->queryOne();

            //查找不到该奖品或者库存量为0，返回false
            if (false === $data || 0 === $data['limit']) {
                $transaction->commit();

                return false;
            }

            //若库存量limit为null，代表无限库存，不进行更新操作
            if (null === $data['limit']) {
                $transaction->commit();

                return true;
            }

            //若库存量 > 0，更新库存
            if ($data['limit'] > 0) {
                $sqlUpdate = "update reward set `limit` = `limit` - 1 where sn = :sn ";
                $db->createCommand($sqlUpdate, ['sn' => $sn])->execute();
                $transaction->commit();

                return true;
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();

            return false;
        }
    }

    public static function fetchOneBySn($sn)
    {
        return reward::find()
            ->where(['sn' => $sn])
            ->one();
    }
}
