<?php

$this->title = '可用代金券';
$this->replaceUrl = Yii::$app->request->referrer;

$this->registerCssFile(ASSETS_BASE_URI . 'css/coupon.css?v=20170522', ['depends' => 'wap\assets\WapAsset']);

?>
<script type="text/javascript">
    var url = '/user/coupon/valid?sn=<?= $sn ?>&money=<?= $money ?>';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="container coupon">
    <?= $this->render('_valid_list', ['coupons' => $coupons, 'money' => $money, 'sn' => $sn]) ?>
    <div class="load"></div>
</div>