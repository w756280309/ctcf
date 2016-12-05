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
        <table width="90%">
            <tr>
                <td width="15%"><strong>标的名称</strong></td>
                <td width="35%"><?= $loan->title ?></td>
                <td width="20%"><strong>标的账户余额(联动)</strong></td>
                <td width="30%"><?= StringUtils::amountFormat2($balance) ?>元</td>
            </tr>
        </table>
    </div>
</div>
<?php $this->endBlock(); ?>