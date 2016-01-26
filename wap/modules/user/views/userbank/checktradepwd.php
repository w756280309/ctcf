<?php
$this->title="交易密码";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/jiaoyimima.css"/>

    <!--可用金额-->
    <div class="row kahao tixian">
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-3 col-sm-1" style="padding-right: 0">提现金额:</div>
        <div class="col-xs-9 col-sm-7"><?= $money?$money:"0" ?>元</div>
        <div class="hidden-xs col-sm-1"></div>
    </div>
    <!--充值金额-->
    <form method="post" class="cmxform" id="form" action="/user/userbank/checktradepwd?money=<?= $money ?>" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input name="money" type="hidden" value="<?= $money ?>">
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1" style="padding: 0;padding-left: 15px;">交易密码</div>
            <div class="col-xs-9 col-sm-8"><input type="password" id="password" name="EditpassForm[password]" placeholder="请输入交易密码" maxlength="6"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <input type="text" name="" style="display:none"/>
        <!--限额提醒-->
        <div class="row dan">
            <div class="col-xs-10">
                <span>客服联系电话：400-800-8888</span>
            </div>
        </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="checktrade" class="btn-common btn-normal" name="signUp" type="button" value="确认" >
            </div>
            <div class="col-xs-3"></div>
        </div>
        <!-- 提现弹出框 start  -->
        <div class="mask"></div>
        <div class="succeed-info hidden">
            <div class="col-xs-12"><img src="/images/succeed.png" alt="对钩"> </div>
            <div class="col-xs-12">提现成功</div>
        </div>
        <!-- 提现弹出框 end  -->
    </form>
    <script type="text/javascript">
    var csrf;
    $(function(){
       var err = '<?= $data['code'] ?>';
       var mess = '<?= $data['message'] ?>';
       var tourl = '<?= $data['tourl'] ?>';
       if(err == '1') {
           toasturl(tourl,mess);
       }
        
       csrf = $("meta[name=csrf-token]").attr('content');
       $('#checktrade').bind('click',function(){
           $(this).addClass("btn-press").removeClass("btn-normal");
           if($('#password').val()==''){
               toast(this,'交易密码不能为空');
               $(this).removeClass("btn-press").addClass("btn-normal");
               return false;
           }
           if($('#password').val().length!=6){
               toast(this,'交易密码必须包含6个数字');
               $(this).removeClass("btn-press").addClass("btn-normal");
               return false;
           }
           subForm("#form", "#checktrade");
           $(this).removeClass("btn-press").addClass("btn-normal");
       });

    })    
    </script>

    