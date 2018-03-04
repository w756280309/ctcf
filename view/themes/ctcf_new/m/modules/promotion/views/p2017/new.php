<?php

use common\models\adv\Share;

$this->title = '2017楚天财富年报';
$hostInfo = Yii::$app->params['clientOption']['host']['wap'];
$this->share = new Share([
    'title' => '这是我的2017年报，快来看看吧！',
    'description' => '楚天财富，市民身边的财富管家',
    'imgUrl' => 'https://static.wenjf.com/upload/link/link1515029207433498.png',
    'url' => $hostInfo.'/promotion/p2017/s2',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180102/css/share.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<div class="flex-content new-share" id="app">
    <div class="new-share-nav">
        <div class="new-nav-contain">
            <p>至今，楚天财富已安全运营<span><?= $platSafeDays ?></span>天</p>
            <p>已累计兑付<span><?= $platRefundAmount ?></span>亿元</p>
            <p>兑付率达<span>100%</span></p>
            <p>为客户赚取<span><?= $platRefundInterest ?></span>亿元</p>
            <p>未来，楚天财富将继续与您携手同行</p>
        </div>
        <a @click="weixinShare" class="new-nav-button share">分享给好友</a>
    </div>
    <div v-if="isShowMask" @click="removeMask" style="position: fixed;left: 0;bottom:0;width: 100%;height: 100%;background: #000;opacity: 0.6;z-index: 11;" >
        <div style="position: fixed;left: 0;bottom:0;width: 100%;height: 100%;z-index: 12;text-align: right;">
            <img style="float: right;width: 80%;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/share.png" alt="">
        </div>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data:{
            isShowMask:false,
        },
        methods:{
            weixinShare:function(){
                console.log(navigator.userAgent);
                if(navigator.userAgent.indexOf('MicroMessenger') > -1 ) {
                    this.isShowMask = true;
                }
            },
            removeMask:function () {
                this.isShowMask = false;
            }
        }
    });
</script>