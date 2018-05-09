<?php

$this->title = '11月理财节';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171111/less/change-one.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<div class="flex-content" id="app">
    <img class="top-part" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/banner-one.png" alt="图">
    <img class="text-tip" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/text-tip.png" alt="空心字">
    <div class="bottom-part ">
        <img class="card-box" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pic_card.png" alt="图">
        <div class="result-box clearfix">
            <div class="result-box-task lf">
                <p >成功邀请好友</p>
                <p >注册并出借</p>
                <p class="center-txt"><span class="inviteTask">0</span>/1</p>
                <a href="/user/invite" class="link">去邀请</a>
            </div>
            <div class="result-box-task rg">
                <p class="top-invest">任意出借一笔</p>
                <p class="center-txt"><span class="investTask">0</span>/1</p>
                <a href="/deal/deal/index" class="link">去出借</a>
            </div>
        </div>
        <p class="last-tips">本活动最终解释权归楚天财富所有</p>
    </div>
</div>
<script>
    var app = new Vue({
        el: 'app',
        data: {
            inviteTask: 0,
            investTask: 0
        },
        created: function() {
            this.init();
        },
        methods: {
            init: function () {
                var _this = this;
                _this.inviteTask = <?= $inviteTask ?>;
                _this.investTask = <?= $investTask ?>;
                $('.inviteTask').html(_this.inviteTask); // 邀请
                $('.investTask').html(_this.investTask);// 出借
                if (_this.inviteTask == '1') {
                    $('.inviteTask').parents('.result-box-task').addClass('finished');
                }
                if (_this.investTask == '1') {
                    $('.investTask').parents('.result-box-task').addClass('finished');
                }
            }
        }
    });
    Vue.config.devtools = false;
</script>
