<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = '添加/编辑测试用户';
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
                                <a href="/system/">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/system/auth/list">测试用户管理</a>
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="javascript:void(0);"><?php if($model->id){echo "编辑";}else{echo "添加";} ?>测试用户</a>
                        </li>  
                </ul>
        </div>        
    </div>
    
    <div class="portlet-body form">

            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['id' => 'edit_form', 'action' => "/adminuser/test/edit?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
                   
                    <div class="control-group">
                            <label class="control-label">测试用户名名称</label>
                            <div class="controls">
                                    <?= $form->field($model, 'user', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'测试用户名名称']])->textInput() ?>
                                    <?= $form->field($model, 'user', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">手机号码</label>
                            <div class="controls">
                                    <?= $form->field($model, 'tel', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'手机号码']])->textInput() ?>
                                    <?= $form->field($model, 'tel', ['template' => '{error}']); ?>
                            </div>
                    </div>
                     <div class="control-group">
                            <label class="control-label">登陆时间</label>
                            <div class="controls">
                                    <?= $form->field($model, 'login_time', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'登陆时间']])->textInput() ?>
                                    <?= $form->field($model, 'login_time', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">状态</label>
                            <div class="controls">
                                <?= $form->field($model, 'status', ['template' => '{input}'])->radioList(['0' => '冻结', '1' => '正常']); ?>
                                <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                            </div>
                    </div>
            
                      <div class="control-group">
                            <label class="control-label">创建时间</label>
                            <div class="controls">
                                    <?= $form->field($model, 'created_at', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'创建时间']])->textInput() ?>
                                    <?= $form->field($model, 'created_at', ['template' => '{error}']); ?>
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