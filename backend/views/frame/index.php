<?php

$this->title = '首页';
$this->loadAuthJs = false;

?>

<?php $this->beginBlock('block1eft'); ?>
<ul class="page-sidebar-menu hidden-phone hidden-tablet">
    <li>
        <div class="sidebar-toggler hidden-phone"></div>
    </li>
    <li>
        <a href="javascript:;">
            <i class="icon-th-list"></i>
            <span class="title">管理首页</span>
            <span class="arrow "></span>
        </a>
    </li>
</ul>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                欢迎使用后台管理系统
            </h3>
            <h4 class="hk-notice hk-notice-item"></h4>
            <h4 class="hk-notice draw-notice-item"></h4>
            <h4 class="hk-notice bankupdate-notice-item"></h4>
        </div>
    </div>
</div>

<script>
    $(function() {
        $.get('/product/productonline/hk-stats-count', function(data) {
            $('.hk-notice-item').html('7天内有<a href="/product/productonline/list?days=7">'+ data.week +'</a>个项目等待还款；当天有<a href="/product/productonline/list?days=1">'+ data.today +'</a>个项目等待还款！');
        });

        $.get('/user/user/draw-stats-count', function(data) {
            $('.draw-notice-item').append('<br>当月提现次数到达3次的用户有<a href="/user/user/draw-limit-list?times=3">'+ data.small +'</a>人；提现次数到达5次的用户有<a href="/user/user/draw-limit-list?times=5">'+ data.large +'</a>人！');
        });

        $.get('/datatj/bank/count-for-update', function(data) {
            var noticeClass = '';

            if (0 === data) {
                noticeClass = 'notice-font';
            }

            $('.bankupdate-notice-item').append('<br>截止当前有<a href="/datatj/bank/update-list" class="'+noticeClass+'">'+data+'</a>条超过14天的换卡记录需要处理！');
        });
    });
</script>

<style type="text/css">
    a.notice-font {
        color: #000 !important;
    }
</style>
<?php $this->endBlock(); ?>