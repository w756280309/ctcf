<?php
$this->title = "交易明细";

$this->registerJs('var mingxitype = new Array();', 1);
foreach (Yii::$app->params['mingxi'] as $key => $val) {
    $this->registerJs('mingxitype['.$key.'] = \''.$val.'\';', 1);
}
$this->registerJsFile(ASSETS_BASE_URI . 'js/moment.min.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/jiaoyimingxi.js?v=20161026', ['depends' => 'yii\web\YiiAsset','position' => 1]);
wap\assets\WapAsset::register($this);
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/jiaoyimingxi.css?v=20161026"/>

<div class="container">
    <?php if ($model) {
        foreach ($model as $val) : ?>
            <div class="clear"></div>
            <div  class="row jiaoyi">
                <div class="col-xs-1"></div>
                <div class="col-xs-10">
                    <div class="col-xs-6 lf"><p  class="way"><?= Yii::$app->params['mingxi'][$val['type']] ?></p><p class="revenue">余额：<span><?= number_format($val['balance'], 2) ?>元</span></p></div>
                    <div class="col-xs-6 rg"><p  class="money <?= ($val['in_money'] >= $val['out_money']) ? 'red' : 'green' ?>" ><?= ($val['in_money'] >= $val['out_money']) ? ('+'.number_format($val['in_money'], 2)):('-'.number_format($val['out_money'], 2)) ?></p><p  class="date" ><span class="data1"><?= date('Y-m-d', $val['created_at']) ?></span>  <span class="data2"><?= date('H:i:s', $val['created_at']) ?></span></p></div>
                </div>
                <div class="col-xs-1"></div>
            </div>
        <?php endforeach; ?>
        <div class="load"></div>
    <?php } else { ?>
        <div class="nodata" style="display:block;">暂无数据</div>
    <?php } ?>
</div>
