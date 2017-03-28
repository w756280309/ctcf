<?php

namespace console\controllers;

use common\models\code\GoodsType;
use common\models\code\VirtualCard;
use yii\console\Controller;

class CardController extends Controller
{
    private $number = 300; //默认生成券码的数量
    private $goodsSn = 'quchaqu'; //商品
    private $noSecert = true; //是否生成密码开关
    private $isReserved = false; //是否为积分商城预留

    /**
     * 生成指定商品指定数量的券码
     *
     * @param null $goods_sn 商品sn
     * @param null $num      数量
     *
     * @return int
     */
    public function actionAdd($goods_sn = null, $num = null)
    {
        $goodsSn = null !== $goods_sn ? $goods_sn : $this->goodsSn;
        $cardNum = null !== $num ? $num : $this->number;
        $goods = GoodsType::find()
            ->where(['sn' => $goodsSn])
            ->one();
        if (null === $goods) {
            $this->stdout('该商品不存在');
            return 1;
        }

        $initNum = VirtualCard::find()->count();
        for ($i = 0; $i < $cardNum; $i++) {
            $card = new VirtualCard();
            $card->serial = $this->createRandomStr();
            if (!$this->noSecert) {
                $card->secret = $this->createRandomStr();
            }
            $card->createTime = date('Y-m-d H:i:s');
            $card->goodsType_id = $goods->id;
            $card->affiliator_id = $goods->affiliator_id;
            $card->isReserved = $this->isReserved;
            $card->save();
        }
        $finialNum = VirtualCard::find()->count();
        $this->stdout('总共插入券码'. ($finialNum - $initNum) . '条');
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
