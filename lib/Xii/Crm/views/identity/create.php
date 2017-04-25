<?php

use Xii\Crm\Model\Contact;

$this->title = '潜客登记';

$this->params['breadcrumbs'][] = ['label' => '潜客列表', 'url' => '/crm/identity'];
$this->params['breadcrumbs'][] = ['label' => '潜客登记', 'url' => '/crm/identity/create'];

?>

<div class="row">
    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'identity_form']]) ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'numberType')->radioList(Contact::getTypeLabels()) ?>
    <?= $form->field($model, 'number')->textInput()->hint('手机号(188******12)或带区号的固话(0577-12****12)') ?>
    <?= \yii\helpers\Html::submitButton('添加', ['class' => 'btn btn-primary', 'id' => 'identity_submit']) ?>

    <?php $form->end() ?>
</div>

<script>
    $('#identity_form').on('beforeSubmit', function(e){
        $('#identity_submit').attr('disabled', true);
    });
</script>