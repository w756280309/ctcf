<?php
use common\view\LoanHelper;
use common\utils\StringUtils;
?>

<?php foreach ($deals as $val): ?>
    <a class="row col" href="/deal/deal/detail?sn=<?= $val->sn ?>">
        <div class="col-xs-12 col-sm-12 col-txt">
            <div class="row clearfix credit-num">
                <div class="col-xs-10 col-sm-10 col-title">
                    <span class="item-tit"><i class="credit-lf"></i><?= $val->title ?></span>
                </div>
                <div class="col-xs-2 col-sm-2 col-title"><i class="credit-staus <?= in_array($val->status, [5, 6]) ? 'credit-staus-over' : '' ?>"><?= Yii::$app->params['deal_status'][$val->status] ?></i></div>
            </div>
            <div class="row credit-all clearfix" >
                <div class="col-xs-4">
                    <span class="rate-steps">
                        <?= LoanHelper::getDealRate($val) ?><i class="col-lu">%<?php if (!empty($val->jiaxi) && !$val->isFlexRate) { ?><em class="credit-jiaxi">+<?= StringUtils::amountFormat2($val->jiaxi) ?>%</em><?php } ?></i>
                    </span>
                    <p>预期年化率</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps"><?php $ex = $val->getDuration() ?><?= $ex['value'] ?><?= $ex['unit']?></span>
                    <p>期限</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps"><?= StringUtils::amountFormat2($val->start_money) ?>元</span>
                    <p>起投</p>
                </div>
            </div>
            <div class="row credit-per">
                <?php $progress = $val->getProgressForDisplay(); ?>
                <div class="col-xs-10"><span class="credit-per-length"><i class="credit-per-width" style="width:<?= $progress ?>%;"></i></span></div>
                <div class="col-xs-2"><span class="credit-per-num credit-over-color"><?= $progress ?>%</span></div>
            </div>
            <div class="row credit-repay">
                <div class="col-xs-12">
                    <i></i>
                    <span>还款方式：<?= Yii::$app->params['refund_method'][$val->refund_method] ?></span>
                </div>
            </div>
        </div>
    </a>
<?php endforeach; ?>