<?php
use yii\widgets\LinkPager;
?>
<?php $this->beginBlock('blockmain'); ?>

    <div class="container-fluid">
        <img src="<?= $ticket ?>" alt="">
    </div>
    <div class="container-fluid">
        <span style="color: red;font-size: large">公众号【渠道专用二维码】<br>
用户扫描关注并注册成功后，此用户算本渠道的业绩<br>请自行保存（鼠标右键，另存为）</span>
    </div>

<?php $this->endBlock(); ?>