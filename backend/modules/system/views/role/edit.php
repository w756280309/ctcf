<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = '添加/编辑角色';
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
                                <a href="/system/role/list">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/system/role/list">角色管理</a>
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="javascript:void(0);"><?php if($model->id){echo "编辑";}else{echo "添加";} ?>角色</a>
                        </li>  
                </ul>
        </div>        
    </div>
    
    <div class="portlet-body form">

            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['id' => 'auth_form', 'action' => "/system/role/edit?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
                    <div class="control-group">

                            <label class="control-label">编号</label>

                            <div class="controls">
                                    <?= $form->field($model, 'sn', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'编号']])->textInput() ?>
                                    <?= $form->field($model, 'sn', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">角色名称</label>
                            <div class="controls">
                                    <?= $form->field($model, 'role_name', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'角色名称']])->textInput() ?>
                                    <?= $form->field($model, 'role_name', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">角色说明</label>
                            <div class="controls">
                                    <?= $form->field($model, 'role_description', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'角色说明']])->textInput() ?>
                                    <?= $form->field($model, 'role_description', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">分配权限</label>
                            <div class="controls">
                                <a class="btn" onclick="showlayer();">选择</a> 
				<?= $form->field($model, 'auths', ['inputOptions' => ['value' => $raval], 'template' => '{input}'])->hiddenInput() ?>
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

                            <a href="/system/role/list" class="btn">取消</a> 

                    </div>

             <?php $form->end(); ?>

            <!-- END FORM-->  

    </div>
</div>
<script type="text/javascript">
    var id=<?= $model->id?$model->id:0  ?>;
    function showlayer(){
        openwin('/system/role/authlist?id='+id,500,300)
    }
</script>
<?php $this->endBlock(); ?>