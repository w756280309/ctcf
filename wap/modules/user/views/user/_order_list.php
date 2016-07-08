<?php
    use common\models\product\OnlineProduct;
    use common\models\order\OnlineRepaymentPlan;
    use common\models\order\OnlineOrder;
?>
<?php if($list['data']) { foreach ($list['data'] as $o) { ?>
    <a class="loan-box block" href="/user/user/orderdetail?id=<?= $o->id ?>">
        <div class="loan-title">
            <div class="title-overflow"><?=$o->loan->title?></div>
            <?php
            if (!in_array($o->loan->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
                $classname = 'column-title-rg';
            } elseif (OnlineProduct::STATUS_HUAN === (int) $o->loan->status) {
                $classname = 'column-title-rg2';
            } else {
                $classname = 'column-title-rg1';
            }
            ?>
            <div class="loan-status <?= $classname ?>"><?=Yii::$app->params['deal_status'][$o->loan->status]?></div>
        </div>

        <div class="row loan-info">
            <div class="col-xs-8 loan-info1">
                <p><span class="info-label">认购金额：</span><span class="info-val"><?= rtrim(rtrim(number_format($o->order_money, 2), '0'), '.') ?>元</span></p>
                <?php if (0 === (int)$o->loan->finish_date) { ?>
                    <p><span class="info-label">项目期限：</span><span class="info-val"><?php $ex = $o->loan->getDuration() ?><?= $ex['value'] ?><?= $ex['unit']?></span></p>
                <?php } else { ?>
                    <p><span class="info-label">到期时间：</span><span class="info-val"><?= date('Y-m-d', $o->loan->finish_date) ?></span></p>
                <?php } ?>
            </div>
            <?php if (in_array($o->loan->status, [OnlineProduct::STATUS_NOW])) { ?>
                <div class="col-xs-4 loan-info2">
                    <p class="info-val"><?= $o->loan->getProgressForDisplay()?>%</p>
                    <p class="info-label">募集进度</p>
                </div>
            <?php } else { ?>
                <div class="col-xs-4 loan-info2">
                    <?php $profit = OnlineRepaymentPlan::getTotalLixi(new OnlineProduct(['refund_method' => $o->loan->refund_method, 'expires' => $o->loan->expires]), new OnlineOrder(['order_money' => $o->order_money, 'yield_rate' => $o->yield_rate])) ;?>
                    <p class="info-val"><?= rtrim(rtrim(number_format($profit, 2), '0'), '.') ?>元</p>
                    <p class="info-label"><?= ($o->loan->status==6)?"实际收益":"预期收益" ?></p>
                </div>
            <?php } ?>
        </div>
    </a>
<?php } ?>
<?php } ?>

