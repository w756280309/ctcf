<?php
/* @var $this yii\web\View */

$this->title = 'Show Main';
use common\models\AuthSys;
$menus = AuthSys::getMenus('U1000000');
?>
<?php $this->beginBlock('block1eft'); ?>

<td width="180" id="page_left" height="100%" align="left" valign="top">
    <!--左边-->
    <div id="nav" class="scroll-pane" style="height: 591px;"><div class="title"><a href="javascript:void(0)">管理首页</a></div>
        <ul class="load menu">
        	<li><a href="/user/usertype/index" target="main">会员类型</a></li>
            <li><a href="/user/usertype/edit" target="main">添加会员类型</a></li>
            <li><a href="/user/user/list" target="main">会员列表</a></li>
            <li><a href="/user/user/edit" target="main">添加个人会员</a></li>
            <li><a href="/user/user/editorg" target="main">添加机构会员</a></li>
        <!--    <?php foreach ($menus as $val){ ?>
                <li><a href="/<?=$val['path']?>" target="main"><?=$val['auth_name']?></a></li>
            <?php } ?>-->
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

