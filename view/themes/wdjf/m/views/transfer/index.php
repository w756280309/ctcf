<?php

$this->title = '授权失败';
$this->hideHeaderNav = true;
$referUrl = Yii::$app->request->referrer;
if (null !== $referUrl) {
    $this->registerMetaTag(['http-equiv' => 'refresh', 'content' => "3;url='$referUrl'"]);
}
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160331">
<div class="row" id="bind-box">
    <div class="col-xs-12">
        <img style="display: block;margin: 70px auto 0;width: 90px;" src="<?= ASSETS_BASE_URI ?>/images/transfer_err.png" alt="">
    </div>
    <div class="col-xs-12">
        <div style="margin-top: 30px">【<?= $code ?>】<?= $message ?></div>
    </div>
</div>
