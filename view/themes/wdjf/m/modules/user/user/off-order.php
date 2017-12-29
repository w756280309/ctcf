<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-26
 * Time: 上午11:21
 */

use wap\assets\WapAsset;

$this->title = '门店理财';
$this->backUrl = $backUrl ? $backUrl : '/user/user';
$back = $backUrl ? urlencode(Yii::$app->request->hostInfo.$backUrl) : '';

$this->registerCssFile(ASSETS_BASE_URI.'css/bind.css?v=1', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css?v=20160927', ['depends' => WapAsset::class]);

?>
    <script type="text/javascript">
        var url = '/user/user/myorder?type=';
        var tp = '<?= $pages->pageCount ?>';
    </script>
    <script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<?php if(!empty($model))  { ?>
    <?php foreach ($model as $val) {  $loan = null; ?>
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
    <div class="load"></div>
<?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>