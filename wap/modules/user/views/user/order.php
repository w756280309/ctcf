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
    <div class="row" id='licai-title'>
    	<div class="col-xs-4">
    		<div class="licai-title1"><img src="/images/licaiGang.png" alt=""> 累计投资金额</div>
    		<div class="licai-money">10.000<span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title2"><img src="/images/licaiMoney.png" alt=""> 累计获得收益</div>
    		<div class="licai-money">10.000<span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title3"><img src="/images/licaiHead.png" alt=""> 待还清项目</div>
    		<div class="licai-money">5<span>个</span></div>
    	</div>
    </div>
    <div class="row" id='licai-xiangmu'>
    	<div class="col-xs-12">我投资过的项目</div>
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
        <!-- 修改 -->
        <div class="container" id='project-box'>
        	<div class="row">
        		<div class="col-xs-4">
        			<div>11%</div>
        			<p>年化收益</p>
        		</div>
        		<div class="col-xs-4">
        			<div>60天</div>
        			<p>期限</p>
        		</div>
        		<div class="col-xs-4">
        			<div>5188.05元</div>
        			<p>预期收益</p>
        		</div>
        	</div>
        </div>
    </div>
<?php } ?>
    <div class="load">加载更多</div>
<?php } else{ ?>   
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>
