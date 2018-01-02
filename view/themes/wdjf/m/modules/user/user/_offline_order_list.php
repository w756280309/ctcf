<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-29
 * Time: 下午11:43
 */
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
?>
<?php foreach ($model as $val) {  ?>
    <a class="loan-box block" href="/user/user/offline-orderdetail?id=<?= $val->id ?>">
        <div class="loan-title">
            <div class="title-overflow"><?= '【门店】' . $val->loan->title ?></div>
            <?php
            if ($val->loan->status == '募集中') {
                $class = ['class' => 'column-title-rg1', 'name' => '募集中'];
            } else if ($val->loan->status == '收益中') {
                $class = ['class' => 'column-title-rg2', 'name' => '收益中'];
            } else {
                $class = ['class' => 'column-title-rg', 'name' => '已还清'];
            }
            ?>
            <div class="loan-status <?= $class['class'] ?>"><?= $class['name'] ?></div>
        </div>
        <div class="row loan-info">
            <div class="col-xs-8 loan-info1">
                <p>
                    <span class="info-label">认购金额：</span>
                    <span class="info-val">
                        <?= \common\utils\StringUtils::amountFormat3($val->money * 10000) ?>
                        元</span>
                </p>
                <p>
                    <span class="info-label">认购日期：</span>
                    <span class="info-val">
                        <?= $val->orderDate ?>
                    </span>
                </p>
                <span class="info-label">到期时间：</span>
                <span class="info-val">
                        <?=  mb_substr($val->loan->finish_date, 0, 10) ?>
                    </span>
            </div>
            <?php if ($val->expectedEarn > 0) : ?>
                <div class="col-xs-4 loan-info2">
                    <p class="info-val"><?= $val->expectedEarn ?>元</p>
                    <p class="info-label"><?= $val->loan->status == '已还清' ? "实际收益" : "预期收益" ?></p>
                </div>
            <?php endif; ?>
        </div>
    </a>
<?php } ?>
