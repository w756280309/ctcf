<?php
$this->title = '双十一 抢红包';
?>
<link rel="stylesheet" type="text/css" href="<?= FE_BASE_URI ?>wap/campaigns/active20181111/css/main.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/index.min.css?v=1.4">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/window-box.min.css?v=1.3">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/window-box.min.css?v=1.3">

<div class="main-content" id="app" class="flex-content">
    <div class="section">
        <div class="section-tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-tit01.png">
        </div>
        <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken; ?>">
        <div class="section-main">
            <div class="packet">

                    <?php
                        if(!$isLoggedIn){
                            $strDisplay =  '请登录';
                        }else{
                            if(time()> strtotime('2018-11-16 23:59:59')){
                                $strDisplay = '抽奖活动已结束';
                            }else if(intval(date("H",time()))<10){
                        		$strDisplay = '今日活动还未开始,请耐心等待~';
                        	} else if(empty($arrKeys)){
                                $strDisplay = '今日红包已发放完,明天再来吧~';
                            }else if($isGet){
                                $strDisplay = '今日已抢过，明天再来吧~';
                            }else{
                                $strDisplay = $isClick;
                            }
                        }

                    ?>
                <?php if(true === $strDisplay){?>
                    <a href="javascript:void(0);" class="packet-btn" id="pressbtn"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-pressbtn.png"></a>
                <?php }else{?>
                    <p class="packet-btn packet-btn-tips" id="" style="display: ;"><?php echo $strDisplay; ?></p>
                <?php } ?>

                <p class="packet-btn packet-btn-tips" id="hadcode" style="display: none;">今日已抢过，明天再来吧~'</p>
            </div>
            <p class="packet-notice">活动期间，每日10:00开启，限量红包100个，抢完即止！</p>
            <div class="gift-settings">
                <p class="gift-tit"><span>//////</span>奖品设置<span>//////</span></p>
                <table border="0" cellspacing="0" cellpadding="0" class="gift-box">
                    <tr>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-gift01.png"></td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-gift02.png"></td>
                    </tr>
                    <tr>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-gift03.png"></td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-gift04.png"></td>
                    </tr>
                </table>
            </div>
            <?php if(count($awardlist) > 0 && $isLoggedIn):?>
                <a href="javascript:void(0);" class="side-btn" id="myGiftList">我<br/>的<br/>奖<br/>品</a>
            <?php endif;?>
        </div>
    </div>
    <div class="section">
        <div class="section-tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-tit02.png">
        </div>
        <div class="section-main cj-box">
            <p style="color: #fff;">活动期间，单笔出借金额进行逢万津贴<span>11元</span>的奖励，出借越多，津贴越多，上不封顶。</p>
            <div class="jl-tit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-tit04.png"></div>
            <p style="color: #ffffcc;">单笔出借180天的产品10万元<br>获得返现津贴为：11元*10=110元</p>
        </div>
        <a href="javascript:void(0);" class="cj-btn" id="nowloan">立即出借</a>
    </div>
    <div class="section">
        <div class="section-tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181111/images/img-tit03.png"/>
        </div>
        <div class="tips">
            <p>1、活动时间：2018.11.5-2018.11.16。</p>
            <p>2、活动期间，登录平台账户即可抢红包，每个用户  每日限抢一次。</p>
            <p>3、红包内的奖品为：111积分、11元现金、1.1%加息券（加息天数11天）、11元满减券（不限出借金额），凭手气随机获得。</p>
            <p>4、拆开红包后的奖品将实时发放到平台账户，在“账户中心”查看即可。</p>
            <p>5、出借津贴于活动结束后7个工作日内，客服联系核实后统一发放。</p>
            <p>6、本活动最终解释权归楚天财富（武汉）金融服务有限公司所有。</p>
            <!--<a class="tips-arrow">
                <img src="<?/*= FE_BASE_URI */?>wap/campaigns/active20181111/images/icon-arrow.png">
            </a>-->
        </div>
    </div>
</div>

<!--qianghongbao-->
<div class="alert-container" style="display: none;" id="getsuc">
    <div class="alert-content alert-packet">
        <p class="packet-detail">恭喜您获得<span id="setaward">111积分</span></p>
        <p class="packet-ts" style="color: #ff8c8c;">点击“我的奖品”即可查看</p>
    </div>
    <i class="icon-close"></i>
</div>
<!--qianghongbao-->
<div class="alert-container" style="display: none;">
    <div class="alert-content alert-packet">
        <p class="packet-detail">o(╥﹏╥)o<span>抢光了</span></p>
        <p class="packet-ts" style="color: #ffb7b7;">没关系，明天继续加油喔！</p>
    </div>
    <i class="icon-close"></i>
</div>
<!--list-->
<?php if($awardlist):?>
<div class="alert-container" style="display: none;" id="myGift">
    <div class="alert-content alert-giftlist">
        <div class="gift-list">
            <div class="list-detail">
                <table border="0" cellspacing="0" cellpadding="0">
                    <?php foreach($awardlist as $value):?>
                    <tr>
                        <td><?php echo $value['sname'];?></td>
                        <td><?php echo $value['updatetime'];?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
        </div>
    </div>
    <i class="icon-close"></i>
</div>
<?php endif;?>
<script>
    $(function () {
        var csrf = $('input[name=_csrf]').val();
        var awardCnt = '<?php echo count($awardlist);?>';
        var isLoggedIn = "<?php echo $isLoggedIn?>";
        if(!isLoggedIn){
            setTimeout(function () {
                location.href='/site/login?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>';
            }, 2000);
        }
        $("#myGiftList, #nowloan, #pressbtn").on("click",function(){
            switch($(this).attr('id')){
                case 'pressbtn'://抢购
                    var purl = '/promotion/p181111/openactive';
                    $.post(
                        purl,
                        {
                            _csrf:csrf
                        },
                        function (data) {
                            if(data.code == 200){
                                $('#setaward').html(data.data.promotype);
                                $('#getsuc').show();
                            }else if(data.code == 201){
                                alert('未登录');
                                $('#getsuc').hide();
                                location.href='/site/login';
                            }else if(data.code == 203) {
                                $('#hadcode').show();
                            }else{
                                $('#getsuc').hide();
                                alert(data.message);
                                window.location.reload();
                            }
                        }
                    );
                    break;
                case  'myGiftList'://我的奖品
                    if(awardCnt != '0'){
                        $('#myGift').show();
                    }else{
                        alert('暂时没有抽到奖品!');
                    }
                    break;
                case 'nowloan'://立即出借
                    location.href='/deal/deal/index';
                    break;
            }
        });
        //关闭按钮
        $('.icon-close').click(function(){
            $(this).parent('.alert-container').hide();
            window.location.reload();
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