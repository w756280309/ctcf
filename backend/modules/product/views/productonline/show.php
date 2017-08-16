<?php
$this->title = '查看项目';

use common\utils\StringUtils;
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/product/productonline/list">贷款管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/productonline/list">项目列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)">查看项目信息</a>
                </li>
            </ul>
        </div>
    </div>

    <div align="center">
        <table class="table">
            <tr>
                <td><strong>标的名称</strong></td>
                <td><?= $loan->title ?></td>
                <td><strong>标的账户余额(联动)</strong></td>
                <td><?= StringUtils::amountFormat2($balance) ?>元</td>
                <td><strong>募集金额</strong></td>
                <td><?= StringUtils::amountFormat2($loan->funded_money)?></td>
            </tr>
            <tr>
                <td><strong>用户实际支付金额</strong></td>
                <td><?= StringUtils::amountFormat2($paymentAmount)?></td>
                <td><strong>代金券金额</strong></td>
                <td><?= StringUtils::amountFormat2($couponAmount)?></td>
                <td><strong>是否已贴现</strong></td>
                <td><?= $couponTransfer ? '是' : '否'?></td>
            </tr>
        </table>
    </div>
</div>
<?php $this->endBlock(); ?>