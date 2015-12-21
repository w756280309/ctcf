<?php
use yii\widgets\LinkPager;
frontend\assets\WapAsset::register($this);
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<!--  账户中心页 start-->

    <div class="row  border-bottom  earning">
        <div class="row earn-tit">
            <div class="col-xs-1"></div>
            <div class="col-xs-5 col">资产总额 （元）</div>
            <div class="col-xs-6"></div>
        </div>
        <div class="row earn-num1">
            <div class="col-xs-1"></div>
            <div class="col-xs-7 col"><?=$zcze?></div>
            <div class="col-xs-3"></div>
        </div>
        <div class="row earn-tit">
            <div class="col-xs-1"></div>
            <div class="col-xs-4 col">累计收益 （元）</div>
            <div class="col-xs-5 col">可用余额 （元）</div>
        </div>
        <div class="row earn-num2">
            <div class="col-xs-1"></div>
            <div class="col-xs-4 col"><?=$ljsy?></div>
            <div class="col-xs-5 col"><?=$ua->available_balance?></div>
        </div>
    </div>
    <div class="row">
        <div class="row border-bottom account">
            <div class="col-xs-6 rg-line" onclick="tixian()">提现</div>
            <div class="col-xs-6 " onclick="recharge()">充值</div>
        </div>
        <div class="clear"></div>
        <div class="row sm-height border-bottom margin-top" onclick="location.href='/user/user/myorder'">
            <div class="col-xs-3 left-txt">我的理财</div>
            <div class="col-xs-7"></div>
            <div class="col-xs-1 arrow">
                <a href="/user/user/myorder"><img src="/images/arrow.png" alt="右箭头"></a>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="clear"></div>
        <div class="row sm-height border-bottom" onclick="location.href='/system/system/safecenter'">
            <div class="col-xs-3 left-txt">安全中心</div>
            <div class="col-xs-7"></div>
            <div class="col-xs-1 arrow">
                <a href="/system/system/safecenter"><img src="/images/arrow.png" alt="右箭头"></a>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="clear"></div>
        <div class="row sm-height border-bottom" onclick="location.href='/user/user/mingxi'">
            <div class="col-xs-3 left-txt">交易明细</div>
            <div class="col-xs-7"></div>
            <div class="col-xs-1 arrow">
                <a href="/user/user/mingxi"><img src="/images/arrow.png" alt="右箭头"></a>
            </div>
            <div class="col-xs-1"></div>
        </div>
    </div>
    <form></form>
    <!-- 账户中心页 end  -->    
    
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript">
    function tixian(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if(err == '1') {
            toasturl(tourl,mess,0);
        }else{
            location.href='/user/userbank/tixian';
        }
    }
    
    function recharge(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if(err == '1') {
            toasturl(tourl,mess,1);
        }else{
            location.href='/user/userbank/recharge';
        }
    }
    
    function toasturl(url,val,type){
        $('.account div').eq(type).css("background","#e8eaf0");
        var kahao=$('<div class="error-info" style="display: block"><div>'+val+'</div></div>');
            $(kahao).insertAfter($('form'));
            setTimeout(function(){
                $(kahao).fadeOut();
                setTimeout(function(){
                    $(kahao).remove();
                    $('.account div').eq(type).css("background","#fff");
                    window.location.href = url;
                },200);
            },2000);
    }
        
    </script>