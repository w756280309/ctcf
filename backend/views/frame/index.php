<?php
$this->title = '首页 - 温都金服后台';
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
            <h4 class="hk-notice"></h4>
        </div>
    </div>
</div>
<script>
    var xhr = $.get('/product/productonline/hk-stats-count', function(data) {
        $('.hk-notice').html('7天内有<a href="/product/productonline/list?days=7">'+ data.week +'</a>个项目等待还款；当天有<a href="/product/productonline/list?days=1">'+ data.today +'</a>个项目等待还款！');
    });

    xhr.fail();
</script>
<?php $this->endBlock(); ?>

