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
                        <img src="<?= ASSETS_BASE_URI ?>images/ctcf/location.png">
                    </div>
                    <p>公司地址 : 武汉市东湖路181号楚天文化创意产业园8号楼1层</p>
                    <p>客服电话 : <?= \Yii::$app->params['platform_info.contact_tel'] ?></p>
                    <p>客服时间 : <?= Yii::$app->params['platform_info.customer_service_time'] ?>（周一至周日，假日例外）</p>
                    <p>客服QQ  : 3227992346</p>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

