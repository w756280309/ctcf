<?php

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\Html;

?>

<?php foreach ($deals as $deal): ?>
    <?php $isActive = !in_array($deal->status, [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW]) || $deal->end_date < time(); ?>
    <a class="row col" href="/deal/deal/detail?sn=<?= $deal->sn ?>">
        <?php if ($deal->is_xs && in_array($deal->status, [1, 2])) { ?>
            <?php if ($deal->end_date >= time()) :?>
                <div class="newer" ><img src="<?= ASSETS_BASE_URI ?>images/newer.png" alt="新手专享"></div>
            <?php endif;?>
        <?php } ?>
        <div class="col-xs-12 col-sm-12 col-txt">
            <div class="row clearfix credit-num">
                <div class="col-xs-10 col-sm-10 col-title">
                    <span class="item-tit"><i class="credit-lf"></i><?= $deal->title ?></span>
                </div>
                <div class="col-xs-2 col-sm-2 col-title"><i class="credit-staus <?= $isActive ? 'credit-staus-over' : '' ?>"><?= ($deal->end_date < time() && in_array($deal->status, [1, 2])) ? '募集结束' : Yii::$app->params['deal_status'][$deal->status] ?></i></div>

                <?php if (!empty($deal->tags) || $deal->pointsMultiple > 1) : ?>
                    <div class="col-xs-12 col-sm-12 col-tag">
                        <?= $this->renderFile("@common/views/tags.php", ['loan' => $deal]) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row credit-all clearfix" >
                <div class="col-xs-4">
                    <span class="rate-steps <?= $isActive ? '' : 'specialcolor' ?>">
                        <?= LoanHelper::getDealRate($deal) ?><i class="col-lu">%<?php if (!empty($deal->jiaxi)) { ?><em class="credit-jiaxi">+<?= StringUtils::amountFormat2($deal->jiaxi) ?>%</em><?php } ?></i>
                    </span>
                    <p>预期年化率</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps"><?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><i class="col-lu"><?= $ex['unit']?></i></span>
                    <p>期限</p>
                </div>
                <div class="col-xs-4">
                    <span class="rate-steps"><?= StringUtils::amountFormat2($deal->start_money) ?><i class="col-lu">元</i></span>
                    <p>起投</p>
                </div>
            </div>
            <div class="row credit-per">
                <?php $progress = $deal->getProgressForDisplay(); ?>
                <div class="col-xs-10"><span class="credit-per-length"><i class="credit-per-width" style="width:<?= $progress ?>%;"></i></span></div>
                <div class="col-xs-2"><span class="credit-per-num credit-over-color"><?= $progress ?>%</span></div>
            </div>
            <div class="row credit-repay">
                <div class="col-xs-12">
                    <span>还款方式：<?= Yii::$app->params['refund_method'][$deal->refund_method] ?></span>
                </div>
            </div>
        </div>
    </a>
<?php endforeach; ?>
