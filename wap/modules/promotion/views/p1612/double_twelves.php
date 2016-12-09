<?php
$this->title = '幸运双十二';
$this->share = $share;

use common\models\promo\Promo1212;
use common\utils\StringUtils;
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/flex.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/js/index.js"></script>
<style>
    header div:first-child {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/banner_01.png") no-repeat center;
        background-size: 100% 100%;
    }
    header div:last-child {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/banner_02.png") no-repeat center;
        background-size: 100% 100%;
    }
    section .btn button {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/btn.png") no-repeat center;
        background-size: 100% 100%;
    }
    section .people .title {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/title.png") no-repeat center;
        background-size: 100% 100%;
    }
    section .activeregular .title,
    section .accept .title {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/title.png") no-repeat center;
        background-size: 100% 100%;
    }
    .login .loginbtn,
    .nochance .loginbtn,
    .login .investbtn,
    .nochance .investbtn {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/btn_01.png") no-repeat center;
        background-size: 100% 100%;
    }
    .login .investbtn,
    .nochance .investbtn {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/btn_02.png") no-repeat center;
        background-size: 100% 100%;
    }
    .drawgift div {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/drawgitfbg.png") no-repeat center;
        background-size: 100% 100%;
    }
    .drawgift a {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/btn_03.png") no-repeat center;
        background-size: 100% 100%;
    }
    .nodrawgift a {
        background: url("<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/btn_04.png") no-repeat center;
        background-size: 100% 100%;
    }
</style>

<!--banner-->
<header>
    <div><span class="activetime">2016.12.12-2016.12.21</span></div>
    <div></div>
</header>
<!--content-->
<section>
    <div id="activereg">
        <div class="step1">
            <span class="lf">第一步：幸运大礼包</span>
            <a class="rg">如何增加抽奖机会?</a>
        </div>
        <div class="lotternumber">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/gift.png" alt="">
            您还有抽奖次数：
            <?php if (null === $user) { ?>
                <a href="/site/login"><span>未登录</span></a>
            <?php } else { ?>
                <span><?= $tickets ?></span>次
            <?php } ?>
        </div>

        <table class="coupon" cellspacing="5" >
            <tr>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_01.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_02.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_03.png" alt=""></td>
            </tr>
            <tr>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_04.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_05.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_06.png" alt=""></td>
            </tr>
            <tr>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_07.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_08.png" alt=""></td>
                <td><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/coupon_09.png" alt=""></td>
            </tr>
        </table>

        <!--兑换按钮-->
        <div class="btn" id="draw">
            <button></button>
        </div>

        <!--对奖记录-->
        <div class="moregift" id="drawForUser">
            我的中奖纪录>
        </div>

        <div class="step2">
            <span class="lf">第二步：幸运大奖</span>
            <a class="rg">如何获取幸运大奖?</a>
        </div>

        <ul class="award clearfix">
            <li class="lf">
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/ipad.png" alt="">
                    <span>ipad mini 4  64G</span>
                </div>
                <p>一等奖 1名</p>
            </li>
            <li class="rg">
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/cooker.png" alt="">
                    <span>米家电饭煲</span>
                </div>
                <p>二等奖 2名</p>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/source.png" alt="">
                    <span>罗马仕充电宝</span>
                </div>
                <p>三等奖 5名</p>
            </li>
        </ul>

        <div class="regular">
            <ul class="clearfix ">
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/phase_01.png" alt=""></li>
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/phase_02.png" alt=""></li>
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/phase_03.png" alt=""></li>
            </ul>
            <p class="warn">*如果中奖号码超过奖品名额，最早投资温都金服的用户中奖，活动期间内每人仅限一次获奖机会。</p>
        </div>

        <?php if (!empty($boardList)) { ?>
            <div class="people">
                <div class="title">获奖名单</div>
                <ul>
                    <?php foreach ($boardList as $record) {  $config = Promo1212::getPrizeMessageConfig($record) ?>
                        <li><span class="lf">恭喜<?= StringUtils::obfsMobileNumber($record->user->mobile) ?> </span>获得  <?= $config['name'] ?><span class="rg"><?= date('Y/m/d', $record->drawAt) ?></span></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>

    <div class="activeregular">
        <div class="title">获奖规则</div>
        <ul>
            <li>1.所有注册用户直接享有一次幸运大礼包的抽奖机会；</li>
            <li>2.所有注册用户邀请1人注册成功后享有1次幸运大礼包的抽奖机会，邀请2人注册成功享有3次幸运大礼包抽奖机会 ，邀请3人注册成功后享有5次幸运大礼包的抽奖机会，邀请3人以上每多邀请1人多得2次幸运大礼包的抽奖机会；</li>
            <li>3.活动期间内，邀请用户注册成功达到3人及以上就有机会获得幸运大奖；</li>
            <li>4.可前往账户中心进行邀请好友。</li>
        </ul>
    </div>
    <!--<div id="acceptreg"></div>-->
    <div class="accept">
        <div class="title">领奖规则</div>
        <ul>
            <li>1.幸运大奖获奖的用户名单活动结束后次日10点公布在温都金服官网上，温都金服客服人员将会在3个工作日内电话联系用户确认礼品以及领取地点；</li>
            <li>2.活动期间获奖人员须本人持身份证来温都金服领取；</li>
            <li>3.活动结束后一周内未领取礼品的视为自动放弃；</li>
            <li>4.领取地点：温州市鹿城区飞霞南路657号（老党校对面）；</li>
            <li>5.未投资过的用户抽中现金红包后，需要首次投资后发放到账户中心，红包有效期20天；</li>
            <li>6.详情可咨询客服热线：<a href="tel:400-101-5151">400-101-5151</a>；</li>
            <li>7.本活动最终解释权在法律范围内归温都金服（温州温都金融信息服务股份有限公司）所有。</li>
        </ul>
    </div>
</section>
<!--footer-->
<footer><img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/adv.png" alt=""> 理财非存款 产品有风险 投资须谨慎 </footer>

<!--遮罩-->
<div class="mask">
    <!--弹框-->
    <!--登录弹框-->
    <div class="login pop">
        <img class="closepop" src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/close01.png" alt="">
        <p>您还未登录！</p>
        <p>登录即可参加抽奖活动，赶快行动吧~</p>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/box.png" alt="">
        <a href="/site/login" class="loginbtn"></a>
        <a href="/<?= defined('IN_APP') ? 'site' : 'luodiye' ?>/signup?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>">没有账号，立即去注册</a>
    </div>
    <!--没有抽奖机会-->
    <div class="nochance pop">
        <img class="closepop" src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/close01.png" alt="">
        <p>没有抽奖机会了！</p>
        <p>邀请好友，可获得更多抽奖机会哦~</p>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/box.png" alt="">
        <a href="/user/invite" class="investbtn"></a>
    </div>
    <!--中奖效果-->
    <div class="drawgift pop">
        <img class="bgtop" src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/drawgiftbg_01.png" alt="">
        <p></p>
        <div>
            <img src="" alt="">
        </div>
        <a href=""></a>
    </div>
    <!--中奖列表无礼品-->
    <div class="nodrawgift pop">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/drawgiftbg_02.png" alt="">
        <p>您还未抽过奖！</p>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/box.png" alt="">
        <a href=""></a>
    </div>
    <!--中奖列表有礼品-->
    <div class="giftlist pop">
        <img  class="giftlistimg" src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/drawgiftbg_02.png" alt="">
        <img class="giftlistclose  closepop" src="<?= FE_BASE_URI ?>wap/campaigns/double-twelve/images/close_02.png" alt="">
        <div class="giftlistbox" id="giftBox"></div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#draw').on('click', function () {
            $(this).attr('disabled', true);
            request('/promotion/p1612/draw', function (data) {
                if (data.code) {
                    if ('活动未开始' === data.message || '活动已结束' === data.message) {
                        alert(data.message);
                    } else if ('您还未登录' === data.message) {
                        pop('.login');
                    } else if ('您没有抽奖机会了' === data.message) {
                        pop('.nochance');
                    }
                } else {
                    $('.drawgift div img').attr('src', data.data.pic);
                    $('.drawgift p').html('恭喜您获得'+data.data.name+'！');
                    pop('.drawgift');
                }

                $(this).attr('disabled', false);
            }, function () {
                $(this).attr('disabled', false);
            });
        });

        $('#drawForUser').on('click', function () {
            $(this).attr('disabled', true);
            request('/promotion/p1612/draw-for-user', function (data) {
                if (data.code) {
                    if ('活动未开始' === data.message || '活动已结束' === data.message) {
                        alert(data.message);
                    } else if ('您还未登录' === data.message) {
                        pop('.login');
                    } else if ('您还未抽过奖' === data.message) {
                        pop('.nodrawgift');
                    }
                } else {
                    $('#giftBox').html(data.html);
                    pop('.giftlist');
                }

                $(this).attr('disabled', false);
            }, function () {
                $(this).attr('disabled', false);
            });
        });
    })

    function request(url, succ, fail)
    {
        var xhr = $.get(url);

        xhr.done(function (data) {
            succ(data);
        });

        xhr.fail(function() {
            alert('系统繁忙，请稍后重试！');
            fail();
        });
    }

    function pop(className)
    {
        $('.mask').show();
        $(className).show();
        $('body').on('touchmove', eventTarget, false);
    }
</script>