<?php

$this->title = '奖励金天天领';
$this->headerNavOn = true;
$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);

$btnStatusClass = '';
if (1 === $promoStatus) {
    $btnStatusClass = 'btn_04';
} elseif (2 === $promoStatus) {
    $btnStatusClass =  'btn_05';
} else {
    if (null === $user) {
        $btnStatusClass =  'btn_02';
    }
}

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170731/css/index.css?v=1.8">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js?v=1.0"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2.0"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script>function award(){};</script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/js/index.js?v=1.1"></script>
<div class="flex-content">
    <div class="banner">
        <img class="top-banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/banner.png" alt="">
        <p class="start-time">2017年8月10日-8月16日</p>
        <p class="gift-des">每天领100元奖励金</p>
        <span class="regular <?= $btnStatusClass ?>">活动<br>规则</span>
    </div>
    <div class="gifts-task">
        <ul class="clearfix">
            <li class="lf marginrg25">
                <p class="task-top <?php if ($taskStatus['checkInFinished']) { ?>complete<?php } ?>"><em class="paddingtop50">签到</em></p>
                <p class="task-mid"><em><span>额外5</span><br>积分</em></p>
                <a  class="task-bot <?= $btnStatusClass ?>" href="/user/checkin"><em>去领取</em></a>
            </li>
            <li class="lf marginrg25">
                <p class="task-top <?php if ($taskStatus['investFinished']) { ?>complete<?php } ?>"><em>每年化投资5万<br>得10元现金<br><span style="color:#fff;">已投<?= rtrim(rtrim(bcdiv($todayAnnualInvest, 10000, 1), '0'), '.') ?>万元</span></em></p>
                <p class="task-mid"><em><span>最高100元</span><br>现金红包</em></p>
                <a  class="task-bot <?= $btnStatusClass ?>" href="/deal/deal/index"><em>去理财</em></a>
            </li>
            <li class="lf">
                <p class="task-top <?php if ($taskStatus['inviteFinished']) { ?>complete<?php } ?>"><em>邀请好友首投<br>得500积分<br><span style="color:#fff;">已邀请<?= $invitePeopleCount ?>位</span></em></p>
                <p class="task-mid"><em><span>最高1500</span><br>积分</em></p>
                <a  class="task-bot <?= $btnStatusClass ?>" href="/user/invite"><em>去邀请</em></a>
            </li>
        </ul>
        <p class="tips">注：单日年化投资每累计5万元，可获得10元现金红包；每天最高可获得100元（即单日年化投资50万元）。</p>
    </div>
    <div class="draw-gifts">
        <div class="title-bg">
            <p>每天完成所有任务抽苹果7</p>
            <span class="prizes <?= $btnStatusClass ?>">我的<br>奖品</span>
            <div class="draw-tips">以上三个任务（签到、投资、邀请）各完成1次即可抽奖。</div>
        </div>
        <div class="draw-bg" id="prz_pool">
            <table class="clearfix" id="prize_pool">
                <tr>
                    <td class="lf lottery-unit-1 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_00.png" alt=""></td>
                    <td class="lf lottery-unit-2 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_01.png" alt=""></td>
                    <td class="lf lottery-unit-3 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_02.png" alt=""></td>
                </tr>
                <tr>
                    <td class="lf lottery-unit-8 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_07.png" alt=""></td>
                    <td class="lf lottery-unit"><img id="choujiang-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/btn.png" alt=""></td>
                    <td class="lf lottery-unit-4 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_03.png" alt=""></td>
                </tr>
                <tr>
                    <td class="lf lottery-unit-7 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_06.png" alt=""></td>
                    <td class="lf lottery-unit-6 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_05.png" alt=""></td>
                    <td class="lf lottery-unit-5 lottery-unit"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/gifts_04.png" alt=""></td>
                </tr>
            </table>
        </div>
        <p class="day-clear">所有数据当天清零，别浪费哟！</p>
    </div>
    <div class="footer-bg">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/footer_bg.png" alt="">
        <a href="/user/invite" class="invite <?= $btnStatusClass ?>">邀请好友一起中奖</a>
    </div>
</div>


<!--活动规则-->
<div class="mask-regular">
    <div class="active-regular">
        <h5>活动规则</h5>
        <img class="close-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/close.png" alt="">
        <ol>
            <li>活动时间2017年8月10日-8月16日；</li>
            <li>本次活动面向所有楚天财富注册用户；</li>
            <li>
                <p>活动期间每日完成指定任务，获得对应礼品；</p>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>签到</td>
                        <td>5积分</td>
                    </tr>
                    <tr>
                        <td>累计年化投资5万元</td>
                        <td>10元现金红包<br>(最高100元)</td>
                    </tr>
                    <tr>
                        <td>邀请好友首次投资</td>
                        <td>500积分<br>(最高1500积分)</td>
                    </tr>
                </table>
            </li>
            <li>活动期间单日年化投资每累计5万元，即可获得10元现金红包；每日最高可获得100元现金红包（单日累计年化投资50万元）；</li>
            <li>被邀请人活动期间完成首次投资，邀请人才能获得积分奖励，每日最多获得3次；</li>
            <li>活动期间每日完成所有任务（签到、投资、邀请任务各完成1次），获得1次免费抽奖机会；</li>
            <li>活动期间，现金红包及积分奖励将即时发放到您的账户；实物礼品将在活动结束后的7个工作日内发放，请保持通讯畅通。</li>
        </ol>
        <p class="regular-tips">注：年化投资金额=投资金额*项目期限/365</p>
    </div>
</div>

<!--我的奖品-->
<div class="prizes-box">
    <div class="outer-box">
        <img class="pop_close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png" alt="">

        <div class="prizes-pomp">
            <p class="prizes-title">我的奖品</p>
            <div id="wrapper">
                <ul>
                    <!--没有奖品-->
                    <?php if (empty($rewardList)) { ?>
                    <li class="no-prizes">您还没有获得奖品哦！</li>
                    <?php } else { ?>
                        <?php foreach ($rewardList as $ticket) { ?>
                            <?php if (!empty($ticket->reward)) { ?>
                            <li class="clearfix">
                                <div class="lf"><img src="<?= FE_BASE_URI.$ticket->reward->path ?>" alt="礼品"></div>
                                <div class="rg"><?= $ticket->reward->name ?></div>
                            </li>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.regular').on("click",function(){
            if ($(this).hasClass('btn_04') || $(this).hasClass('btn_02')) {
                return false;
            }
            $(".mask-regular").show();
            $('body').on('touchmove',eventTarget, false);
        });
        $('.mask-regular,.close-btn').on("click",function(){
            $(".mask-regular").hide();
            $('body').off('touchmove');
        });
        $('.active-regular').on("click",function(e){
            e.stopPropagation();
        });

        //我的奖品按钮
        var myScroll = new iScroll('wrapper',{
            vScrollbar:false,
            hScrollbar:false
        });

        $(".prizes").on("click",function(){
            if ($(this).hasClass('btn_04') || $(this).hasClass('btn_02')) {
                return false;
            }
            $('.prizes-box').show();
            $('body').on('touchmove',eventTarget, false);
            myScroll.refresh();//点击后初始化iscroll
        });
        $('.prizes-box').on('click',function(){
            $(this).hide();
            $('body').off('touchmove');
        });
        $('.pop_close').on('click',function(){
            $('.prizes-box').hide();
            $('body').off('touchmove');
        });
        $('.outer-box').on('click',function(event){
            event.stopPropagation();
        });

        function eventTarget(event) {
            var event = event || window.event;
            event.preventDefault();
        }

        //登录
        $('.btn_02').on('click',function(e) {
            e.preventDefault();
            poptpl.popComponent({
                popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_bg_01.png) no-repeat',
                popBorder:0,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png",
                btnMsg : "马上登录",
                title:'<p style="font-size:0.72rem;margin: 2.8rem 0 2rem;color: #fff;">您还没有登录哦！</p>',
                popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_btn_01.png) no-repeat',
                popBtmBorderRadius:0,
                popBtmColor:'#e01021',
                popBtmFontSize : ".50666667rem",
                btnHref:'<?= $loginUrl ?>'
            });
        });

        $('.btn_04').on('click', function(e) {
            e.preventDefault();
            toastCenter('活动未开始');
        });

        $('.btn_05').on('click', function(e) {
            if ($(this).hasClass('regular') || $(this).hasClass('prizes')) {
                return false;
            }
            e.preventDefault();
            toastCenter('活动已结束');
        });

        //初始化抽奖部分
        lottery.init('prz_pool');
        //点击进行抽奖
        $("#choujiang-btn").click(function() {
            var key = '<?= $promo->key ?>';
            var xhr = $.get('/promotion/draw1708/draw?key='+key);
            xhr.done(function(data) {
                if (data.code === 0) {
                    if (click) {
                        return false;
                    } else {
                        //调用接口 改变lottery.jiangpin来决定最终哪个奖品 改变award函数来决定函数的回掉
                        lottery.speed = 100;
                        //这里设置中的奖品
                        lottery.jiangpin =getRewardPosition(data.ticket);
                        award=function() {
                            var module = poptpl.popComponent({
                                popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_bg.png) no-repeat',
                                popBorder:0,
                                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png",
                                btnMsg : "收下礼品",
                                popTopColor:"#fff",
                                bgSize:"100% 100%",
                                title:'<p style="font-size:0.72rem;">恭喜您获得了</p><p style="font-size:0.5066667rem;">'+data.ticket.name+'</p>',
                                popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_btn_01.png) no-repeat',
                                popMiddleHasDiv:true,
                                popBtmColor:'#e01021',
                                contentMsg:"<img style='margin:0 auto;display: block;width: 4.2666667rem;margin-bottom: 0.4rem;' src='<?= FE_BASE_URI ?>"+data.ticket.path+"' alt=''/>",
                                popBtmBorderRadius:0,
                                popBtmFontSize : ".50666667rem",
                                afterPop: function () {
                                    location.href = '/promotion/draw1708/?_mark=<?= time() ?>';
                                }
                            },'close');
                        };
                        roll();
                        click = true;
                        return false;
                    }
                }
            });

            xhr.fail(function(jqXHR) {
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);
                    if (1 === resp.code || 2 === resp.code) {
                        toastCenter(resp.message);
                    } else if (3 === resp.code) {
                        poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_bg_01.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png",
                            btnMsg : "马上登录",
                            title:'<p style="font-size:0.72rem;margin: 2.8rem 0 2rem;color: #fff;">您还没有登录哦！</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_btn_01.png) no-repeat',
                            popBtmBorderRadius:0,
                            popBtmColor:'#e01021',
                            popBtmFontSize : ".50666667rem",
                            btnHref:'<?= $loginUrl ?>'
                        });
                    } else if (4 === resp.code) {
                        poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_bg_01.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png",
                            btnMsg : "确定",
                            popMiddle:false,
                            popTopColor:"#fb3f5a",
                            title:'<p style="font-size:0.54rem;margin: 2.8rem 0 2rem;color: #fff;">您的抽奖机会已用完！</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_btn_01.png) no-repeat',
                            popBtmBorderRadius:0,
                            popBtmColor:'#e01021',
                            popBtmFontSize : ".50666667rem"
                        },'close');
                    } else if (5 === resp.code) {
                        poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_bg_01.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_close.png",
                            btnMsg : "我知道了",
                            title:'<p style="font-size:0.54rem;margin: 2.8rem 0 2rem;color: #fff;">您还没有完成全部任务哦！</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170731/images/pop_btn_01.png) no-repeat',
                            popBtmBorderRadius:0,
                            popBtmColor:'#e01021',
                            popBtmFontSize : ".50666667rem"
                        },'close');
                    } else {
                        toastCenter('系统繁忙，请稍后重试！', function () {
                            location.href = '/promotion/draw1708/?_mark=<?= time() ?>';
                        });
                    }
                } else {
                    toastCenter('系统繁忙，请稍后重试！', function () {
                        location.href = '/promotion/draw1708/?_mark=<?= time() ?>';
                    });
                }
            });
        });

        function getRewardPosition(ticket)
        {
            var code;
            switch (ticket.sn) {
                case '1708_iphone':
                    code = 0;
                    break;
                case '1708_yagao':
                    code = 1;
                    break;
                case '1708_card_50':
                    code = 2;
                    break;
                case '1708_air':
                    code = 3;
                    break;
                case '1708_pillow':
                    code = 4;
                    break;
                case '1708_phonefare_50':
                    code = 5;
                    break;
                case '1708_points_160':
                    code = 6;
                    break;
                case '1708_points_120':
                    code = 6;
                    break;
                case '1708_cash_8.8':
                    code = 7;
                    break;
                case '1708_cash_6.6':
                    code = 7;
                    break;
            }
            return code;
        }
    });
</script>
