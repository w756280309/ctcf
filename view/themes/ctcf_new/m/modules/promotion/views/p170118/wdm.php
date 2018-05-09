<?php

$this->title = '楚天财富落地页';
$this->share = $share;
$config = json_decode($promo->config, true);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css?v=20170119">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170119">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/wendumao/css/index.css?v=20170119">
<script  src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/jquery.lazyload.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/com.js"></script>

<div class="container flex-content" id="scrolltop">
    <?php if (!defined('IN_APP')) { ?>
        <div class="header">
            <ul class="clearfix">
                <li class="lf f16"><img src="<?= FE_BASE_URI ?>wap/wendumao/images/logo.png" alt="">楚天财富国资平台</li>
                <li class="rg f13"><a class="" href="/">返回首页</a></li>
            </ul>
        </div>
    <?php } ?>

    <?php if (isset($config['image'])) { ?>
        <div class="banner">
            <img src="<?= $config['image'] ?>" alt="">
        </div>
    <?php } ?>

    <div class="phonenum">
        <div class="inputphone"><input class="f15" type="tel" placeholder="请输入手机号" maxlength="11"></div>
        <a class="f22">马上领取</a>
    </div>

    <?php if (isset($config['rules'])) { ?>
        <div class="regular">
            <ul class="f15">
                <li>活动规则：</li>
                <?php foreach ($config['rules'] as $key => $rule) { ?>
                    <li><?= $key + 1 ?>.<?= $rule ?></li>
                <?php } ?>
                <li class="f12 statement">*购买转让产品不参与活动</li>
            </ul>
        </div>
    <?php } ?>

    <div class="gift">
        <p class="f18 "><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_01.png" alt=""> 积分商城，更多好礼等你拿 <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_01.png" alt=""></p>
        <ul class="clearfix">
            <li class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/gift_01.png" alt=""></li>
            <li class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/gift_02.png" alt=""></li>
            <li class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/gift_03.png" alt=""></li>
            <li class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/gift_04.png" alt=""></li>
        </ul>
    </div>

    <div class="choosereason">
        <p class="f18 "><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_01.png" alt=""> 为什么选择国资平台-楚天财富 <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_01.png" alt=""></p>
        <span class="reasonone f15">01  收益稳健</span>
        <p class="f13">如果5万元存一年，预期获得收益对比</p>
        <ul>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/chart_01.png" alt=""></li>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/chart_02.png" alt=""></li>
        </ul>
    </div>
    <div class="newnorm">
        <p class="newnormtitle f15">新手专享<span class="f12">专享体验</span><span class="f12">灵活稳健</span></p>
        <a href="/deal/deal/index">
            <p class="f15">新手专享标</p>
            <p class="f45"><em>10<i class="f25">%</i><span class="f15"></span></em></p>
            <p class="f12">借贷双方约定利率</p>
            <ul class="clearfix">
                <li class="f14 lf text-align-lf"><img class="icon5"  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_05.png" alt=""> 1,000元起借</li>
                <li class="f14 lf comred"><img class="icon6"  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_06.png" alt=""> 15天理财期限</li>
                <li class="f14 lf text-align-rg"><img class="icon7"  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_07.png" alt=""> 限购一万</li>
                <li class="f18 lf">去理财</li>
            </ul>
        </a>
        <p class="f15 intro"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_02.png" alt=""> 楚天财富历史兑付率100%</p>
    </div>

    <div class="choosereason2">
        <span class="reasonone f15" style="margin-bottom: 0.2666667rem;">02  股东强势 国资背景</span>
        <div class="shareholder">
            <p class="f15">什么是楚天财富？</p>
            <ul>
                <li></li>
                <li class="f15" style="color: #333333;text-align: left;">楚天财富是隶属湖北日报新媒体集团旗下的理财平台。甄选各类金融机构、优质企业理财产品。提供银行级理财服务，保障用户资金，安享稳健收益。</li>
            </ul>
        </div>

        <div class="shareholder">
            <p class="f15">股东背景</p>
            <ul>
                <li></li>
                <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/shareholde_01.png" alt=""></li>
            </ul>
        </div>

        <div class="platform">
            <p class="f15">平台优势</p>
            <ul class="clearfix">
                <li></li>
                <li class="lf platformList">
                    <div class="platformListFirst">
                        <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/platform_01.png" alt="">
                        <div class="f15">国资背景</div>
                        <div class="f14">湖北日报新媒体集团旗下</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListSecond">
                        <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/platform_02.png" style="width: 1.1rem;" alt="">
                        <div class="f15">资金稳妥</div>
                        <div class="f14">第三方资金托管</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div>
                        <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/platform_03.png" alt="">
                        <div class="f15">收益稳健</div>
                        <div class="f14">预期收益5.5~9%</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListLast">
                        <img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/platform_04.png" alt="">
                        <div class="f15">产品优质</div>
                        <div class="f14">国企、政信类产品</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="leader">
        <span class="reasonone f15">03  本地服务方便快捷</span>
        <dl class="clearfix">
            <dt class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/leader_01.png" alt=""></dt>
            <dd class="lf" style="margin-top: -0.17rem;font-size: 0.4rem;">
                楚天财富服务中心位于武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层湖北日报分类业务处。
            </dd>
        </dl>
        <dl class="clearfix">
            <dt class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/leader_02.png" alt=""></dt>
            <dd class="lf" style="margin-top: 0.2rem;font-size: 0.4rem;">
                四楼办公区，宽敞明亮，为客户提供更多的咨询、线下活动、积分兑换等服务。
            </dd>
        </dl>
    </div>

    <div class="media">
        <span class="reasonone f15" style="margin-bottom: 0.4rem;">04  权威媒体合作支持</span>
        <ul>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/media_01.png" alt=""></li>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/media_02.png" alt=""></li>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/media_03.png" alt=""></li>
            <li><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/media_04.png" alt=""></li>
        </ul>
    </div>


    <div class="active">
        <span class="reasonone f15" style="margin-bottom: 0.4rem;">05  会员活动丰富</span>
        <ul class="clearfix f12">
            <li class="lf" style="margin-bottom: 0.4rem;"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/active_01.png" alt="">欧洲城波比披萨店“圣诞节披萨制作”活动</li>
            <li class="rg" style="margin-bottom: 0.4rem;"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/active_02.png" alt="">龙湾莲情谷一日游体验农家乐趣</li>
            <li class="lf"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/active_03.png" alt="">独家冠名的瞿溪户外协会年会</li>
            <li class="rg"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/active_04.png" alt="">联合林子贝贝教育中心举办的亲子活动</li>
        </ul>
        <a class="f22" href="#scrolltop">马 上 领 取</a>
        <p class="f12">*本次活动最终解释权归楚天财富所有</p>
    </div>

    <div class="bottomside">
        <p class="f12 botintro">如有问题请拨打客服电话或关注楚天财富公众号</p>
        <dl>
            <dt style="width: 70%;">
            <div class="clearfix">
                <img class="lf"  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/icon_03.png" alt="">
                <p class="lf">
                    <a class="f15" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>" style="line-height:0.6266667rem;"><?= Yii::$app->params['platform_info.contact_tel'] ?></a>
                    <span class="f12" style="line-height:0.56rem;">工作时间：8:30~20:00</span>
                </p>
            </div>
            <div class="f12 netaddress">官网地址：<a href="<?= defined('IN_APP') ? '/' : 'https://www.wenjf.com' ?>">www.wenjf.com</a></div>
            <div class="f12 address clearfix"><p class="lf">公司地址：</p><p class="specialp lf">武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层</p></div>
            </dt>
            <dd class="erweima f11"><img  data-original="<?= FE_BASE_URI ?>wap/wendumao/images/erweima.png" alt=""><p>微信公众号</p></dd>
        </dl>
        <p class="f11">*理财非存款，产品有风险，出借须谨慎</p>
    </div>
</div>
<script>
    $(function() {
        FastClick.attach(document.body);
        $("img").lazyload({
            threshold : 200
        });
        $('.inputphone input').on('keyup',function() {
            var num = moduleFn.clearNonum($('.inputphone input'))
        });

        var allowClick = true;
        $('.phonenum a').on('click',function(e) {
            e.preventDefault();

            if (!allowClick) {
                return;
            }

            allowClick = false;

            var phonenum =  $('.inputphone input').val();
            if (phonenum.length < 11) {
                toastCenter('请输入正确的手机号', function () {
                    allowClick = true;
                });
                return;
            } else {
                if (moduleFn.check.mobile(phonenum)) {
                    var xhr = $.get('/promotion/p170118/validate-mobile?mobile='+phonenum);

                    xhr.done(function(data) {
                        var toUrl = data.toUrl;

                        if (data.code) {
                            toastCenter(data.message, function() {
                                if ('' !== toUrl) {
                                    location.href = toUrl;
                                }

                                allowClick = true;
                            });
                        } else {
                            location.href = toUrl;
                        }
                    });

                    xhr.fail(function () {
                        toastCenter('系统繁忙,请稍后重试!', function() {
                            allowClick = true;
                        });
                    })
                } else {
                    allowClick = true;

                    return false;
                }
            }
        })
    });
</script>
