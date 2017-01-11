<?php

$this->title = '编辑线下数据统计项';

use yii\widgets\ActiveForm;

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                线下数据
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">编辑线下数据统计项</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="portlet-body">
        <?php
            $form = ActiveForm::begin([
                'action' => '/offline/offline/edit-stats',
                'options' => [
                    'class' => 'form-horizontal form-bordered form-label-stripped',
                    'enctype' => 'multipart/form-data'
                ]
            ]);
        ?>
        <div class="control-group">
            <label class="control-label">募集规模(元)</label>
            <div class="controls">
                <?=
                    $form->field($stats, 'tradedAmount', [
                        'template' => '{input}',
                        'inputOptions' => [
                            'autocomplete' => 'off',
                            'class' => 'm-wrap span12',
                            'placeholder' => '募集规模',
                        ]
                    ])->textInput()
                ?>
                <?= $form->field($stats, 'tradedAmount', ['template' => '{error}']) ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">兑付本金(元)</label>
            <div class="controls">
                <?=
                    $form->field($stats, 'refundedPrincipal', [
                        'template' => '{input}',
                        'inputOptions' => [
                            'autocomplete' => 'off',
                            'class' => 'm-wrap span12',
                            'placeholder' => '兑付本金',
                        ]
                    ])->textInput()
                ?>
                <?= $form->field($stats, 'refundedPrincipal', ['template' => '{error}']) ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">兑付利息(元)</label>
            <div class="controls">
                <?=
                    $form->field($stats, 'refundedInterest', [
                        'template' => '{input}',
                        'inputOptions' => [
                            'autocomplete' => 'off',
                            'class' => 'm-wrap span12',
                            'placeholder' => '兑付利息',
                        ]
                    ])->textInput()
                ?>
                <?= $form->field($stats, 'refundedInterest', ['template' => '{error}']) ?>
            </div>
        </div>

        <!--普通提交-->
        <div class="form-actions">
            <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
            <a href="list" class="btn">取消</a>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<?php $this->endBlock(); ?>