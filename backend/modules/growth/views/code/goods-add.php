<?php
use yii\widgets\ActiveForm;

$type = array(
    1 => '代金券',
    2 => '实体商品',
);
$this->title = '添加商品';
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
                    <a href="#">添加商品</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'id' => 'add-goods',
            'action' => '/growth/code/goods-add',
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">商品名称</label>
                <div class="controls">
                    <?= $form->field($goodsType, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入商品名称']])->textInput() ?>
                    <?= $form->field($goodsType, 'name', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">商品类型</label>
                <div class="controls">
                    <?= $form->field($goodsType, 'type', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'onChange'=>"list(this.value)", 'class' => 'm-wrap']])->dropDownList($type) ?>
                    <?= $form->field($goodsType, 'type', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group" id = "daijinquan" style = "display: block;">
                <label class="control-label">代金券</label>
                <div class="controls">
                    <a href="/coupon/coupon/add" id="sample_editable_1_new" class="btn blue" style="float: right;">
                        添加代金券<i class="icon-plus"></i></a>
                    <?= $form->field($goodsType, 'sn', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span5']])->dropDownList($model) ?>
                    <?= $form->field($goodsType, 'sn', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue sub-btn"><i class="icon-ok"></i>提交</button>
                <a href="/growth/code/goods-list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    function list(data){
        var  obj = document.getElementById("daijinquan");
        if(data == 1){
            obj.style.display='block';
        } else {
            obj.style.display='none';
        }
    }
</script>
<?php $this->endBlock(); ?>

