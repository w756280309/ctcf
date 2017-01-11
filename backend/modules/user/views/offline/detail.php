<?php

use common\utils\StringUtils;

$this->title = '线下会员详情';
?>

<?php $this->beginBlock('blockmain'); ?>

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
                    <a href="/user/offline/user">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/offline/user">线下会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)">会员详情</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <div class="detail_font">会员账户详情</div>
            <table class="table table-condensed">
                <tr>
                    <td><strong>手机号</strong></td>
                    <td><?= $user->mobile ?></td>
                    <td><strong>真实姓名</strong></td>
                    <td><?= $user->realName ?></td>
                    <td><strong>身份证号</strong></td>
                    <td><?= StringUtils::obfsIdCardNo($user->idCard) ?></td>
                </tr>
                <tr>
                    <td><strong>积分</strong></td>
                    <td><?= StringUtils::amountFormat2($user->points) ?></td>
                    <td><strong>会员等级</strong></td>
                    <td>VIP<?= $user->level ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>