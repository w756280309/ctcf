<?php
$this->showBottomNav = true;
$this->title = '实名认证';

$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => \yii\web\JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'libs/fastclick.js', ['depends' => \yii\web\JqueryAsset::class, 'position' => 1]);
$this->registerJs('forceReload_V2();');
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/guide-realName/css/index.css ">
<style>
    body{background-color:#fff}.section-one{padding:.66666667rem 2.66666667% 1.2rem}.section-one img{width:2.13333333rem;display:block;margin:0 auto}.section-one h5{font-size:.48rem;color:#333;text-align:center;line-height:1;margin:.56rem 0}.section-one p{width:85%;font-size:.37333333rem;color:#333;line-height:.45333333rem;margin:0 auto}.section-two{padding:0 .26666667rem;overflow:hidden}.section-two ul{padding:0 .8rem;width:100%;border:1px solid #ededed;box-shadow:1px 1px 5px #eaeaea;margin-bottom:.26666667rem;border-radius:.13333333rem}.section-two ul li{padding:1rem 0;line-height:.58666667rem;font-size:.48rem;color:#999}.section-two ul li img{width:.58666667rem;margin:0 .73333333rem 0 .36rem;position:relative;top:-.05rem;left:0}.section-two ul li span.rg{display:block;width:45%}.section-two ul li:first-child{color:#f35e52;border-bottom:1px solid #999}.go-realName{display:block;text-align:center;border-radius:.13333333rem;color:#fff;font-size:.50666667rem;line-height:1.17333333rem;background-color:#f35d52;height:1.17333333rem;width:100%}.tips{color:#999;font-size:.37333333rem;line-height:1rem}.go-out{text-decoration:underline;font-size:.37333333rem;color:#f35d52;line-height:1;text-align:center;margin-bottom:.8rem}.popover{display:none;width:80%;position:absolute;top:50%;left:50%;z-index:100001;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);border-radius:.35rem;height:5.06666667rem;background-color:#fff}.popover p{padding:1.33333333rem 1rem;color:#999;font-size:.37333333rem;line-height:.53333333rem}.popover dl{position:absolute;bottom:0;left:0;height:1.36rem;line-height:1.33333333rem;width:100%;border-top:.02666667rem solid #ddd;font-size:.42666667rem;color:#333;text-align:center}.popover dt{width:50%}.popover dd{width:50%;color:#f03745;border-left:1px solid #ddd}
</style>
<div class="flex-content">
    <div class="section-one">
        <img src="<?= FE_BASE_URI ?>wap/guide-realName/images/real_name.png" alt="">
        <h5>实名认证</h5>
        <p>平台积极响应金融监管对用户投资的安全性要求，您在使用本产品时，请先进行实名认证哦。</p>
    </div>

    <div class="section-two">
        <ul>
            <li>
                <img src="<?= FE_BASE_URI ?>wap/guide-realName/images/checked.png" alt="">
                <span>第1步</span>
                <span class="rg">注册登录成功</span>
            </li>
            <li>
                <img src="<?= FE_BASE_URI ?>wap/guide-realName/images/unchecked.png" alt="">
                <span>第2步</span>
                <span class="rg">实名认证</span>
            </li>
        </ul>
        <a class="go-realName" href="/user/identity">立即实名认证</a>
        <p class="tips">温都金服将严格保密您的个人信息。</p>
    </div>
</div>
<div class="mask" style="display: none;"></div>
<div class="popover">
    <p>未完成实名认证将无法使用该产品， 确定要放弃吗？</p>
    <dl class="clearfix">
        <dt class="lf cancel">取消</dt>
        <dd class="rg confirm">确定</dd>
    </dl>
</div>
<?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
    <div style="height: 50px;"></div>
    <?= $this->renderFile('@wap/views/layouts/footer.php')?>
<?php } ?>

<script>
    $(function(){
        FastClick.attach(document.body);
        $(".go-out").on("click",function(){
            $(".mask,.popover").fadeIn();
        })
        $(".cancel").on("click",function(){
            $(".mask,.popover").fadeOut();
        })
        $(".confirm").on("click",function(){
            window.location.href = "/user/user";//js控制页面跳转
            $(".mask,.popover").fadeOut();

        })
    })
</script>
