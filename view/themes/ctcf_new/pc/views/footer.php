<?php
//use common\view\UdeskWebIMHelper;

//UdeskWebIMHelper::init($this);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/footer.min.css', ['depends' => 'frontend\assets\CtcfFrontAsset']);
?>

<div class="ctcf-footer">
    <div class="footer-top">
        <div class="ctcf-container clear-fix">
            <dl class="about-ct fz14 lf">
                <dt class="fz16">关于楚天财富</dt>
                <dd><a class="footer-hover" href="/helpcenter/about/">关于我们</a></dd>
                <dd><a class="footer-hover" href="/helpcenter/advantage/">平台优势</a></dd>
                <dd><a class="footer-hover" href="/helpcenter/contact/">联系我们</a></dd>
                <dd><a class="footer-hover" href="/jobs/">加入我们</a></dd>
            </dl>
            <dl class="help-ct fz14 lf">
                <dt class="fz16">帮助中心</dt>
                <dd><a class="footer-hover" href="/helpcenter/operation/">注册登录</a></dd>
                <dd><a class="footer-hover" href="/helpcenter/operation?type=1">开通资金托管</a></dd>
                <dd><a class="footer-hover" href="/helpcenter/operation?type=2">绑卡充值</a></dd>
                <dd><a class="footer-hover" href="/helpcenter/operation?type=3">投资提现</a></dd>
            </dl>
            <div class="phone-service fz16 lf">
                <p class="phone-service-msg">客服电话</p>
                <p class="phone-service-number fz30"><?= Yii::$app->params['platform_info.contact_tel'] ?></p>
                <p class="phone-service-time" style="line-height:26px;">(周一到周日 9:00-20:00)</p>
            </div>
            <ul class="clear-fix rg">
                <li class="lf">
                    <img src="<?= ASSETS_BASE_URI ?>ctcf/images/app_download.png" alt="">
                    <span class="fz14">下载APP</span>
                </li>
                <li class="lf">
                    <img src="<?= ASSETS_BASE_URI ?>ctcf/images/weixin_follow.png" alt="">
                    <span class="fz14">官方微信</span>
                </li>
              <!--  <li class="rg">
                    <img src="<?/*= ASSETS_BASE_URI */?>ctcf/images/weibo@2x.png" alt="">
                    <span class="fz14">官方微博</span>
                </li>-->
            </ul>
        </div>
    </div>
    <div class="footer-bottom ctcf-container fz14">
        <p>楚天财富（武汉）金融服务有限公司&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层</p>
        <p>鄂ICP备15002057号-1</p>
    </div>
</div>

