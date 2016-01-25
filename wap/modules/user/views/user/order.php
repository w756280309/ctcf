<?php
$this->title="我的理财";
?>
<link rel="stylesheet" href="/css/bind.css"/>
    <link rel="stylesheet" href="/css/licai.css"/>
    <link rel="stylesheet" href="/css/swiper.min.css">
    <script type="text/javascript">
        var total='<?=$list['header']['count'] ?>';
        var size='<?=$list['header']['size'] ?>';
        var tp='<?=$list['header']['tp'] ?>';
        var cp='<?=$list['header']['cp'] ?>';
        var ctype='<?=(empty($type)?0:$type) ?>';
        </script>
    <script src="/js/jquery.js"></script>
    <script src="/js/swiper.min.js"></script>
    <script src="/js/licai.js"></script>
    
    <!-- Swiper -->
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide <?= empty($type)?"dian":"" ?>" onclick="window.location.href='/user/user/myorder'">全部</div>
                    <div class="swiper-slide <?= $type==2?"dian":"" ?>" onclick="window.location.href='/user/user/myorder?type=2'">募集期</div>
                    <div class="swiper-slide <?= $type==3?"dian":"" ?>" onclick="window.location.href='/user/user/myorder?type=3'">满标</div>
                    <div class="swiper-slide <?= $type==7?"dian":"" ?>" onclick="window.location.href='/user/user/myorder?type=7'">提前结束</div>
                    <div class="swiper-slide <?= $type==5?"dian":"" ?>" onclick="window.location.href='/user/user/myorder?type=5'">还款中</div>
                    <div class="swiper-slide <?= $type==6?"dian":"" ?>" onclick="window.location.href='/user/user/myorder?type=6'">已还清</div>
                </div>
                <!-- Add Pagination -->
                <!--<div class="swiper-pagination"></div>-->
            </div>
<?php if($list['data']){ foreach ($list['data'] as $o){ ?>
    <!--时间-->
     <div class="row times">
          <div class="col-xs-4"><?= $o['order_time'] ?></div>
          <div class="col-xs-8"></div>
     </div>
    <div class="row column">
        <div class="hidden-xs col-sm-1" style="height:50px;"></div>
        <div class="col-xs-12 col-sm-10 column-title"><span><?=$o['title']?></span></div>
        <div class="<?= in_array($o['pstatus'], [1,2])?"column-title-rg":"column-title-rg1";?>"><?=$o['statusval']?></div>
        <div class="hidden-xs col-sm-1" style="height:50px;"></div>
        <div class="container" style="margin-top: 60px">
            <ul class="row column-content">
                <li class="hidden-xs col-sm-1"></li>
                <li class="col-xs-6 col-sm-5">
                    <div>投资金额<span><?=$o['order_money']?>元</span></div>
                </li>
                <li class="col-xs-6 col-sm-5">
                    <div><?= ($o['pstatus']==6)?"实际收益":"预期收益" ?><span class="money" alt=""><?=$o['profit']?>元</span></div>
                </li>
                <li class="hidden-xs col-sm-1"></li>
            </ul>
            <ul class="row column-content">
                <li class="hidden-xs col-sm-1"></li>
                <li class="col-xs-6 col-sm-5">
                    <div>年化收益
                        <span>
                         <?= doubleval(number_format($o['yield_rate']*100, 2)) ?>% 
                         <?php if (!empty($o['jiaxi'])) { ?> + <?= doubleval(number_format($o['jiaxi']*100, 2)) ?>% <?php } ?>
                        </span>
                    </div>
                </li>
                <li class="col-xs-6 col-sm-5">
                    <div>结清时间<span><?=$o['returndate']?></span></div>
                </li>
                <li class="hidden-xs col-sm-1"></li>
            </ul>
        </div>
    </div>
<?php } ?>
    <div class="load">加载更多</div>
<?php } else{ ?>   
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>
