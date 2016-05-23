<?php
$this->title = '大河-账户中心';
$this->showBottomNav = true;
$this->showAvatar = true;
?>
<link href="<?= ASSETS_BASE_URI ?>css/informationAndHelp.css" rel="stylesheet">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
<script>
    $(function(){
        $('.footer-inner a').css({color: '#8c8c8c'});
        $('.footer-inner2 a').css({color: '#f44336'});
        $('.footer-inner1 a').css({color: '#8c8c8c'});
        $('.footer-inner1 .licai').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -113px -3px',backgroundSize: '200px'});
        $('.footer-inner2 .zhanghu').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -81px -57px',backgroundSize: '200px'});
        $('.footer-inner .shouye').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -145px -3px',backgroundSize: '200px'});
    })
</script>

    <!--  账户中心页 start-->
    <div class="row  border-bottom  earning accountcenter">
        <div class="row earn-tit">
            <div class="col-xs-1"></div>
            <div class="col-xs-5 col">资产总额 （元）</div>
            <div class="col-xs-6"></div>
        </div>
        <div class="row earn-num1">
            <div class="col-xs-1"></div>
            <div class="col-xs-7 col"><?= number_format($zcze, 2) ?></div>
            <div class="col-xs-3"></div>
        </div>
        <div class="row accountcenter-center-left">
            <div class="col-xs-1"></div>
            <div class="col-xs-5">累计收益（元）</div>
            <div class="col-xs-6">理财资产（元）</div>
        </div>
        <div class="row accountcenter-center-right">
            <div class="col-xs-1"></div>
            <div class="col-xs-5"><?= number_format($ljsy, 2) ?></div>
            <div class="col-xs-6"><?= number_format($dhsbj, 2) ?></div>
        </div>
    </div>
    <div class="row accountcenter-other">
        <div class="row border-bottom accountcenter-bottom">
            <div class="col-xs-1"></div>
            <div class="col-xs-5">
                    <p>可用余额（元）</p>
                    <p class="unmber_remain"><?= number_format($ua->available_balance, 2) ?></p>
            </div>
            <div class="col-xs-3 addcash" onclick="recharge()">充值</div>
            <div class="col-xs-3 rg-line drawcash" onclick="tixian()">提现</div>
        </div>
        <div class="clear"></div>
        <a class="row sm-height border-bottom margin-top block" href="/user/user/myorder" >
            <div class="col-xs-10 left-txt">我的理财</div>
            <div class="col-xs-1 arrow">
                <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
            </div>
            <div class="col-xs-1"></div>
        </a>

        <div class="clear"></div>
        <a class="row sm-height border-bottom block" href="/user/user/mingxi" >
            <div class="col-xs-10 left-txt">交易明细</div>
            <div class="col-xs-1 arrow">
                <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
            </div>
            <div class="col-xs-1"></div>
        </a>
    </div>
    <form></form>
    <!-- 账户中心页 end  -->

    <script type="text/javascript">
    function tixian(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if(err === '1') {
            $('.account div').eq(0).css("background","#e8eaf0");
            toast(mess, function() {
                $('.account div').eq(0).css("background","#fff");
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        }else{
            location.href='/user/userbank/tixian';
        }
    }

    function recharge(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if(err === '1') {
            $('.account div').eq(1).css("background","#e8eaf0");
            toast(mess, function() {
                $('.account div').eq(1).css("background","#fff");
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        }else{
            location.href='/user/userbank/recharge';
        }
    }
    </script>
