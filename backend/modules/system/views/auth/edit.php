<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = '添加/编辑权限';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        系统管理 <small>系统管理模块【主要包含管理员、权限、角色】</small>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/system/auth/list">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/system/auth/list">权限管理</a>
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="javascript:void(0);"><?php if($model->id){echo "编辑";}else{echo "添加";} ?>权限</a>
                        </li>  
                </ul>
        </div>        
    </div>
    
    <div class="portlet-body form">

            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['id' => 'auth_form', 'action' => "/system/auth/edit?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
                    <div class="control-group">

                            <label class="control-label">编号</label>

                            <div class="controls">
                                    <?= $form->field($model, 'sn', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'编号']])->textInput() ?>
                                    <?= $form->field($model, 'sn', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">权限名称</label>
                            <div class="controls">
                                    <?= $form->field($model, 'auth_name', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'权限名称']])->textInput() ?>
                                    <?= $form->field($model, 'auth_name', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">权限说明</label>
                            <div class="controls">
                                    <?= $form->field($model, 'auth_description', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'权限说明']])->textInput() ?>
                                    <?= $form->field($model, 'auth_description', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">权限地址</label>
                            <div class="controls">
                                <?= $form->field($model, 'path', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'权限地址']])->textInput() ?>
                                <?= $form->field($model, 'path', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">状态</label>
                            <div class="controls">
                                <?= $form->field($model, 'status', ['template' => '{input}'])->radioList(['0' => '冻结', '1' => '正常']); ?>
                                <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                            </div>
                    </div>
                  
                    
                    <div class="form-actions">

                            <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>

                            <a href="/system/auth/list" class="btn">取消</a> 

                    </div>

             <?php $form->end(); ?>

            <!-- END FORM-->  

    </div>
</div>
<?php $this->endBlock(); ?>