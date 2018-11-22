<?php
$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
if ($fromNb) {
    $this->title = '现代金报-楚天财富';
} else {
    $this->title = '楚天财富';
}

$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$this->showBottomNav = true;

use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->registerCssFile(FE_BASE_URI . "libs/swiper/swiper.min.css");
$this->registerCss(file_get_contents(Yii::getAlias('@fe/dist/v2/wap/index/css/index.css')));
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'res/js/js.cookie.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'libs/fastclick.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'libs/jquery.lazyload.min.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/swiper.min.js', ['depends' => JqueryAsset::class]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/index/css/popover-index.css?v=1.03803">
    <div class="container flex-content relative">
        <!--  签到   -->
        <a class="signature" href="/user/checkin">
            <img src="<?= FE_BASE_URI ?>wap/index/images/signature.png" alt="签到抽奖">
        </a>
        <img style="width:100%;" class="channel hide" src="<?= FE_BASE_URI ?>wap/index/images/channel.png" alt="">


        <?php if (!empty($hotActs)) { ?>
            <div class="banner swiper-container">
                <div class="swiper-wrapper" id="index_banner">
                    <?php
                    $firstHotAdv = current($hotActs);
                    if (isset($firstHotAdv) && !is_null($firstHotAdv->media)) {
                        ?>
                        <a class="swiper-slide" href="<?= $firstHotAdv->getLinkUrl() ?>">
                            <img src="<?= UPLOAD_BASE_URI . $firstHotAdv->media->uri ?>" alt="">
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php } ?>

        <div class="login clearfix hide" id="login">
            <a class="f15 lf" href="/site/login">登录</a>
            <a class="f15 rg" href="/site/signup">注册</a>
        </div>

        <div class="cash-box">
            <p class="f15 volume-of-trad hide" id="totalTradeAmount">平台累计交易额：<i></i><span>亿元</span></p>
            <p class="f13 cash-rate"><i class="cash-icon"></i>历史兑付率100%</p>

            <div class="total">
                <ul class="f13 clearfix">
                    <li class="lf" id="totalRefundAmount">累计兑付<span></span>万元</li>
                    <li class="rg" id="totalRefundInterest">带来<span></span>万元收益</li>
                    <i></i>
                </ul>
            </div>
        </div>

        <?php if (\Yii::$app->params['showCharityAmount']) : ?>
            <div class="charity-box">
                <div class="charity-top clearfix">
                    <div class="charity-content charity-content-lf lf">
                        <p class="word f15">平台累计捐赠：</p>
                        <p class="number f20" id="totalCharityAount"></p>
                    </div>
                    <div class="charity-content charity-content-rg rg">
                        <p class="word f12">武汉市慈善总会&nbsp;&nbsp;湖北日报分会设立<i class="red-txt">"楚天财富慈善公益基金"</i></p>
                    </div>
                </div>
                <div class="charity-bottom">
                    <p><img src="<?= FE_BASE_URI ?>wap/index/images/heart.png" alt=""><span>您的每一笔出借，都有一份爱的奉献</span><img src="<?= FE_BASE_URI ?>wap/index/images/heart.png" alt=""></p>
                </div>
            </div>
        <?php else: ?>
            <div class="charity-box">
                <div class="charity-top-txt clearfix">
                    <div class="charity-content-top">
                        <p class="word">520慈善基金</p>
                        <p class="word-txt">正式成立</p>
                    </div>
                </div>
                <div class="charity-bottom">
                    <p><img src="<?= FE_BASE_URI ?>wap/index/images/heart.png" alt=""><span>您的每一笔出借，都有一份爱的奉献</span><img src="<?= FE_BASE_URI ?>wap/index/images/heart.png" alt=""></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="showMore">
            <div class="clearfix">
                <a class="f13 lf" href="/site/h5?wx_share_key=h5">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/icon_01.png" alt="">
                    <p>平台介绍</p>
                </a>
                <a class="f13 lf" href="/user/invite">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/icon_02.png" alt="">
                    <p>邀请好友</p>
                </a>
                <a class="f13 lf" href="/site/app-download?redirect=/mall/portal/guest<?= (Yii::$app->request->get('token') && defined('IN_APP')) ? '?token='.Yii::$app->request->get('token') : ''?>">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/icon_03.png" alt="">
                    <p>积分商城</p>
                </a>
                <a class="f13 lf" href="/news">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/icon_04.png" alt="">
                    <p>官网公告</p>
                </a>
            </div>
        </div>

        <?php if ($xs) { ?>
            <div class="newnorm hide" id="loginNewPeople">
                <p class="newnormtitle f12">新手专享
                    <?= $this->renderFile("@common/views/tags.php", ['loan' => $xs]) ?>
                </p>
                <?php $ex = $xs->getDuration() ?>
                <?php if (!$xs->isFlexRate && !$xs->jiaxi) { ?>
                    <a href="/deal/deal/index">
                        <p class="f15"><?= $xs->title ?></p>
                        <p class="f45"><?= LoanHelper::getDealRate($xs) ?><i class="f25">%</i></p>
                        <p class="f12">借贷双方约定利率</p>
                        <ul class="clearfix">
                            <li class="f14 lf text-align-lf"><img class="icon5" src="<?= FE_BASE_URI ?>wap/index/images/icon_05.png" alt=""> <?= StringUtils::amountFormat2($xs->start_money) ?>元起借</li>
                            <li class="f14 lf comred"><img class="icon6" src="<?= FE_BASE_URI ?>wap/index/images/icon_06.png" alt=""> <?= $ex['value'].$ex['unit'] ?>期限</li>
                            <li class="f14 lf text-align-rg"><img class="icon7" src="<?= FE_BASE_URI ?>wap/index/images/icon_07.png" alt=""> 限购一万</li>
                            <li class="f18 lf">去出借</li>
                        </ul>
                    </a>
                <?php } else { ?>
                    <!--加息-->
                    <a href="/deal/deal/index">
                        <p class="f15"><?= $xs->title ?></p>
                        <p class="f30"><em><?= LoanHelper::getDealRate($xs) ?><i class="f20">%</i><?php if (!empty($xs->jiaxi)) { ?><span class="f15">+<?= StringUtils::amountFormat2($xs->jiaxi) ?>%</span><?php } ?></em></p>
                        <p class="f12">借贷双方约定利率</p>
                        <ul class="clearfix">
                            <li class="f14 lf text-align-lf"><img class="icon5" src="<?= FE_BASE_URI ?>wap/index/images/icon_05.png" alt=""> <?= StringUtils::amountFormat2($xs->start_money) ?>元起借</li>
                            <li class="f14 lf comred"><img class="icon6" src="<?= FE_BASE_URI ?>wap/index/images/icon_06.png" alt=""> <?= $ex['value'].$ex['unit'] ?>期限</li>
                            <li class="f14 lf text-align-rg"><img class="icon7" src="<?= FE_BASE_URI ?>wap/index/images/icon_07.png" alt=""> 限购一万</li>
                            <li class="f18 lf">去出借</li>
                        </ul>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($issuers)) { ?>
            <?php
                $num = count($issuers);
                foreach ($issuers as $k => $issuer) {
                    ${'issuer'.$k} = $issuer;
                    ${'medias'.$k} = $issuer->getMedias();
                }
            ?>
            <section>
                <div class="featured">
                    <p class="project f15">精选项目</p>
                    <!--一张图片-->
                    <?php if (1 === $num) { ?>
                        <?php if (null !== $medias0['big']) { ?>
                            <ul class="clearfix onepic">
                                <li>
                                    <a href="<?= Html::encode($issuer0->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias0['big']->uri) ?>" alt=""></a>
                                </li>
                            </ul>
                        <?php } ?>
                        <!--两张图片-->
                    <?php } elseif (2 === $num) { ?>
                        <?php if (null !== $medias0['mid'] && null !== $medias1['mid']) { ?>
                            <ul class="clearfix twopic">
                                <li class="lf">
                                    <a href="<?= Html::encode($issuer0->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias0['mid']->uri) ?>" alt=""></a>
                                </li>
                                <li class="rg">
                                    <a href="<?= Html::encode($issuer1->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias1['mid']->uri) ?>" alt=""></a>
                                </li>
                            </ul>
                        <?php } ?>
                        <!--三张图片-->
                    <?php } elseif (3 === $num) { ?>
                        <?php if (null !== $medias0['mid'] && null !== $medias1['small'] && null !== $medias2['small']) { ?>
                            <ul class="clearfix threepic">
                                <li class="lf">
                                    <a href="<?= Html::encode($issuer0->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias0['mid']->uri) ?>" alt=""></a>
                                </li>
                                <li class="rg">
                                    <a href="<?= Html::encode($issuer1->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias1['small']->uri) ?>" alt=""></a>
                                    <span class="a-two"></span>
                                    <a href="<?= Html::encode($issuer2->path) ?>"><img src="<?= Html::encode(UPLOAD_BASE_URI.$medias2['small']->uri) ?>" alt=""></a>
                                </li>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>

        <?php if (!empty($loans)) { ?>
            <div class="invest">
                <p class="clearfix">
                    <span class="lf f15">出借专区</span>
                    <a class="rg f15" href="/deal/deal/index">查看全部产品</a>
                </p>
                <ul>
                    <?php foreach ($loans as $loan) { ?>
                        <?php $ex = $loan->getDuration() ?>
                        <li>
                            <a class="superscript" href="/deal/deal/detail?sn=<?= $loan->sn ?>">
                                <div class="f14 supertitle"><?= $loan->title ?></div>
                                <div class="superctn clearfix">
                                    <div class="lf">
                                        <p><em class="f24"><?= LoanHelper::getDealRate($loan) ?><span class="f15">%</span><?php if (!empty($loan->jiaxi)) { ?><i class="f12">+<?= StringUtils::amountFormat2($loan->jiaxi) ?>%</i><?php } ?></em></p>
                                        <p class="f14">借贷双方约定利率</p>
                                    </div>
                                    <div class="lf">
                                        <p class="f14 months"><span class="f16"><?= $ex['value'] ?></span><?= $ex['unit'] ?></p>
                                        <p class="f14 money"><span class="f16"><?= StringUtils::amountFormat2($loan->start_money) ?></span>元起借</p>
                                    </div>
                                </div>
                                <div class="progessline clearfix">
                                    <div class="progess lf">
                                        <span style="width: <?= $loan->getProgressForDisplay() ?>%;"></span>
                                    </div>
                                    <i class="rg f14"><?= $loan->getProgressForDisplay() ?>%</i>
                                </div>
                                <div class="tags clearfix">
                                    <?php if (null !== $loan->tags) { ?>
                                        <p class="lf f12">
                                            <?= $this->renderFile("@common/views/tags.php", ['loan' => $loan]) ?>
                                        </p>
                                    <?php } elseif ($loan->pointsMultiple > 1) { ?>
                                        <p class="lf f12">
                                            <?='<span class="red">积分' . Html::encode($loan->pointsMultiple) . '倍</span>';?>
                                        </p>
                                    <?php } ?>
                                    <p class="rg f14"><?= Yii::$app->params['refund_method'][$loan->refund_method] ?></p>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php if (!empty($news)) { ?>
            <div class="notices">
                <ul>
                    <li><span class="lf f15">公告专区</span><a href="/news" class="rg f12">更多</a></li>
                    <?php foreach ($news as $key => $new) { ?>
                        <li class="list"><a class="f14" href="/news/detail?id=<?= $new->id ?>&v=<?= time() ?>"><?= $new->title ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <div class="shareholder">
            <p class="f15">股东背景</p>
            <ul>
                <li></li>
                <li><img data-original="<?= FE_BASE_URI ?>wap/index/images/shareholde_02.png" alt=""><img data-original="<?= FE_BASE_URI ?>wap/index/images/shareholde_01_new.png" alt=""></li>
            </ul>
        </div>

        <div class="platform">
            <p class="f15">平台优势</p>
            <ul class="clearfix">
                <li></li>
                <li class="lf platformList">
                    <div class="platformListFirst">
                        <img data-original="<?= FE_BASE_URI ?>wap/index/images/platform_01.png" alt="">
                        <div class="f15">国资背景</div>
                        <div class="f14">湖北日报新媒体集团旗下</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListSecond">
                        <img data-original="<?= FE_BASE_URI ?>wap/index/images/platform_02.png" style="width: 1.1rem;" alt="">
                        <div class="f15">资金稳妥</div>
                        <div class="f14">第三方资金托管</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div>
                        <img data-original="<?= FE_BASE_URI ?>wap/index/images/platform_03.png" alt="">
                        <div class="f15">收益稳健</div>
                        <div class="f14">预期收益5.5~9%</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListLast">
                        <img data-original="<?= FE_BASE_URI ?>wap/index/images/platform_04.png" alt="">
                        <div class="f15">产品优质</div>
                        <div class="f14">国企、政信类产品</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="platform icp">
            <a href="/news/detail?type=info&id=383" alt=""><img data-original="<?= ASSETS_BASE_URI ?>images/icp.png" alt=""></a>
        </div>

        <div class="address">
            <a class="f14" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><img data-original="<?= FE_BASE_URI ?>wap/index/images/phone.png" alt=""><?= Yii::$app->params['platform_info.contact_tel'] ?></a>
            <p class="f14">武汉市武昌区东湖路181号楚天文化创意产业园区1号楼6层</p>
        </div>

        <footer class="f11"><span></span>湖北日报新媒体集团旗下出借平台<span></span></footer>
        <?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
            <?= $this->renderFile('@wap/views/layouts/footer.php')?>
        <?php } ?>

    </div>

    <div class="mask" style="display: none"></div>
    <!--新增首页弹框1-->
    <div class="first-popover" style="display: none">
        <img class="popover-bg" src="<?= FE_BASE_URI ?>wap/index/images/popover_01.png" alt="">
        <div class="popover-ctn">
            <img class="popover-close" src="<?= FE_BASE_URI ?>wap/index/images/popover-close.png" alt="">
            <p class="popover-title">价值288元代金券<br>已发到您的账户</p>
            <p class="popover-detail">你也可以在账户-代金券中,查看奖励</p>
            <a class="btn_01" href="/user/coupon/list">查看详情</a>
            <a class="btn_02" href="javascript:void(0)">去出借 领取160元超市卡</a>
        </div>
    </div>

    <!--新增首页弹框2-->
    <div class="second-popover" style="display: none">
        <img class="popover-close" src="<?= FE_BASE_URI ?>wap/index/images/popover-close_01.png" alt="">
        <p class="popover-title">出借前先开通资金托管账户</p>
        <p class="popover-detail">开通账户送积分，出借还有更多超市卡等你拿</p>
        <a class="popover-btn" href="/user/identity">马上去开通</a>
    </div>

    <!--对应的弹框的js-->
    <script>
        $('.popover-close').on('click',function(){
            $('.first-popover,.second-popover').hide();
            $('.mask').hide();
        })
    </script>

<?php if (null !== $kaiPing && $kaiPing->media) { ?>
    <div class="mask hide"></div>

    <div class="pop hide">
        <img src="<?= FE_BASE_URI ?>wap/index/images/close.png" class="close_splash" alt="">
        <img src="<?= UPLOAD_BASE_URI.$kaiPing->media->uri ?>" onclick="window.location.href='<?= $kaiPing->link ?>'" alt="">
    </div>
<?php } ?>

    <script>
        function closeAdv()
        {
            $('.mask').addClass('hide');
            $('.pop').addClass('hide');
            document.body.removeEventListener('touchmove', eventTarget, false);
        }

        function eventTarget(event)
        {
            event.preventDefault();
        }

        function checkStatus()
        {
            $.ajax({
                type:'get',
                url:'/site/xs',
                success:function (code) {
                    //会员是否是登录状态
                    if (!code.isLoggedIn) {
                        $('#login').removeClass('hide');
                        $('#loginNewPeople').removeClass('hide');
                    } else {
                        $('.relative .signature').css('top','5.2rem');
                    }
                    //判断是否是出借者
                    if (!code.isInvestor) {
                        $('#loginNewPeople').removeClass('hide');
                    } else {
                        if (Cookies.get('showIndexPop')) {
                            Cookies.remove('showIndexPop');
                            $('.mask,.first-popover').show();
                            $('.second-popover').hide();
                        }
                    }
                    //判断个人出借总额大于五万时，前端页面显示总金额
                    if (code.showplatformStats) {
                        $("#totalTradeAmount").removeClass('hide');
                    }
                }
            })
        }

        var guiZe = '<?= (empty($kaiPing) || empty($kaiPing->media)) ? '' : $kaiPing->media->uri ?>';
    </script>
    <script type="text/template" id="banner_text">
        <?php foreach($hotActs as $act) {
            if (!is_null($act->media)) {
        ?>
                <a class="swiper-slide" href="<?= $act->getLinkUrl() ?>"><img src="<?= UPLOAD_BASE_URI . $act->media->uri ?>" alt=""> </a>
        <?php
            }
        }
        ?>
    </script>

<?php
$this->registerJs(<<<JSFILE
    $(function (){
        addToken();
        FastClick.attach(document.body);
        
        $("img").lazyload({
            threshold: 200
        });
        $('.popover-ctn .btn_02').on('click', function(){
            $('.first-popover').hide();
            $('.second-popover,.mask').show();
        });
        //瑞安渠道
        var source = Cookies.get('campaign_source');
        if (source == 'rarb') {
            $('.channel').removeClass('hide');
        }
        //开屏图
        $('.close_splash').on('click', closeAdv);
        if (guiZe) {
            if (Cookies.get('splash_show') !== guiZe) {
                document.body.addEventListener('touchmove', eventTarget);
                Cookies.set('splash_show', guiZe);
                $('.mask').removeClass('hide');
                $('.pop').removeClass('hide');
                setTimeout(closeAdv, 6000);
            }
        }
        //统计数据
        $.get('/site/stats-for-index', function (data) {
            $('#totalTradeAmount i').html(WDJF.numberFormat(accDiv(data.totalTradeAmount, 100000000), 0));
            $('#totalRefundAmount span').html(WDJF.numberFormat(accDiv(data.totalRefundAmount, 10000), 0));
            $('#totalRefundInterest span').html(WDJF.numberFormat(accDiv(data.totalRefundInterest, 10000), 0));
            
            if ($('#totalCharityAount').length > 0) {
                $('#totalCharityAount').html(WDJF.numberFormat(data.totalCharityAount, 0)+'元'); 
            }
        });
        //判断首页上方登录状态
        checkStatus();
        
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('footer').css({'margin-bottom': 0});
        }
         //替换首页banner图
        $('#index_banner').html($('#banner_text').text());
        //轮播图
        var mySwiper = new Swiper('.swiper-container', {
            autoplay: 4000,
            autoplayDisableOnInteraction: false,
            loop: true,
            pagination: '.swiper-pagination',
        });
    })
JSFILE
)?>
