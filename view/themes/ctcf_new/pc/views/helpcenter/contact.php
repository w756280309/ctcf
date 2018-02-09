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
                        <img style="width: 664px;"  src="<?= ASSETS_BASE_URI ?>ctcf/images/help/location.png">
                    </div>
                    <p>公司地址 : 武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层</p>
                    <p>工作时间 : 9:00-17:30（周一至周五）</p>
                    <p>客服电话 : <?= \Yii::$app->params['platform_info.contact_tel'] ?></p>
                    <p>客服时间 : 9:00-20:00（周一至周日，节假日例外）</p>
<!--                    <p>客服QQ  : 474574526</p>-->
<!--                    <p>&nbsp;</p>-->
<!--                    <p>保丰门店 : 温州市鹿城区飞霞南路657号保丰大楼一层</p>-->
<!--                    <p>工作时间 : 8:30-17:30（周一至周日）</p>-->
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

