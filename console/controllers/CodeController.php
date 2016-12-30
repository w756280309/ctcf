<?php

namespace console\controllers;

use common\models\code\GoodsType;
use Yii;
use yii\console\Controller;
use common\models\code\Code;

class CodeController extends Controller
{
    private $coupon_nums = 10;
    private $goods_nums = 20;
    private $goods_config = [
        1 => [
            '0022:20000-20' => '20元面值代金券',
            '0022:50000-50' => '50元面值代金券',
            '0022:100000-120' => '120元面值代金券',
            '0022:200000-180' => '180元面值代金券',
        ],
        2 => [
            'oil_900' => '金龙鱼食用油900ml',
            'recharge_100' => '温都猫充值卡100元',
            'woema_100' => '沃尔玛超市卡100元面值',
            'woema_500' => '沃尔玛超市卡500元面值',
            'guihuagao_50' => '桂新园蛋糕券50元面值',
            'oil_4000' => '金龙鱼玉米油4L',
            'baijiebu_10' => '德国ARO百洁布10片装',
            'baowenping_1500' =>'象印不锈钢手提式保温瓶1.5L',
            'yagao_100' => 'Aquafresh站立式三色牙膏100ml',
            'xiaomi_usb' => '小米USB插线板',
            'xiaomi' => '小米手环',
            'chongdianbao' => '罗马仕充电宝10000毫安',
            'code_test' => '兑换码-测试',
        ],
    ];

    /*
     * 定时任务生成兑换码
     * 针对于兑换码过期时间设置为2017-12-31 23:59:59
     *
     */
    public function actionInsertrecords()
    {
        $db = Yii::$app->db;
        $config = $this->goods_config;
        $initnum = Code::find()->count();
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = '2017-12-31 23:59:59';

        $transaction = $db->beginTransaction();
        try {
            foreach ($config as $k => $v) {
                $nums = Code::TYPE_GOODS === $k ? $this->goods_nums : $this->coupon_nums;
                foreach ($v as $sn => $name) {
                    $goodsType = new GoodsType();
                    $goodsType->sn = $sn;
                    $goodsType->name = $name;
                    if ($goodsType->save()) {
                        if ($sn === 'code_test') {
                            $code = new Code();
                            $code->code = $this->createRandomStr();
                            $code->expiresAt = $expiresAt;
                            $code->createdAt = $createdAt;
                            $code->goodsType_sn = '0022:1000-10';
                            $code->goodsType = 1;
                            $code->save();
                            $nums = 1;
                        }

                        for ($j = 0; $j < $nums; $j++) {
                            $code = new Code();
                            $code->code = $this->createRandomStr();
                            $code->expiresAt = $expiresAt;
                            $code->createdAt = $createdAt;
                            $code->goodsType_sn = $sn;
                            $code->goodsType = $k;
                            $code->save();
                        }

                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            echo $ex->getCode();
            $transaction->rollBack();
        }
        $finalnum = Code::find()->count();
        $insertnum = $finalnum - $initnum;
        echo "当前表里共有{$finalnum}条,本次总共插入{$insertnum}条兑换码记录";
    }

    private function createRandomStr()
    {
        $code = '';
        $str = "BCEFGHJKMPQRTVWXY2346789";
        $length = strlen($str) - 1;
        for ( $i = 0; $i < 16; $i++ ) {
            $code .= substr($str, mt_rand(0, $length), 1);
        }
        return $code;
    }
}
