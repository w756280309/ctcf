<?php

use common\models\adv\Share;

$this->title = '新用户专享活动';
$hostInfo = Yii::$app->request->hostInfo;
$calloutUser = Yii::$app->session->get('calloutUser');
$this->share = new Share([
    'title' => '新人免费抽奖，iPhone7、苹果手表不限量！',
    'description' => '庆祝温都金服交易额突破20亿，海量好礼等你来！',
    'imgUrl' => 'https://static.wenjf.com/upload/link/link1496370729342200.png',
    'url' => null === $user || ($user && $user->id !== $calloutUser) || ($callout && $callout->responderCount >= 3) ? Yii::$app->request->absoluteUrl : $hostInfo.'/promotion/draw/share?ucode='.$user->usercode,
]);
$this->headerNavOn = true;
$currentUrl = urlencode(Yii::$app->request->absoluteUrl);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170527/css/index.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/zepto.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/weixin-share-tips.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/js/index.js"></script>

<div class="flex-content">
    <div class="top-part">
        <div class="my-progress">接力进度：
            <span>
                <?php
                    if ($callout) {
                        if ($callout->responderCount >= 3) {
                            echo '已完成';
                        } else {
                            echo $callout->responderCount.'人';
                        }
                    } else {
                        echo '未开始';
                    }
                ?>
            </span>
        </div>
        <div class="my-prize">【我的奖品】</div>
    </div>

    <div class="middle-part" id="prz_pool">
        <table id="prize_pool" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="lottery-unit lottery-unit-0">
                    <img class="active" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_1.png" alt="">
                </td>
                <td class="lottery-unit lottery-unit-1">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_2.png" alt="">
                </td>
                <td class="lottery-unit lottery-unit-2">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_3.png" alt="">
                </td>
            </tr>
            <tr>
                <td class="lottery-unit lottery-unit-7"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_8.png" alt="">
                </td>
                <td>
                    <?php if (null === $user || ($user && $restTicketCount > 0) || ($user && 0 === $ticketCount)) : ?>
                        <img class="prz_btn" id="choujiang-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/button_start.png" alt="">
                    <?php elseif ($user && 0 === $restTicketCount && $ticketCount >= 2) : ?>
                        <img class="prz_btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/button_over.png" alt="">
                    <?php else : ?>
                        <img class="prz_btn wap-share-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/button_again.png" alt="">
                    <?php endif; ?>
                </td>
                <td class="lottery-unit lottery-unit-3">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_4.png" alt="">
                </td>
            </tr>
            <tr>
                <td class="lottery-unit lottery-unit-6"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_7.png" alt=""></td>
                <td class="lottery-unit lottery-unit-5"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_6.png" alt=""></td>
                <td class="lottery-unit lottery-unit-4"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/prize_5.png" alt=""></td>
            </tr>
        </table>
    </div>

    <div class="bottom-part">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/rules_title.png" alt="" class="rules-title">
        <ol class="rules-box">
            <li>活动期间新注册用户可以免费获得 1 次抽奖机会;</li>
            <li>抽奖机会用完后,点击“好友接力 再抽一次”按钮,邀请 3 位好友点击助力,即可再获得 1 次抽奖机会(最多 1 次);</li>
            <li>现金红包将于实名认证并绑定银行卡后直接发放到账户余额；</li>
            <li>活动结束后3个工作日内，工作人员将与您联系确认实物奖品领取相关事宜，请保持通讯畅通；</li>
            <li>活动时间2017年6月3日-6月9日。</li>
        </ol>
        <p class="remind" style="margin-top: 1rem;">本次活动最终解释权归温都金服所有</p>
        <p class="remind">所有活动及奖品与苹果公司无关</p>
    </div>

    <div class='mask-list' style="display: none"></div>
    <div class="myprize-list" style="display: none">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/pop_close.png" alt="" class="close-prize">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/img_prizelist.png" alt="" class="prizelist-title">
        <ul class="prizelist-content">
            <?php foreach ($drawList as $key => $draw) :  ?>
                <?php
                    if ($key > 1) {
                        break;
                    }
                ?>
                <li>
                    <div class="pic lf"
                         style="background: url(<?= FE_BASE_URI.$draw->reward->path ?>) center center no-repeat; background-size: contain;
                         <?= in_array($draw->reward->sn, ['603_card100', '603_card500', '603_coupon20', '603_coupon50']) ? 'width: 1.46666667rem; left: 8px;' : '' ?>"></div>
                    <p class="prize-name"><?= $draw->reward->name ?></p>
                    <p class="prize-time"><?= date('Y-m-d  H:i:s', $draw->drawAt) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>

<script type="text/javascript">
    var award = function () {};
    $(function () {
        //初始化抽奖部分
        lottery.init('prz_pool');
        lottery.speed = 200;

        //点击进行抽奖
        var allowClick = true;
        $("#prize_pool #choujiang-btn").click(function(e) {
            e.preventDefault;

            if(!allowClick) {
                return false;
            }

            var xhr = $.get('/promotion/draw/draw');
            allowClick = false;

            xhr.done(function(data) {
                if (data.code === 0) {
                    lottery.jiangpin = data.data.drawId;
                    roll();
                    award = function () {
                        var module = poptpl.popComponent({
                            btnMsg : 'register' !== data.data.drawSource ? '好友助力 再抽一次' : '确定',          //文案得改
                            popTopHasImg : true,
                            popTopImg: "<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/img_gongxi.png",
                            popMiddleHasDiv : true,
                            popMiddleColor : "#fb5a1f",
                            contentMsg: "<span style='font-size: 0.56rem'>恭喜您!</span><br>获得<span style='color: #fc281c'>"+data.data.drawName+"</span>",
                            afterPop: function () {
                                location.href = '';
                            }
                        }, 'close');
                    };
                }

                allowClick = true;
            });

            xhr.fail(function(jqXHR) {
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);
                    if (1 === resp.code || 2 === resp.code) {
                        notice(resp.message);
                    } else if (3 === resp.code) {
                        var module = poptpl.popComponent({
                            btnMsg : "去注册",
                            popTopHasImg : true,
                            popTopImg : "<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/img_register.png",
                            btnHref: "/site/signup?next=<?= $currentUrl ?>",
                            popMiddleHasDiv : true,
                            popMiddleColor : "#fb5a1f",
                            contentMsg: "注册完就可以免费抽奖了哦!"
                        });
                    } else if (4 === resp.code) {
                        notice("本活动仅限新用户参与哦!<br>快去参加其他活动吧!");
                    } else {
                        notice('系统繁忙，请稍后重试！');
                    }
                } else {
                    notice('系统繁忙，请稍后重试！');
                }

                allowClick = true;
            });
        });

        //奖品列表
        $('.my-prize').on('click',function () {
            <?php if ($user) : ?>
                if ($('.prizelist-content').find('li').length > 0) {
                    $('.myprize-list').show();
                    $('.mask-list').show();
                    $('body').css('overflow','hidden').on('touchmove', poptpl.eventTarget, false);
                } else {
                    var module = poptpl.popComponent({
                        popBtmHas :false,
                        popTopHasImg : true,
                        popTopImg : "<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/img_nogift.png",
                        popMiddleHasDiv : true,
                        popMiddleColor : "#fb5a1f",
                        contentMsg:"您还未获得任何奖品!"
                    });
                }
            <?php else : ?>
                location.href = '/site/login?next=<?= $currentUrl ?>';
            <?php endif; ?>
        });

        if(navigator.userAgent.indexOf('MicroMessenger') > -1 ) {
            weiXinShareTips($('.wap-share-btn'));
        } else {
            $('.wap-share-btn').on('click', function (e) {
                e.preventDefault;

                notice('浏览器用户请关注温都金服微信公众号“wendujinfu”，在“新手活动”-“拼手气”中参与本次活动。');
            });
        }
    });

    function notice(msg) {
        var module = poptpl.popComponent({
            btnMsg : "确定",
            popMiddleHasDiv : true,
            popMiddleColor : "#fb5a1f",
            contentMsg: msg
        },'close');
    }
</script>