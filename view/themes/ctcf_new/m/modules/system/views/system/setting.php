<?php

$this->title = "系统设置";
$this->backUrl = '/user/user';

$mobile = $model->mobile;
$muri = ASSETS_BASE_URI;
?>
<link rel="stylesheet" href="<?= $muri ?>ctcf/css/setting.css?v=1.0">
<script src="/js/jquery.min.js"></script>
<script src="/layer/layer.js"></script>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 23px;
    }

    .switch input {display:none;}

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 5px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<div class="row">
    <div class="row bg-height border-bottom">
        <div class="col-xs-4 left-txt">用户头像</div>
        <div class="col-xs-6 " style="color: #aab2bd;">
            ID:<?= $mobile ?>
        </div>
        <div class="col-xs-2 headpic">
            <img class="headpic-img-box" src="<?= $muri ?>images/headpic.png" width="40px" hieght="40px" alt="头像">
        </div>
    </div>
    <div class="row sm-height border-bottom">
        <div class="col-xs-4 left-txt">绑定手机</div>
        <div class="col-xs-2"></div>
        <div class="col-xs-5 left-txt"><?= substr_replace($mobile,'****',3,-4) ?></div>
        <div class="hidden-xs col-sm-1 "></div>
    </div>
    <a class="row sm-height border-bottom margin-top block" href="#">
        <div class="col-xs-4 col-sm-3 left-txt">微信绑定</div>
        <div class="col-xs-6 col-sm-7"></div>
        <div class="col-xs-7 arrow bind" sign="<?= !is_null($social) ? 1 : 0 ?>" style="padding-right: 0;">
            <?= !is_null($social) ? '<span style="color:grey;float: right;">(点击解除绑定)已绑定</span>' : '<span style="color:#419bf9;float: right;">去绑定</span>' ?>
        </div>
        <div class="col-xs-1 col-sm-1 "></div>
    </a>
    <?php if(!is_null($social)) : ?>
        <a class="row sm-height border-bottom block" href="#">
            <div class="col-xs-5 col-sm-3 left-txt">微信自动登录</div>
            <div class="col-xs-6 col-sm-7"></div>
            <label class="switch" style="float: right;vertical-align: middle;top: 25%;right: 8.33%;">
                <input class="automatic" type="checkbox" <?php if($social->isAutoLogin) echo 'checked'; ?>>
                <span class="slider round"></span>
            </label>
            <div class="col-xs-1 col-sm-1 "></div>
        </a>
    <?php endif;?>
    <a class="row sm-height border-bottom margin-top block" href="/system/system/safecenter">
        <div class="col-xs-4 col-sm-3 left-txt">安全中心</div>
        <div class="col-xs-6 col-sm-7"></div>
        <div class="col-xs-1 col-sm-1 arrow">
           <img src="<?=$muri ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1 col-sm-1 "></div>
    </a>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    $('.bind').on('click', function(){
        var sign = $(this).attr('sign');
        if (sign == 1) {
            var url = '/user/wechat/do-unbind';
            layer.open({
                content: '解绑后将收不到微信提醒,确定要解绑吗？'
                ,btn: ['确定', '取消']
                ,no: function(index){
                    layer.close(index);
                    return false;
                }
                ,yes:function (index) {
                    layer.close(index);
                    bind(url)
                }
            });
        } else {
            var url = '/user/wechat/direct-bind';
            bind(url)
        }
    })
    function bind(url) {
        $.ajax({
            url: url,
            type: "get",
            async: true,
            success: function (data) {
                toastCenter(data.message);
                window.location.reload();
            },
            error: function (jqXHR) {
                toastCenter(jqXHR.responseJSON.message)
            }
        });
    }
    //取消微信自动登录
    $('.automatic').on('click', function(){
        $.ajax({
            url: '/user/wechat/auto-login',
            type: "get",
            async: false,
            success: function (data) {
                //window.location.reload();
                toastCenter(data.message);
            },
            error: function (jqXHR) {
                toastCenter(jqXHR.responseJSON.message)
            }
        });
    })
</script>
<!-- 系统设置 end  -->