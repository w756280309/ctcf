<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-26
 * Time: 下午2:43
 */
$this->title = '投资详情';
use common\models\product\RateSteps;
use common\models\order\OnlineRepaymentPlan;
use common\utils\StringUtils;
use wap\assets\WapAsset;
use yii\helpers\ArrayHelper;
use yii\web\JqueryAsset;

$this->registerJsFile(ASSETS_BASE_URI .'js/fastclick.js', ['position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI .'js/touzixiangqing.js?v=20170306', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI .'css/touzixiangqing.css?v=20170306', ['depends' => WapAsset::class]);
?>
<div class="container">
    <!--invest-title-->
    <div class="row" id="invest-box">
        <div class="col-xs-12">
            <div id="invest-title">
                <div class="invest-title">
                    <div class="invest-left"><a href="#"><?= '【门店】' . $model->loan->title?></a></div>
                    <div class="invest-right">
                        <?php if ($model->loan->status == '收益中') { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-orange">收益中</div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-huang.png" alt="">
                        <?php } elseif ($model->loan->status == '募集中') { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-gray">募集中</div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-hui.png" alt="">
                        <?php } else { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-red">已还清</div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-jian.png" alt="">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--投资信息-->
    <div class="row" id="information-box">
        <div class="col-xs-12">
            <div class="information-title">
                <div class="information-left">投资信息</div>
                <div class="information-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </div>
        </div>
    </div>
    <!--投资信息详情-->
    <div class="row" id="information-content">
        <div class="col-xs-12">
            <ul class="information-content">
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat2(bcmul($model->money, 10000)) ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">认购日期</div>
                    <div class="information-content-right"><?= $model->orderDate ?></div>
                </li>
                <li>
                    <div class="information-content-left">起息日期</div>
                    <div class="information-content-right"><?= mb_substr($model->loan->jixi_time, 0, 10) ?></div>
                </li>
                <li>
                    <div class="information-content-left">项目期限</div>
                    <div class="information-content-right"><?= $model->loan->expires . $model->loan->unit ?></div>
                </li>
                <li>
                    <div class="information-content-left">预期收益</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat3($model->expectedEarn) ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">还款方式</div>
                    <div class="information-content-right"><?= Yii::$app->params['refund_method'][$model->loan->repaymentMethod] ?></div>
                </li>
            </ul>
        </div>
    </div>
    <?php if(!empty($plans)) : ?>
        <!--还款计划-->
        <div class="row" id="repayment-box">
            <div class="col-xs-12">
                <div class="repayment-title">
                    <div class="repayment-left">还款计划</div>
                    <div class="repayment-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
                </div>
            </div>
        </div>
    <div class="row" id="repayment-content">
        <div class="col-xs-12">
            <ul class="repayment-content">
            <?php foreach($plans as $key => $plan) : ?>
                <li>
                    <div>第<?= $plan->qishu ?>期</div>
                    <div><?= $plan->refund_time ?></div>
                    <div><?= $plan->benxi == $plan->lixi ? '利息' : '本息' ?></div>
                    <div><?= StringUtils::amountFormat2($plan->benxi) ?>元</div>
                    <!--还款计划-文字颜色-->
                    <p class="<?= $plan->repaymentStatus ? 'repayment-green' : 'repayment-red' ?>"><?= $plan->repaymentStatus ? '已还' : '未还' ?></p>
                </li>
            <?php endforeach; ?>
                <!--下拉按钮-->
                <div class="repayment-down"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <div class="row" id="subscription-box">
        <div class="col-xs-12">
            <a href="#" class="subscription-title">
                <div class="subscription-left">还款银行 </div>
                <div class="subscription-right"><?= substr_replace($model->bankCardNo, '**** **** **** ', 0, -4) . '（'. $model->accBankName .'）'?></div>
            </a>
        </div>
    </div>
    <div class="row" id="subscription-box">
        <div class="col-xs-12">
            <a href="#" class="subscription-title">
                <div class="subscription-left">认购合同 <span class=""></span></div>
                <div class="subscription-right">（已提供纸质版）</div>
            </a>
        </div>
    </div>
</div>
