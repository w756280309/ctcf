<?php
$this->registerCssFile(ASSETS_BASE_URI.'css/footer.css', ['depends' => 'frontend\assets\FrontAsset']);
?>
<div class="section section5 fp-auto-height fp-section fp-table">
    <div class="five-box" style="height: 200px">
        <div class="five-address">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层</div>
        <div class="five-tel">客服电话：<span><?= Yii::$app->params['contact_tel'] ?></span><span style="padding-left: 8px;margin-right: 8px;">客服QQ：1430843929</span>工作时间：9:00-17:00（周一至周六）</div>
        <div class="five-partner clearfix"><i>合作伙伴：</i>
            <ul>
                <li style="border-left:0;padding-left: 0;">温州日报</li>
                <li>温州商报</li>
                <li>温州都市报</li>
                <li>温州晚报</li>
                <li>科技金融时报</li>
                <li>温州网</li>
                <li>温州人杂志</li>
                <li style="border-left:0;padding-left: 0;">南京金融资产交易中心</li>
                <li>同信证券</li>
            </ul>
        </div>
        <div class="five-copyright">Copyright ©温州温都金融信息服务有限公司 浙ICP备16003187号-1</div>
        <div class="first-ma">
            <img src="<?= ASSETS_BASE_URI ?>images/ma.png" alt="">
            <div>访问手机wap版</div>
        </div>
    </div>
</div>