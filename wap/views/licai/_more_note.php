<?php

use common\utils\StringUtils;

?>
<?php foreach ($notes as $note) { ?>
    <?php
        $loan = $note['loan'];
        $order = $note['order'];
        $endTime = new \DateTime($note['endTime']);
        $nowTime = new \DateTime();
        $tradedAmount = $note['tradedAmount'];
        $amount = $note['amount'];
        //转让中状态应对应为：isClosed=false
        $progress = ($note['isClosed'] || $nowTime >= $endTime) ? 100 : bcdiv(bcmul($tradedAmount, '100'), $amount, 0);
    ?>
    <a class="row col" href="/credit/note/detail?id=<?= $note['id'] ?>">
        <div class="col-xs-12 col-sm-12 col-txt">
            <div class="row clearfix credit-num">
                <div class="col-xs-10 col-sm-10 col-title">
                    <span class="item-tit"><i class="credit-lf">【转让】</i><?= null === $loan ? '' : $loan->title ?></span>
                </div>
                <div class="col-xs-2 col-sm-2 col-title">
                    <?php
                        if ($note['isClosed'] || $nowTime >= $endTime) {
                    ?>
                        <i class="credit-staus credit-staus-over">已转让</i>
                    <?php
                        } else {
                    ?>
                        <i class="credit-staus">转让中</i>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <div class="row credit-all clearfix" >
                <div class="col-xs-4">
                    <span class="rate-steps">
                        <?=
                            null === $order ? '' : StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2));
                        ?>
                        <i class="col-lu">%</i></span>
                    <p>预期年化率</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps">
                        <?php
                            if (null !== $loan) {
                                $remainingDuration = $loan->getRemainingDuration();
                                if (isset($remainingDuration['months'])) {
                                    echo $remainingDuration['months'] . '<i class="col-lu">个月</i>';
                                }
                                if (isset($remainingDuration['days'])) {
                                    echo $remainingDuration['days'] . '<i class="col-lu">天</i>';
                                }
                            } else {
                                echo '0<span>天</span>';
                            }
                        ?>
                    </span>
                    <p>剩余期限</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps">
                        <?= StringUtils::amountFormat1('{amount}<i class="col-lu">{unit}</i>', $note['amount'] / 100) ?>
                    </span>
                    <p>转让金额</p>
                </div>
            </div>
            <div class="row credit-per">
                <div class="col-xs-10"><span class="credit-per-length"><i class="credit-per-width" style="width:<?= $progress ?>%;"></i></span></div>
                <div class="col-xs-2"><span class="credit-per-num credit-over-color"><?= $progress ?>%</span></div>
            </div>
            <div class="row credit-repay">
                <div class="col-xs-12">
                    <i></i>
                    <span>还款方式：<?= null === $loan ? '' :  Yii::$app->params['refund_method'][$loan->refund_method] ?></span>
                </div>
            </div>
        </div>
    </a>
<?php } ?>
