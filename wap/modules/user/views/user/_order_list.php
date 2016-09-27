<?php
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
?>

<?php foreach ($model as $val) { ?>
        <?php if (2 === $type) { ?>
            <a class="loan-box block" href="/user/user/orderdetail?id=<?= $val['id'] ?>">
        <?php } else { ?>
            <a class="loan-box block" href="">
        <?php } ?>
        <div class="loan-title">
            <?php if (2 === $type) { ?>
                <div class="title-overflow"><?= $val['loan']['title'] ?></div>
            <?php } else { ?>
                <div class="title-overflow"><?= (empty($val['note_id']) ? '' : '【转让】').$val['loan']['title'] ?></div>
            <?php } ?>
            <?php
                $loanStatus = (int) $val['loan']['status'];
                if (!in_array($loanStatus, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
                    $classname = 'column-title-rg';
                } elseif (OnlineProduct::STATUS_HUAN === $loanStatus) {
                    $classname = 'column-title-rg2';
                } else {
                    $classname = 'column-title-rg1';
                }
            ?>
            <div class="loan-status <?= $classname ?>"><?=Yii::$app->params['deal_status'][$loanStatus]?></div>
        </div>

        <div class="row loan-info">
            <div class="col-xs-8 loan-info1">
                <p>
                    <span class="info-label">认购金额：</span><span class="info-val">
                        <?php
                            if (2 === $type) {
                                echo StringUtils::amountFormat3($val['order_money']);
                            } else {
                                echo StringUtils::amountFormat3(bcdiv($val['amount'], 100, 2));
                            }
                        ?>
                    元</span>
                </p>
                <?php if (empty($val['loan']['finish_date'])) { ?>
                    <p>
                        <span class="info-label">项目期限：</span>
                        <span class="info-val">
                            <?php
                                $loan = new OnlineProduct($val['loan']);
                                $ex = $loan->getDuration();
                            ?>
                            <?= $ex['value'] ?><?= $ex['unit']?>
                        </span>
                    </p>
                <?php } else { ?>
                    <p><span class="info-label">到期时间：</span><span class="info-val"><?= date('Y-m-d', $val['loan']['finish_date']) ?></span></p>
                <?php } ?>
            </div>
            <?php if (OnlineProduct::STATUS_NOW === $loanStatus) { ?>
                <div class="col-xs-4 loan-info2">
                    <p class="info-val"><?= $loan->getProgressForDisplay() ?>%</p>
                    <p class="info-label">募集进度</p>
                </div>
            <?php } else { ?>
                <div class="col-xs-4 loan-info2">
                    <?php
                        unset($val['loan']);
                        $order = 2 === $type ? (new OnlineOrder($val)) : $val['order'];
                        $profit = $order->getProceeds();
                    ?>
                    <p class="info-val"><?= StringUtils::amountFormat3($profit) ?>元</p>
                    <p class="info-label"><?= (OnlineProduct::STATUS_OVER === $loanStatus) ? "实际收益" : "预期收益" ?></p>
                </div>
            <?php } ?>
        </div>
    </a>
<?php } ?>