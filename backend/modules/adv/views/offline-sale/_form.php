<?php
if ($model->isNewRecord) {
    $this->title = '新建活动';
} else {
    $this->title = '更新活动';
}
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="span12">
        <h3 class="page-title">
            运营管理
            <small>活动管理</small>
        </h3>
        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/adv/adv/index">运营管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="/adv/ranking/index">活动管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="javascript:void(0);"><?= $this->title ?></a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <?php $form = \yii\widgets\ActiveForm::begin() ?>
        <div class="control-group">
            <label class="control-label">活动</label>
            <div class="controls">
                <?= $form->field($model, 'rankingPromoOfflineSale_id', ['template' => '{input}', 'inputOptions' => [ 'class' => 'm-wrap span6']])->dropDownList(\yii\helpers\ArrayHelper::map($ranking, 'id', 'title')) ?>
                <?= $form->field($model, 'rankingPromoOfflineSale_id', ['template' => '{error}']) ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">手机号</label>
            <div class="controls">
                <?= $form->field($model, 'mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '手机号', 'maxlength' => true]])->textInput() ?>
                <?= $form->field($model, 'mobile', ['template' => '{error}']) ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">总投资额（元）</label>
            <div class="controls">
                <?= $form->field($model, 'totalInvest', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '总投资额（元）']])->textInput() ?>
                <?= $form->field($model, 'totalInvest', ['template' => '{error}']) ?>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
            <a href="/adv/ranking/index" class="btn">取消</a>
        </div>
        <?php $form->end() ?>
    </div>
</div>

<?php $this->endBlock(); ?>
