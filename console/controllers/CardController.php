<?php

namespace console\controllers;

use common\models\affiliation\AffiliateCampaign;
use common\models\affiliation\Affiliator;
use common\models\code\GoodsType;
use common\models\code\VirtualCard;
use Yii;
use yii\console\Controller;

class CardController extends Controller
{
    private $number = 300; //默认生成券码的数量
    private $goodsSn = 'yikecoffee'; //渠道商品
    private $affiliatorName = '意克咖啡'; //合作商名称
    private $trackCode = 'yikecoffee'; //渠道码
    private $noSecert = true; //是否生成密码开关

    /**
     * 添加合作商及合作商渠道
     *
     * @param null|string $name      合作商名称
     * @param null|string $trackCode 渠道码
     *
     * @return int
     */
    public function actionAddAffiliator($name = null, $trackCode = null)
    {
        $AffiliatorName = null === $name ? $this->affiliatorName : $name;
        if (null !== Affiliator::find()->where(['name' => $AffiliatorName])->one()) {
            $this->stdout('合作商已存在');
            return 1;
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            $affiliator = new Affiliator();
            $affiliator->name = $AffiliatorName;
            if($affiliator->save()) {
                $campaign = new AffiliateCampaign();
                $campaign->trackCode = null === $trackCode ? $this->trackCode : $trackCode;
                $campaign->affiliator_id = $affiliator->id;
                if ($campaign->save()) {
                    $transaction->commit();
                    $this->stdout('插入合作商和渠道码成功');
                    return 0;
                }
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->stdout('添加合作商及渠道信息失败');
            return 1;
        }
    }

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
