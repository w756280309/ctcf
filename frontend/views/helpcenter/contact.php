<?php

$this->title = '联系我们';
$this->registerCssFile(ASSETS_BASE_URI.'css/help/contact.css', ['depends' => 'frontend\assets\FrontAsset']);

?>

<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="contact-box">
                <div class="contact-header">
                    <span class="contact-header-font">联系我们</span>
                </div>
                <div class="contact-content">
                    <div class="location">
                        <img  src="../../images/help/location.png">
                    </div>
                    <p>公司地址 : 温州市鹿城区飞霞南路657号保丰大楼四层</p>
                    <p>工作时间 : 9:00-17:00（周一至周六）</p>
                    <p>客服电话 : <?= \Yii::$app->params['contact_tel'] ?></p>
                    <p>客服时间 : 9:00-20:00（周一至周日，假日例外）</p>
                    <p>客服QQ  : 1430843929</p>
                    <p>&nbsp;</p>
                    <p>保丰门店 : 温州市鹿城区飞霞南路657号保丰大楼一层</p>
                    <p>温都门店 : 温州市鹿城区公园路105号</p>
                    <p>工作时间 : 9:00-17:00（周一至周六）</p>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

