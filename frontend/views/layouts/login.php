<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <meta name="baidu-site-verification" content="7YkufMc2UW" />
        <link rel="stylesheet" href="/css/index.css">
        <!--[if lt IE 9]>
        <style>
            .section1 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section2 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section3 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section4 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
        </style>
        <![endif]-->
    </head>

    <body>
        <?php $this->beginBody() ?>
        <div id="top-box">
            <div class="top-box">
                <a href="/"><img class="logo" src="/images/logo.png"/></a>
                <div class="top-right">
                    <?php if (Yii::$app->user->isGuest) { ?>
                    <div class="top-resign"><a href="/site/login?flag=reg">注册</a></div>
                    <div class="top-login"><a href="/site/login">登录</a><span>大额充值通道</span></div>
                    <?php } else { ?>
                    <div class="top-resign"><a href="/user/useraccount/accountcenter">我的账户</a></div>
                    <div class="top-login"><a href="javascript:void(0)" onclick="$('#logout').submit();">安全退出</a><span>大额充值通道</span></div>
                    <form method="post" id="logout" action="/site/logout">
                        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?= $content ?>
        <div class="section section5 fp-auto-height fp-section fp-table">
            <div class="five-box" style="height: 200px;">
                <div class="five-address">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层</div>
                <div class="five-tel">客服电话：<span><?= Yii::$app->params['contact_tel'] ?></span><span style="padding-left: 8px;margin-right: 8px;">客服QQ：1430843929</span>工作时间：9:00-17:00（周一至周六）</div>
                <div class="five-partner">合作伙伴：温州日报<span style="margin-left: 8px">温州商报</span><span>温州都市报</span><span>温州晚报</span><span>科技金融时报</span><span>温州网</span><span>温州人杂志</span></div>
                <div class="five-partner five-partners"><span style="border: 0">南京金融资产交易中心</span><span>同信证券</span></div>
                <div class="five-copyright">Copyright ©温州温都金融信息服务有限公司 浙ICP备16003187号-1</div>
                <div class="first-ma">
                    <img src="/images/ma.png" alt="">
                    <div>扫码进入温都金服</div>
                </div>
            </div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
