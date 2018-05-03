<?php

if ($model->isNewRecord) {
    $this->title = '新建活动';
} else {
    $this->title = '更新活动';
}

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

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
            <label class="control-label">标题</label>
            <div class="controls">
                <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '标题']])->textInput() ?>
                <?= $form->field($model, 'title', ['template' => '{error}']) ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">开始时间</label>
            <div class="controls">
                <?= $form->field($model, 'startTime', [
                    'template' => '{input}',
                    'inputOptions' => [
                        'autocomplete' => 'off',
                        'class' => 'm-wrap span3 Wdate',
                        'placeholder' => '选择开始时间',
                        'onclick' => 'WdatePicker({dateFmt: \'yyyy-MM-dd HH:mm:ss\'})',
                    ]])->textInput() ?>
                <?= $form->field($model, 'startTime', ['template' => '{error}']); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">结束时间</label>
            <div class="controls">
                <?= $form->field($model, 'endTime', [
                    'template' => '{input}',
                    'inputOptions' => [
                        'autocomplete' => 'off',
                        'class' => 'm-wrap span3 Wdate',
                        'placeholder' => '选择结束时间',
                        'onclick' => 'WdatePicker({dateFmt: \'yyyy-MM-dd HH:mm:ss\'})',
                    ]])->textInput() ?>
                <?= $form->field($model, 'endTime', ['template' => '{error}']); ?>
            </div>
        </div>
        <div  class="control-group">
            <label class="control-label">活动排序</label>
            <div class="controls">
                <?= $form->field($model, 'sortValue', [
                    'template' => '{input}',
                    'inputOptions' => [
                        'autocomplete' => 'off',
                        'class' => 'm-wrap span3',
                        'placeholder' => '请填写排序值',
                    ]])->textInput() ?>
                <?= $form->field($model, 'sortValue', ['template' => '{error}']); ?>
            </div>
        </div>
        <div  class="control-group">
            <label class="control-label">首页轮播ID</label>
            <div class="controls">
                <?= $form->field($model, 'advSn', [
                    'template' => '{input}',
                    'inputOptions' => [
                        'autocomplete' => 'off',
                        'class' => 'm-wrap span3',
                        'placeholder' => '请填写轮播图ID',
                    ]])->textInput() ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">是否隐藏</label>
            <div class="controls">
                <?= $form->field($model, 'isHidden', ['template' => '{input}'])
                    ->radioList(['0' => '否', '1' => '是'])
                ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">活动白名单</label>
            <div class="controls">
                <?= $form->field($model, 'whiteList', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '活动白名单必须是以英文逗号分隔的手机号，首尾不得加逗号']])->textarea() ?>
                <?= $form->field($model, 'whiteList', ['template' => '{error}']); ?>
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