<?php
use common\models\product\OnlineProduct;

$this->title = '温都金服 - 首页';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/jquery.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/TouchSlide.1.1.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/jquery.classyloader.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/index.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerCssFile('/css/index.css', ['depends' => 'frontend\assets\WapAsset']);  //加载在depends之后
$this->registerCssFile('/css/first.css', ['depends' => 'frontend\assets\WapAsset']);

$dates = Yii::$app->functions->getDateDesc($deals->start_date);
$rate = number_format($deals->finish_rate * 100, 0);

?>

<div class="container">
    <div class="slideBox" id="slideBox">
        <div class="bd">
             <?php foreach($adv as $val): ?>
                <li> <a class="pic" href="<?= $val['link'] ?>"><img src="/upload/adv/<?= $val['image'] ?>" alt=""></a> </li>
             <?php endforeach; ?>
        </div>
        <div class="hd">
            <ul></ul>
        </div>
    </div>
 <!-- 添加修改部分 -->

 <!-- 新手区start  -->
    <div class="row new-box">
        <div class="new-head" onclick="window.location.href='/deal/deal/index?xs=1'">
            <div class="col-xs-8 col-sm-7 new-head-title">
                <div class="arrow-rg"></div>
                <div class="new-head-tit"><span>新手区</span><span class="new-head-txt">更短周期，更高收益</span></div>
            </div>
            <div class="col-xs-1 col-sm-3 "> </div>
            <div class="col-xs-3 col-sm-2 more"><a href="/deal/deal/index?xs=1">更多》</a></div>
        </div>

        <?php if (!empty($deals->jiaxi)) { ?>
        <ul class="row new-bottom" onclick="window.location.href='/deal/deal/detail?sn=<?= $deals->sn ?>'">
            <li class="col-xs-6 padding-5">
                <div class="xian1">
                    <div class="newcomer-badge"><img src="/images/badge.png" alt="猴年加息"></div>
                    <span class="interest-rate"><?= rtrim(rtrim(number_format($deals->yield_rate*100, 2), '0'), '.') ?>%</span>
                    <span class="interest-rate-add">+<?= $deals->jiaxi ?>%</span>
                </div>
            </li>
            <li class="col-xs-6 padding-5">
                <div class="new-bottom-rg">
                    <p ><span class="tishi"><?= rtrim(rtrim($deals->start_money, '0'), '.') ?>元起投，<?= $deals->getSpanDays() ?>天</span></p>
                    <?php if (OnlineProduct::STATUS_PRE === $deals->status) { ?>
                        <!-- 未开标 -->
                        <a class="zhuangtai weikaibiao" href="/deal/deal/detail?sn=<?= $deals->sn ?>"><?= $dates['desc'] ?> <?= date('H:i', $deals->start_date) ?></a>
                        <?php } elseif (OnlineProduct::STATUS_NOW === $deals->status) { ?>
                        <!-- 进度 % -->
                        <div style="margin:10px 3px; border:1px solid #fe9b00; ">
                            <a class="a-progress-bg"  style="width:<?= $rate ?>%;"></a>
                            <a class="zhuangtai a-progress" href="/deal/deal/detail?sn=<?= $deals->sn ?>"><?= $rate ?>%</a>
                        </div>
                        <?php } else { ?>
                        <div class="zhuangtai" style="margin:10px 3px; background-color: #9fa0a0;">
                            <font style="color: #fff; line-height: 40px;"><?= Yii::$app->params['productonline'][$deals->status] ?></font>
                        </div>
                    <?php } ?>
                </div>
            </li>
        </ul>
        <?php } else { ?>
        <ul class="row new-bottom" onclick="window.location.href='/deal/deal/detail?sn=<?= $deals->sn ?>'">
            <li class="col-xs-6 padding-5">
                <div class="xian">
                    <span class="interest-rate"><?= rtrim(rtrim(number_format($deals->yield_rate*100, 2), '0'), '.') ?>%</span>
                </div>
            </li>
            <li class="col-xs-6 padding-5">
                <div class="new-bottom-rg">
                    <p><span class="tishi"><?= rtrim(rtrim($deals->start_money, '0'), '.') ?>元起投，<?= $deals->getSpanDays() ?>天</span></p>
                    <?php if (OnlineProduct::STATUS_PRE === $deals->status) { ?>
                        <!-- 未开标 -->
                        <a class="zhuangtai weikaibiao" href="/deal/deal/detail?sn=<?= $deals->sn ?>"><?= $dates['desc'] ?> <?= date('H:i', $deals->start_date) ?></a>
                        <?php } elseif (OnlineProduct::STATUS_NOW === $deals->status) { ?>
                        <!-- 进度 % -->
                        <div style="margin:10px 3px; border:1px solid #fe9b00; ">
                            <a class="a-progress-bg"  style="width:<?= $rate ?>%;"></a>
                            <a class="zhuangtai a-progress" href="/deal/deal/detail?sn=<?= $deals->sn ?>"><?= $rate ?>%</a>
                        </div>
                        <?php } else { ?>
                        <div class="zhuangtai" style="margin:10px 3px; background-color: #9fa0a0;">
                            <font style="color: #fff; line-height: 40px;"><?= Yii::$app->params['productonline'][$deals->status] ?></font>
                        </div>
                    <?php } ?>
                </div>
            </li>
        </ul>
        <?php } ?>
    </div>
<!--  新手区end  -->
    <div style="width:100%;height:16px;background: #f7f8f8; border-bottom:1px solid #e6e7e7;"></div>
<!-- 理财区start -->
    <div class="licai-box">
        <div class="licai-head">
            <div class="col-xs-8 col-sm-7 new-head-title">
                <div class="arrow-rg"></div>
                <div class="new-head-tit"><span>理财区</span><span class="new-head-txt">固定收益，稳定本息</span></div>
            </div>
            <div class="col-xs-4 col-sm-5"></div>
        </div>
        <div style="clear: both;"></div>
        <div class="row margin-add">
            <div class="col-xs-4 padding-clear"><a href="/deal/deal/index?cat=1"><img src="/images/type1.png" alt="短期产品" /></a> </div>
            <div class="col-xs-4 padding-clear"><a href="/deal/deal/index?cat=2"><img src="/images/type2.png" alt="政府平台" /></a> </div>
            <div class="col-xs-4 padding-clear"><a href="/order/booking/detail?pid=1"><img src="/images/type3.png" alt="股权投资" /></a> </div>
        </div>

    </div>
    <!-- 理财区end -->
</div>

<!--   end  -->
<!--footer-->
    <div class="row navbar-fixed-bottom footer">
        <div class="col-xs-4 footer-title">
            <div class="footer-inner">
                <a href="/" class="shouye1"><span class="shouye"></span>首页</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner1">
                <a href="/deal/deal/index"><span class="licai"></span>理财</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner2">
                <a href="/user/user"><span class="zhanghu"></span>账户</a>
            </div>
        </div>
    </div>


