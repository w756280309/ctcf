<?php

use yii\widgets\ActiveForm;

$this->title = '添加兑换码';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

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
                    <a href="#">添加兑换码</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'id' => 'add-code',
            'action' => '/growth/code/refer',
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">商品名称</label>
                <div class="controls">
                    <select class = "m-wrap goods-name" style = "width:200px" name = 'gid'>
                        <option value="">--请选择--</option>
                        <?php foreach ($goods as $good): ?>
                            <option value="<?= $good->id ?>">
                                <?= $good->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">兑换码数量</label>
                <div class="controls">
                    <input type="number" placeholder="请输入生成兑换码的数量" autocomplete="off" style = "width:185px" name="num" class="code-num">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">截止兑换时间</label>
                <div class="controls">
                    <input type="text" placeholder="请输入兑换码的截止时间" autocomplete="off" style = "width:185px" name="expiresAt" class="expiresAt" onclick = "WdatePicker({dateFmt:'yyyy-MM-dd  HH:mm:ss'})" readonly="readonly">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue sub-btn"><i class="icon-ok"></i> 提交 </button>&nbsp;&nbsp;&nbsp;
                <a href="/growth/code/goods-list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $(function() {
        $('#add-code').submit(function () {
            var num = parseInt($('.code-num').val());
            var goodsName = $('.goods-name').val();
            var expiresAt = $('.expiresAt').val();
            if ('' === goodsName) {
                layer.msg('请选择兑换码对应的商品名称', {icon: 2});
                return false;
            }

            if (isNaN(num) || num <= 0 || num > 10000) {
                layer.msg('兑换码的数量必须大于0且一次不能导出超过10000条', {icon: 2});
                return false;
            }
            if ('' === expiresAt) {
                layer.msg('请选择兑换码的截止时间', {icon: 2});
                return false;
            }
            $('.code-num').val(num);
        })
    })
</script>
<?php $this->endBlock(); ?>

