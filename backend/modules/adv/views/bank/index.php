<?php

use yii\widgets\LinkPager;
use common\models\AuthSys;

$menus = AuthSys::getMenus('A100000');
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理
                <small>运营管理模块【主要包含广告管理和银行管理】</small>
                <a href="/adv/bank/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    新增银行 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/bank/index">银行管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">银行列表</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
            <tr>
                <th style="text-align: center">银行</th>
                <th style="text-align: center">个人网银充值</th>
                <th style="text-align: center">企业网银充值</th>
                <th style="text-align: center">快捷充值</th>
                <th style="text-align: center">快捷充值限额</th>
                <th style="text-align: center">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lists as $key => $val) : ?>
                <tr>
                    <td style="text-align: center"><?= $val->bankName ?></td>
                    <td style="text-align: center">
                        <?php if ($val->isPersonal): ?>
                            <i class="icon-ok green" style="color: green;"></i>
                        <?php else: ?>
                            <i class="icon-remove" style="color: red;"></i>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php if ($val->isBusiness): ?>
                            <i class="icon-ok green" style="color: green;"></i>
                        <?php else: ?>
                            <i class="icon-remove" style="color: red;"></i>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php if ($val->isQuick): ?>
                            <i class="icon-ok green" style="color: green;"></i>
                        <?php else: ?>
                            <i class="icon-remove" style="color: red;"></i>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?= $val->quota ?>
                    </td>
                    <td style="text-align: left;width: 130px;">
                        <a href="javascript:void(0)" onclick="edit_blank(<?= $val->id ?>,'<?= $val->bankName ?>')"
                           class="btn mini green">
                            <i class="icon-edit"></i>编辑
                        </a>
                        <?php if ($val->isDisabled): ?>
                            <a href="javascript:void(0);" class="btn mini red"><i class="icon-minus-sign"></i>
                                已停用
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination" style="text-align:center;"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<script type="text/javascript">
    function edit_blank(id, name) {
        layer.open({
            type: 2,
            title: name,
            shadeClose: true,
            shade: 0.8,
            area: ['450px', '300px'],
            content: '/adv/bank/edit?id=' + id
        });
    }
</script>
<?php $this->endBlock(); ?>
