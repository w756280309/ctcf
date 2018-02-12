<?php

$this->title = '好友召集令';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171221/css/call-help.css?v=1.21111">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<body>
<div class="flex-content" id="app">
    <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <input type="hidden" name="inv" value="<?= \yii\helpers\Html::encode($inv) ?>">
    <div class="msg-help clearfix">
        <p class="msg-help-character">快来为我助力，即可限时领取<span>专属超级福利</span>！我在这里等你！</p>
        <img src="<?= $headImgUrl ?>" alt="邀请人" title="邀请人" class="person-icon">
    </div>
    <ol>
        <li><p>1、注册即领<span>698元</span>红包</p></li>
        <li><p>2、首投任意金额领<span>50元</span>超市卡</p></li>
    </ol>
    <p class="msg-tips">友情提示：必须通过微信端注册，才能助力成功哦！</p>

    <div class="get-friend-btn">马上去助力</div>
</div>
<script>
    $(function(){
        FastClick.attach(document.body);

        //点击助力按钮根据返回值去做跳转
        var that = this;
        var csrf = $('input[name=_csrf]').val();
        var inv= $('input[name=inv]').val();

        $('.get-friend-btn').on("click",function(){
            var _that = $(this);
            if (_that.hasClass('alreadyClicked')) {
                return false;
            }
            _that.addClass('alreadyClicked');
            var xhr = $.post('/promotion/p171228/do',{inv: inv, _csrf:csrf});
            xhr.done(function(res){
                _that.removeClass('alreadyClicked');
                if(res.code === 0){
                  // toastCenter('助力成功');
                  location.href='/site/signup?inviteCode='+inv;
                }
            });
            xhr.fail(function(jqXHR){
                _that.removeClass('alreadyClicked');
                var resp = $.parseJSON(jqXHR.responseText);
                if(resp.code===6){
                    toastCenter('系统错误！');
                }else if(resp.code===8){
                    location.href="/promotion/p171228/";
                }else if(resp.code===9){
                    toastCenter('不能助力自己！');
                }else if(resp.code===10){
                    toastCenter('只能在微信上才能为好友助力哦！');
                }
            });

        })
    })

    //弹窗组件
    function toastCenter(val, active) {
        var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: .4rem;"><div>' + val + '</div></div>');
        $('body').append($alert);
        $alert.find('div').width($alert.width());
        setTimeout(function () {
            $alert.fadeOut();
            setTimeout(function () {
                $alert.remove();
            }, 200);
            if (active) {
                active();
            }
        }, 2000);
    };
</script>
