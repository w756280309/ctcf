<?php

$this->title = '积分批量导入';
use yii\widgets\ActiveForm;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                积分记录导入
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">积分批量导入</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row-fluid">
            <h4>文件格式示例</h4>
            <table class="table">
                <tr>
                    <th>手机号</th>
                    <th>身份证号(请使用文本格式，否则可能导入失败)</th>
                    <th>是线上用户(1:线上用户;0:线下用户)</th>
                    <th>待发积分(整数)</th>
                    <th>描述(会显示到前台，请谨慎填写, 默认为"投资奖励")</th>
                </tr>
                <tr>
                    <td>138********</td>
                    <td>110101********</td>
                    <td>1</td>
                    <td>100</td>
                    <td>土豪奖</td>
                </tr>
                <tr>
                    <td>188********</td>
                    <td>110102********</td>
                    <td>0</td>
                    <td>200</td>
                    <td>投资奖励</td>
                </tr>
            </table>
        </div>
        <div class="row-fluid">
            <h4>请选择文件并导入</h4>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-group' ,'id' => 'import_form', 'style' => 'margin-left:8px']]); ?>
            <?= \yii\helpers\Html::fileInput('pointsFile', null, ['class' => 'form-control', 'accept' => '.xlsx, .xls'])?>
            <?= \yii\helpers\Html::submitButton('预览', ['class' => 'btn btn-default', 'id' => 'submit_btn'])?>
            <?php $form->end(); ?>
        </div>
        <div class="row-fluid">
            <h4>导入须知</h4>
            <ul>
                <li style="list-style-type:none;text-indent:-15px;">1、每条记录用户待操作的积分不应超过10万积分；</li>
                <li style="list-style-type:decimal;text-indent:-15px;">2、表格一次性导入不应超过千行，尽量分批进行导入，例如每次导入200条；</li>
                <li style="list-style-type:decimal;text-indent:-15px;">3、若导入的线下用户为已账户合并的用户，则积分依然会导入到其对应的线上用户中；</li>
                <li style="list-style-type:decimal;text-indent:-15px;">4、尽量避免进行重复导入，若不确定可以通过会员管理>>查看用户详情>>积分来确认。</li>
            </ul>
        </div>
    </div>
</div>
<script>
    $('#submit_btn').click(function(e){
        e.preventDefault();
        $(this).attr('disabled', true);
        $('#import_form').submit();
    });
</script>
<?php $this->endBlock(); ?>
