<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id'=>'admin_form', 'action' =>"$formaction"]); ?>
    <div class="page_table form_table">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <?= $form->field($model, 'username',
                [
                    'labelOptions'=>['class'=>''],
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '<tr><th width="100">{label}</th><td width="300">{input}</td><td>{error}</td></tr>'
                ]
            )->textInput(); ?>
            <?= $form->field($model, 'mobile',[
                'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>
            <?= $form->field($model, 'type',
                [
                    'labelOptions'=>['class'=>''],
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '<tr><th width="100">{label}</th><td width="300">{input}</td><td>{error}</td></tr>'
                ]
            )->textInput(); ?>

            <?= $form->field($model, 'email',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>

            <?= $form->field($model, 'real_name',[
                'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>
            <?= $form->field($model, 'user_pass',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->passwordInput(); ?>
            <?= $form->field($model, 'idcard',[
                'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>
            <?= $form->field($model, 'org_name',[
                'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>

            <?= $form->field($model, 'org_code',[
                'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->textInput(); ?>

            <?= $form->field($model, 'type',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['1' => '普通用户', '2' => '机构用户']); ?>
            <?= $form->field($model, 'bank_card_status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '未验证', '1' => '正常']); ?>
            <?= $form->field($model, 'email_status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '未验证', '1' => '验证通过']); ?>

            <?= $form->field($model, 'mobile_status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '未验证', '1' => '验证通过']); ?>
            <?= $form->field($model, 'mobile_status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '未验证', '1' => '验证通过']); ?>

            <?= $form->field($model, 'idcard_status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '未验证', '1' => '验证通过']); ?>

            <?= $form->field($model, 'status',
                [
                    'inputOptions'=>['class'=>'text_value','style'=>'width: 200px'],
                    'template' => '
                        <tr>
                        <th width="100">{label}</th>
                        <td width="300">
                            {input}
                        </td>
                        <td>{error}</td>
                        </tr>
                        '])->radioList(['0' => '冻结', '1' => '正常']); ?>
            <tr>
                <th width="100px"></th>
                <td><button type="submit" class="button"> 确 定 </button></td>
            </tr>
        </table>
    </div>
    <?php ActiveForm::end(); ?>

</div>
