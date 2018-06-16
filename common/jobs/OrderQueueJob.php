<?php
/*
新建保全思路

（1）根据成功订单，生成指定格式的pdf合同（ContractTemplate::replaceTemplate()）,可以替换认购协议、风险提示书；项目说明直接保存
（2）将合同保存为指定文件名，文件保存到临时目录中
（3）新建保全，保存本地数据库【数据结构见表：ebao_quan】
（4）删除临时文件
 */
namespace common\jobs;

use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use yii\queue\Job;
use yii\base\Object;

class OrderQueueJob extends Object implements Job  //需要继承Object类和Job接口
{
    public $userId;
    public $sn;
    public $promoId;

    public function execute($queue)
    {
        $reward = Reward::fetchOneBySn($this->sn);
        $user = User::findOne($this->userId);
        $promo = RankingPromo::findOne($this->promoId);
        PromoService::award($user, $reward, $promo);
    }
}
