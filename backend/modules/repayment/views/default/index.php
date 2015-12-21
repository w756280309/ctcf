<?php
/* @var $this yii\web\View */

$this->title = 'Show Main';
?>
<?php $this->beginBlock('block1eft'); ?>

<td width="180" id="page_left" height="100%" align="left" valign="top">
    <!--左边-->
    <div id="nav" class="scroll-pane" style="height: 591px;"><div class="title"><a href="javascript:void(0)">投资明细首页</a></div>
        <ul class="load menu">
            <li><a href="/order/order/index" target="main">投资明细</a></li>
            <li><a href="/order/order/edit" target="main">添加新投资</a></li>
        </ul>
    </div>
</td>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('blockmain'); ?>
<td height="100%" style="width:100%\9" align="left" valign="top">
    <!--右边-->
    <div style="position:relative; width:100%; height:100%;">
        <div class="loading" id="content_loading" style="display: none;"></div>
        <iframe id="main" name="main" src="/site/index" frameborder="0"></iframe>

    </div>
</td>
<?php $this->endBlock(); ?>

