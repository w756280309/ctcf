<?php
$this->title="帮助中心";
$this->registerJsFile('/js/helpcenter.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link href="/css/informationAndHelp.css" rel="stylesheet">

<div class="container bootstrap-common helpcenter">
        <!-- 主体 -->
        <div class="row">
                <div class="col-xs-12">
                        <p class="header"><span>—————</span>&nbsp;常见问题&nbsp;<span>—————</span></p>
                </div>
        </div>
        <div class="kong-width">
                <!--注册登录-->
                <a href="/site/help?type=1">
                        <div class="row">
                                <div class="col-xs-12">
                                    <img src="/images/system/back_1.png" width="100%" alt="">
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img id="img_height" src="/images/system/back_2.png" width="100%" alt="">
                                </div>
                                <div class="common_single">
                                        <div class="mask-center">
                                                注册登录篇
                                                <img class="back_icon" src="/images/system/back_icon.png" height="100%"
                                                     alt="">
                                        </div>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_3.png" width="100%" alt="">
                                </div>
                        </div>
                </a>
                <!--绑卡充值-->
                <a href="/site/help?type=2">
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_4.png" width="100%" alt="">
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img id="img_height" src="/images/system/back_5.png" width="100%" alt="">
                                </div>
                                <div class="common_single">
                                        <div class="mask-center">
                                                绑卡充值篇
                                                <img class="back_icon" src="/images/system/back_icon.png" height="100%"
                                                     alt="">
                                        </div>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_6.png" width="100%" alt="">
                                </div>
                        </div>
                </a>
                <!--投资提现-->
                <a href="/site/help?type=3">
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_7.png" width="100%" alt="">
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img id="img_height" src="/images/system/back_8.png" width="100%" alt="">
                                </div>
                                <div class="common_single">
                                        <div class="mask-center">
                                                投资/提现篇
                                                <img class="back_icon" src="/images/system/back_icon.png" height="100%"
                                                     alt="">
                                        </div>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_9.png" width="100%" alt="">
                                </div>
                        </div>
                </a>
                <!--名词解释-->
                <a href="/site/help?type=4">
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_10.png" width="100%" alt="">
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img id="img_height" src="/images/system/back_11.png" width="100%" alt="">
                                </div>
                                <div class="common_single">
                                        <div class="mask-center">
                                                名词解释篇
                                                <img class="back_icon" src="/images/system/back_icon.png" height="100%"
                                                     alt="">
                                        </div>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12">
                                        <img src="/images/system/back_12.png" width="100%" alt="">
                                </div>
                        </div>
                </a>
        </div>
</div>