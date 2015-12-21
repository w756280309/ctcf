<?php
/* @var $this yii\web\View */
$this->title = '后台管理系统';
?>
<?= $this->blocks['block1eft'] ?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">
                        欢迎使用后台管理系统
                </h3>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="120">程序版本: </td>
                <td width="250">温都金服 正式版 1.0.0</td>
                <td width="120">  </td>
                <td> </td>
            </tr>
            <tr>
                <td width="120">  </td>
                <td width="250"> </td>
                <td width="120">  </td>
                <td><font color=green> </font></td>
            </tr>
            <tr>
                <td width="120">  </td>
                <td width="250">
                    </td>
                <td width="120"> </td>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>
</div>

<?php $this->endBlock(); ?>
