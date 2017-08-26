<?php
$list = [];
foreach ($awardlist as $k=>$award) {
    $list[$k]['gifts_num'] = FE_BASE_URI.$award['path'];
    $list[$k]['gifts_title'] = $award['name'];
    $list[$k]['gifts_time'] = $award['note'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>七夕闯关作战</title>
    <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
    <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/gifts-list.css">
    <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/index.css">
    <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
    <script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
    <script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
    <script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
    <script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
    <script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
    <script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
    <script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>

</head>
<body>
<div class="flex-content">
    <div class="part-one"></div>
    <div class="part-two">
        <?php
            if ($status['code'] == 0) {
                echo '<a href="javascript:;" class="start"></a>';
                echo '<a href="javascript:;" class="start"></a>';
                echo '<a href="javascript:;" class="start"></a>';
                echo '<div class="start"></div>';
            } else if($status['code'] == 2) {
                echo '<a href="javascript:;" class="end"></a>';
                echo '<a href="second"></a>';
                echo '<a href="javascript:;" class="end"></a>';
                if (!is_null($user)) {
                    echo '<div class="my-prize"></div>';
                } else {
                    echo '<div class="unlogin"></div>';
                }
            } else {
                echo '<a href="first"></a>';
                echo '<a href="second"></a>';
                echo '<a href="third"></a>';
                if (!is_null($user)) {
                    echo '<div class="my-prize"></div>';
                } else {
                    echo '<div class="unlogin"></div>';
                }
            }
        ?>
        <!-- 活动结束后7天，不再显示       -->


    </div>
</div>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/js/gifts-list.js"></script>
<script >
    giftsList({
        isGifts:true,//有奖品，无奖品为false
        closeImg:'<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-close.png',
        list:<?= json_encode($list) ?>
    });
</script>
<script>
    $(".unlogin").on('click',function(){
        //登录
        var module = poptpl.popComponent({
            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
            popBorder:0,
            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
            btnMsg : "去登录",
            popTopColor:"#fb3f5a",
            title:'<p style="font-size:0.50666667rem; margin: 1rem 0 2.2rem;">您还未登录哦！</p>',
            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
            popBtmBorderRadius:0,
            popBtmFontSize : ".50666667rem",
            btnHref:'/site/login?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>'
        });
    })

    //活动是否开始
    $('.start').on('click',function(){
        toastCenter('活动未开始');
    })
    $('.end').on('click',function(){
        toastCenter('活动已结束');
    })

    $(function(){
        FastClick.attach(document.body);
        var myScroll = new iScroll('wrapper',{
            vScrollbar:false,
            hScrollbar:false
        });

        //我的奖品按钮
        $(".my-prize").on("click",function(){
            $('.prizes-box').show();
            $('body').on('touchmove',eventTarget, false);
            myScroll.refresh();//点击后初始化iscroll
        });

        $('.pop_close').on('click',function(){
            $('.prizes-box').hide();
            $('body').off('touchmove');
        });
        function eventTarget(event) {
            var event = event || window.event;
            event.preventDefault();
        }
    });
</script>
</body>
</html>