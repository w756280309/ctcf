<?php

use common\models\code\Code;
use yii\widgets\LinkPager;

$this->title = '兑换码列表';
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                兑换码管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/code/goods-list">商品列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">兑换码列表</a>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="/growth/code/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><span class="title">兑换码</span></td>
                        <td>
                            <input type="text" class="m-wrap" style="margin-bottom: 0px" id="title" name='code'
                                   value="" placeholder="请输入兑换码"/>
                        </td>
                        <td>
                            <div class="search-btn" align="right">
                                <button type='submit' class="btn blue btn-block button-search">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!--search end -->
        <div class="portlet-body">
            <table class="table table-condensed">
                <tr>
                    <td><strong>商品名称</strong></td>
                    <td><?= $goods->name ?></td>
                </tr>
            </table>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th class="valign-middle">兑换码</th>
                    <th class="valign-middle">客户手机号</th>
                    <th class="valign-middle">领取时间</th>
                    <th style="width: 20%" class="valign-middle"><center>操作</center></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $code) : ?>
                    <tr>
                        <td class="valign-middle"><?= $code->code ?></td>
                        <td class="valign-middle"><?= empty($code->user_id) ? '--' : $code->user->mobile ?></td>
                        <td class="valign-middle"><?= $code->isUsed ? $code->usedAt : '--' ?></td>
                        <td class="valign-middle">
                            <center>
                                <?php if (!$code->isUsed && Code::TYPE_COUPON !== $code->goodsType) { ?>
                                <a href="/growth/code/pull-user?id=<?= $code->id ?>" class="btn mini green"><i class="icon-edit"></i>补充领取人</a>
                                <?php } else { ?>
                                    ----
                                <?php } ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination" style="text-align:center;clear: both">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
</div>
    <script>
        $(function() {
            $('.search-btn').bind('click', function () {
                if ('' === $('#title').val()) {
                    layer.msg('请填写兑换码', {icon: 2});
                    return false;
                }
            });
        });
    </script>
<?php $this->endBlock(); ?>
