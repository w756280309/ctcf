<?php

use common\utils\StringUtils;
use yii\widgets\LinkPager;

$this->title = '线下会员列表';
?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .search_form td input {
        margin: 0px;
    }
</style>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员管理模块【主要包含投资会员和融资会员的管理】</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/offline/list">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/offline/list">线下会员</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">线下会员列表</a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th>手机号</th>
                    <th>真实姓名</th>
                    <th>证件号</th>
                    <th><center>操作</center></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user->mobile ?></td>
                        <td><a href="/user/offline/detail?id=<?= $user->id ?>"><?= $user->realName ?></a></td>
                        <td><?= StringUtils::obfsIdCardNo($user->idCard) ?></td>
                        <td>
                            <center>
                                <a href="/user/offline/detail?id=<?= $user->id ?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($users)) { ?>
            <div class="note">暂无数据</div>
        <?php } ?>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>
