<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = '添加会员类型';
$this->params['breadcrumbs'][] = $this->title;
//ActiveForm.enableClientValidation=

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
    <div class="info">
        <h3>添加会员类型</h3>
    </div>
    <div class="exercise">
        <a href="/user/usertype/index">类型列表</a>
    </div>
</div>
<div class="page_main">
    <?php $form = ActiveForm::begin(['id' => 'adv_form', 'action' => "" ]); ?>
    <div class="page_table table_list" style="height:auto; max-width:280px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="right" colspan="2">添加会员类型</td>
            </tr>
            <tr>
            <td style="text-align: center;">
                   <?= $form->field($model, 'name', ['template' => '{label}']) ?>
                </td>
                <td style="text-align: center;">
                    <?= $form->field($model, 'name',['inputOptions'=>['class'=>'text_value','style'=>'width: 200px'] ,'template' => '{input}'])->textInput() ?><?= $form->field($model, 'name', ['template' => '{error}']) ?>
                </td>
            </tr>
            <tr>
                <td align="left">显示</td>
                <td width="60" class="text_left">
                    <?= $form->field($model, 'status',['inputOptions'=>['class'=>'text_value','style'=>'width: 200px'] ,'template' => '{input}'])->radioList([1=>"正常",0=>"隐藏"]) ?>
                </td>
            </tr>
            <tr>
                <td width="200" colspan="2">
                    <input type="submit" class="button_small" onclick="" value="确定" />
                </td>
            </tr>
        </table>
    </div>
    <?php $form->end(); ?>
</div>
<?php $this->endBlock(); ?>