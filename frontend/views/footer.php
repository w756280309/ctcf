<?php
$this->registerCssFile(ASSETS_BASE_URI.'css/footer.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/clipboard.min.js', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div class="footer-section footer-section5 footer-fp-auto-height footer-fp-section footer-fp-table">
    <div class="footer-five-box" style="height: 200px">
        <div class="footer-five-address">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层</div>
        <div class="footer-five-tel">客服电话：<span><?= Yii::$app->params['contact_tel'] ?></span><span style="padding-left: 8px;margin-right: 8px;">客服QQ：1430843929</span>客服时间：8:30-20:00（周一至周日）</div>
        <div class="footer-five-partner footer-clearfix"><i>合作伙伴：</i>
            <ul>
                <li style="border-left: 0;padding-left: 0;">温州日报</li>
                <li>温州晚报</li>
                <li>温州都市报</li>
                <li>温州商报</li>
                <li>科技金融时报</li>
                <li>温州网</li>
                <li>温州人杂志</li>
                <li style="border-left:0;padding-left: 0;">南京金融资产交易中心</li>
                <li>东方财富证券</li>
                <li>联动优势</li>
                <li>电子数据保全中心</li>
            </ul>
        </div>
        <div class="footer-five-copyright">Copyright ©温州温都金融信息服务股份有限公司 浙ICP备16003187号-1 浙公网安备 33030202000311号</div>
        <div class="footer-first-ma">
            <img src="<?= ASSETS_BASE_URI ?>images/ma-new.png" alt="">
            <div>访问手机wap版</div>
        </div>
    </div>
</div>
