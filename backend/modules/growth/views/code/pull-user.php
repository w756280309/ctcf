<?php

use yii\widgets\ActiveForm;
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$this->title = '补充领取人';
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
                    <a href="/growth/code/list?sn=<?= $model->goodsType_sn ?>" class="duihuan-list">兑换码列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">补充领取人</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'id' => 'pull-form',
            'action' => '/growth/code/draw?cid=' . $model->id,
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">兑换码</label>
                <div class="controls">
                    <?= $form->field($model, 'code', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off']])->textInput(['readonly' => 'readonly']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">手机号</label>
                <div class="controls">
                    <input type="text" name="mobile" maxlength="11" class="mobile" placeholder="请输入联系人手机号">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">领取时间</label>
                <div class="controls">
                    <input type="text" name="usedAt" class="usedAt" placeholder="请选择领取时间" readonly="readonly" onclick = "WdatePicker({dateFmt:'yyyy-MM-dd  HH:mm:ss', maxDate:'<?= date('Y-m-d') ?>'})">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn blue pull-btn"><i class="icon-ok"></i> 确定 </button>&nbsp;&nbsp;&nbsp;
                <a href="/growth/code/list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $(function() {
        $('.pull-btn').bind('click', function () {
            var _this = $(this);
            if ('' === $('.mobile').val()) {
                layer.msg('请填写手机号', {icon: 2});
                return false;
            }
            if ('' === $('.usedAt').val()) {
                layer.msg('请选择领取时间', {icon: 2});
                return false;
            }
            var form = $('#pull-form');
            vals = form.serialize();
            _this.attr('disabled', 'disabled');
            $.post(form.attr("action"), vals, function (data) {
                _this.removeAttr('disabled');
                if (0 === data.code) {
                    location.href = $('.duihuan-list').attr('href');
                } else {
                    layer.msg(data.message, {icon: 2});
                    return false;
                }
            });
        });
    });
</script>
<?php $this->endBlock(); ?>

