<?php
frontend\assets\WapAsset::register($this);
$this->registerJsFile(ASSETS_BASE_URI . 'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link href="<?= ASSETS_BASE_URI ?>css/informationAndHelp.css" rel="stylesheet">
<!--  账户中心页 start-->

    <div class="row  border-bottom  earning accountcenter">
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
        <div class="row accountcenter-center-left">
            <div class="col-xs-1"></div>
            <div class="col-xs-5">累计收益（元）</div>
            <div class="col-xs-6">理财资产（元）</div>
        </div>
        <div class="row accountcenter-center-right">
            <div class="col-xs-1"></div>
            <div class="col-xs-5"><?=$ljsy?></div>
            <div class="col-xs-6"><?= $dhsbj ?></div>
        </div>
    </div>
    <div class="row accountcenter-other">
        <div class="row border-bottom accountcenter-bottom">
            <div class="col-xs-1"></div>
            <div class="col-xs-5">
                    <p>可用余额（元）</p>
                    <p class="unmber_remain"><?=$ua->available_balance?></p>
            </div>
            <div class="col-xs-3 addcash" onclick="recharge()">充值</div>
            <div class="col-xs-3 rg-line drawcash" onclick="tixian()">提现</div>
        </div>
        <div class="clear"></div>
        <div class="row sm-height border-bottom margin-top" onclick="location.href='/user/user/myorder'">
            <div class="col-xs-3 left-txt">我的理财</div>
            <div class="col-xs-7"></div>
            <div class="col-xs-1 arrow">
                <a href="/user/user/myorder"><img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头"></a>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="clear"></div>
        <div class="row sm-height border-bottom" onclick="location.href='/user/user/mingxi'">
            <div class="col-xs-3 left-txt">交易明细</div>
            <div class="col-xs-7"></div>
            <div class="col-xs-1 arrow">
                <a href="/user/user/mingxi"><img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头"></a>
            </div>
            <div class="col-xs-1"></div>
        </div>
    </div>
    <form></form>
    <!-- 账户中心页 end  -->

    <script type="text/javascript" src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
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
