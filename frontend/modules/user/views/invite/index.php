<?php
$this->title = '邀请好友';

frontend\assets\FrontAsset::register($this);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css');
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/invitefriends.css');

use common\utils\StringUtils;
use common\widgets\Pager;
?>

<div class="inviteFriends-box">
    <div class="inviteFriends-box-header">
        <div class="inviteFriends-box-header-icon"></div>
        <span class="inviteFriends-box-header-font">邀请好友</span>
    </div>
    <!--邀请好友 banner -->
    <div class="inviteFriends-img"></div>

    <div class="lf_title">
        <p class="p_left"><span></span>邀请方式</p>
    </div>

    <!-- 邀请链接 -->
    <div class="code-box">
        <div class="lf code code-lf">
            <div class="txt">微信邀请<img class="triangle triangle-lf" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/triangle.png"></div>
            <div id="qrcode-img" class="qrcode-img"></div>
            <p>打开微信，使用“扫一扫”即可分享给好友</p>
        </div>
        <div class="lf code code-rg">
            <div class="txt txt-rg">发送邀请链接<img class="triangle triangle-rg" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/triangle.png"></div>
            <img class="code-img" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/alink.png" alt="用户邀请链接">
            <p class="code-tip">您可以复制以下链接，发送给您的好友：</p>
            <div class="linking">
                <?= Yii::$app->params['clientOption']['host']['frontend'] ?>luodiye/invite?code=<?= $user->usercode ?>
            </div>
            <a class="copy-alink" id="copy-button" data-clipboard-text='<?= Yii::$app->params['clientOption']['host']['frontend'] ?>luodiye/invite?code=<?= $user->usercode ?>'>复制链接</a>
        </div>
    </div>

    <div class="lf_title">
        <p class="p_left"><span></span>奖励规则</p>
    </div>

    <div class="box">
        <div class="box-ticket ">
            <div class="ticket ticket-left">
                <p class="num"><span>30</span>元</p>
                <p class="coupon">(投资代金券)</p>
                <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/border-fff.png">
            </div>
            <p class="ticket-txt">好友首次投资<10,000元</p>
        </div>
        <div class="box-ticket">
            <div class="ticket ticket-center">
                <p class="num"><span>50</span>元</p>
                <p class="coupon">(投资代金券)</p>
                <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/border-fff.png">
            </div>
            <p class="ticket-txt">好友首次投资>=10,000元</p>
        </div>
        <div class="box-ticket">
            <div class="ticket ticket-right">
                <p class="num"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/money.png"></p>
                <p class="coupon">(现金红包)</p>
                <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/border-fff.png">
            </div>
            <p class="ticket-txt">好友前三次投资的0.1%</p>
        </div>
        <div class="box-ticket">
            <div class="ticket ticket-last">
                <p class="num"><span>50</span>元</p>
                <p class="coupon">(投资代金券)</p>
                <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/useraccount/invitefriends/border-fff.png">
            </div>
            <p class="ticket-txt">好友注册即得</p>
        </div>
        <div style="clear: both"></div>
    </div>

    <div class="inviteFriends-box-content">
        <div class="count">
            <div class="block"><span>邀请人数:</span><i><?= count($model) ?></i>个</div>
            <div class="block"><span>代金券奖励:</span><i><?= StringUtils::amountFormat2(array_sum(array_column($model, 'coupon'))) ?></i>元</div>
            <div class="block"><span>现金红包奖励:</span><i><?= StringUtils::amountFormat3(array_sum(array_column($model, 'cash'))) ?></i>元</div>
            <div style="clear: both"></div>
        </div>
        <div class="page-title">
            <span class="active">邀请列表</span>
            <span class="">活动规则</span>
        </div>

        <!-- 邀请详情 -->
        <div class="active-box" >
            <table>
                <tr>
                    <th width="135">姓名</th>
                    <th width="135">手机号</th>
                    <th width="135">注册日期</th>
                    <th width="135">代金券(元)</th>
                    <th width="135">现金红包(元)</th>
                </tr>
                <?php if (!empty($data)) : ?>
                    <?php foreach ($data as $val) : ?>
                        <tr class="<?= ($key + 1) % 2 === 0 ? 'td-back-color' : '' ?>">
                            <td><?= empty($val['name']) ? '---' : StringUtils::obfsName($val['name']) ?></td>
                            <td><?= StringUtils::obfsMobileNumber($val['mobile']) ?></td>
                            <td><?= $val['day'] ?></td>
                            <td><?= $val['coupon'] ?></td>
                            <td><?= StringUtils::amountFormat3($val['cash']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
            <?php if (empty($model)) : ?>
                <div class="without-font">暂未获得邀请奖励，快前去邀请吧</div>
            <?php endif; ?>

            <center><?= Pager::widget(['pagination' => $pages]); ?></center>
        </div>

        <!-- 活动规则 -->
        <div class="active-box rule hide">
            <h4>活动时间：2016年8月4日~2016年9月25日</h4>

            <h4>活动规则：</h4>

            <p>登录温都金服网站，进入“我的账户”；</p>
            <p>点击“邀请好友”可以看到邀请好友活动，通过微信或者链接进行邀请；</p>
            <p>当您的小伙伴通过此邀请链接注册并成功投资后，您即可获得邀请好友的奖励啦；</p>
            <p>现金奖励需要您有投资记录才能发放，发放奖励现金时，以"角"为单位取整，采用四舍五入；</p>
            <p>严禁恶意刷邀请好友，如有发生，封号处理。</p>

            <h4>奖励规则：</h4>
            <p>被邀请好友首次单笔投资1万元以上（含1万元），邀请人获得1张50元代金券；</p>
            <p>被邀请好友首次单笔投资1万元以下（不含1万元），邀请人获得1张30元代金券；</p>
            <p>邀请人获得被邀请人投资额0.1% 的奖励返现（仅限活动期间的前三次投资）；</p>
            <p>被邀请人注册即可获得50元代金券。</p>

            <h4>代金券使用规则：</h4>
            <p>代金券有效期30天(单笔投资满1万元抵扣)。</p>
        </div>
    </div>
</div>
<div class="clear"></div>

<script src="<?= ASSETS_BASE_URI ?>js/ZeroClipboard.min.js" type="text/javascript"></script>
<script src="<?= ASSETS_BASE_URI ?>js/jquery.qrcode.min.js" type="text/javascript" charset="utf-8"></script>
<!--[if gte IE 9]><!-->
    <script src="<?= ASSETS_BASE_URI ?>js/clipboard.min.js" type="text/javascript"></script>
<!--[endif]-->
<!--[if !IE]><!-->
    <script src="<?= ASSETS_BASE_URI ?>js/clipboard.min.js" type="text/javascript"></script>
<!--<![endif]-->
<script>
    $(document).ready(function () {
        //1.自动生成二维码
        $(".qrcode-img").qrcode({
            render: "div",
            size: 156,
            text: '<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/p1608/invite'// 需要修改成正确的地址
        });

        //2.复制
        var browser = navigator.appName;
        var b_version = navigator.appVersion;
        var version = b_version.split(";");
        if (version.length > 1) {
            var trim_Version = version[1].replace(/[ ]/g,"");
        } else {
            var trim_Version = version[0].replace(/[ ]/g,"");
        }
        if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE8.0") {
            $('#copy-button').on('click',function() {
                alert('请手动复制');
            })
        } else if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE9.0") {
            $('#copy-button').on('click',function() {
                alert('请手动复制');
            })
        } else {
            try {
                var btn = document.getElementById('copy-button');
                var clipboard = new Clipboard(btn);
                clipboard.on('success', function(e) {
                    alert('内容已复制到剪贴板');
                });

                clipboard.on('error', function(e) {
                    alert('请重新复制');
                });
            } catch(error) {
            }
        }

        //3.选项卡
        $('.page-title span').on('click',function() {
            var index = $(this).index();
            $('.page-title span').each(function() {$(this).removeClass('active')});
            $('.page-title span').eq(index).addClass('active');

            $('.active-box').each(function() {$(this).addClass('hide')});
            $('.active-box').eq(index).removeClass('hide');
        });
    });
</script>