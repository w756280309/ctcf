<?php

$this->title = '我要出借';

$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/attorn.min.css?v=1.111', ['depends' => 'frontend\assets\CtcfFrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/pagination.css', ['depends' => 'frontend\assets\CtcfFrontAsset']);

use common\utils\StringUtils;
use common\widgets\Pager;

$action = Yii::$app->controller->action->getUniqueId();

?>
<div class="ctcf-container">
    <div class="main">
        <ul class="product-nav clear-fix fz18">
            <li class="lf <?= 'licai/index' === $action ? 'active-nav-product' : '' ?>"><a href="/licai/">散标列表</a></li>
            <li class="lf <?= 'licai/notes' === $action ? 'active-nav-product' : '' ?>"><a href="/licai/">转让列表</a></li>
        </ul>
        <div class="attorn-product-list fz-gray">
            <ul class="attorn-list">
                <!--转让中-->
                <?php foreach ($notes as $note) : ?>
                <?php
                $loan = $note['loan'];
                $order = $note['order'];
                $endTime = new \DateTime($note['endTime']);
                $nowTime = new \DateTime();
                $tradedAmount = $note['tradedAmount'];
                $amount = $note['amount'];
                $realClosed = $note['isClosed'] || $nowTime >= $endTime;
                //转让中状态应对应为：isClosed=false
                $progress = $realClosed ? 100 : bcdiv(bcmul($tradedAmount, '100'), $amount, 0);
                ?>
                <li class="product-attorning">
                    <a class="fz-gray" href="/credit/note/detail?id=<?= $note['id'] ?>" target="_blank">
                        <div class="product-top-part">
                            <!--新手专享角标-->
                            <!--<i class="sup-icon"></i>-->
                            <div class="product-title">
                                <u class="attorn-msg fz16 fz-orange-strong">【转让】</u>
                                <h5 class="fz18"><?= null === $loan ? '' : $loan->title ?></h5>
                            </div>
                        </div>
                        <div class="product-bottom-part clear-fix">
                            <div class="interest-rate">
                                <div class="rate-top-part fz28 fz-orange-strong">
                                    <span class="fz42 "><?= null === $order ? '' : StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2)) ?></span>%
                                </div>
                                <div class="rate-bottom-part fz14">借贷双方约定利率</div>
                            </div>
                            <div class="term-rate">
                                <div class="term-top-part fz18 fz-black">
                                    <?php
                                    if (null !== $loan) {
                                        $remainingDuration = $loan->getRemainingDuration();
                                        if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                                            echo '<span class="fz30">'.$remainingDuration['months'] . '</span>个月';
                                        }
                                        if (isset($remainingDuration['days'])) {
                                            if (!isset($remainingDuration['months']) || $remainingDuration['days'] >0) {
                                                echo '<span class="fz30">'.$remainingDuration['days'] . '</span>天';
                                            }
                                        }
                                    } else {
                                        echo '<span class="fz30">0</span>天';
                                    }

                                    ?>
                                </div>
                                <div class="term-bottom-part fz14">剩余期限</div>
                            </div>
                            <div class="mode-style fz14">
                                <div class="mode-top-part">转让金额<span class="fz14 fz-black"><?= StringUtils::amountFormat1('{amount}{unit}', $note['amount'] / 100) ?></span></div>
                                <div class="mode-bottom-part">
                                    <p>计息方式</p>
                                    <p class="mode-bottom-part-msg">
                                        <span class="fz14 fz-black"><?= null === $loan ? '' :  Yii::$app->params['refund_method'][$loan->refund_method] ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="raise-mode">
                                <div class="raise-top-part">距离结束<span class="fz14 fz-black"><?php
                                        if ($realClosed) {
                                            echo '0天0时0分';
                                        } else {
                                            $dateDiff = $endTime->diff($nowTime);
                                            echo $dateDiff->d . '天' . $dateDiff->h . '时' . $dateDiff->i . '分';
                                        }
                                        ?></span></div>
                                <div class="raise-bottom-part">转让进度
                                    <div class="speed-raise">
                                        <!--进度条-->
                                        <i style="width: <?= $progress ?>%;"></i>
                                    </div>
                                    <div class="speed-raise-number fz14"><?= $progress ?>%</div>
                                </div>
                            </div>
                            <div class="btn-check">
                                <?php
                                if ($realClosed) {
                                    ?>
                                    <div class="btn-gray">转让完成</div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="btn-orange">转让中</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
                <!--转让完成-->
            </ul>
            <?php if (!empty($notes)) { ?>
                <center><?= Pager::widget(['pagination' => $pages])?></center>
            <?php } else { ?>
                <p class="yet_show">暂无转让项目</p>
            <?php } ?>

        </div>
    </div>
</div>
<script>

    $(function(){
        $('h5.product-top-title').each(function(i,item){
            console.log(this);
            if(this.innerHTML.length>25){
                var tpl=this.innerHTML;
                var title=tpl.substr(0,25);
                this.innerHTML=title+'...';
            }
        })
    })
</script>
