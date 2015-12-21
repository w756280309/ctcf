<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = '添加/编辑管理员';
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
                                <a href="/adminuser/admin/list">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/adminuser/admin/list">管理员管理</a>
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
            <?php $form = ActiveForm::begin(['id' => 'admin_form', 'action' => "/adminuser/admin/edit?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
                    <div class="control-group">

                            <label class="control-label">管理员用户名</label>

                            <div class="controls">
                                    <?= $form->field($model, 'username', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'管理员用户名']])->textInput() ?>
                                    <?= $form->field($model, 'username', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">角色</label>
                            <div class="controls">
                                    <?= $form->field($model, 'role_sn', ['inputOptions' => ['class' => 'm-wrap span4'], 'template' => '{input}'])->dropDownList(['0' => '请选择'] + $roles); ?>
                                    <?= $form->field($model, 'role_sn', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    
                    <div class="control-group">
                            <label class="control-label">分配权限</label>
                            <div class="controls">
                                <a class="btn" onclick="showlayer();">选择</a> 
				<?= $form->field($model, 'auths', ['inputOptions' => ['value' => $authsval], 'template' => '{input}'])->hiddenInput() ?>
                                <?= $form->field($model, 'auths', ['template' => '{error}']); ?>
                            </div>
                    </div>
            
                    <div class="control-group">
                            <label class="control-label">邮箱</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    
                                    <?= $form->field($model, 'email', ['template' => '<span class="add-on">@</span>{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span16','placeholder'=>'邮箱','style'=>"width:225px"]])->textInput() ?>
                                    
                                </div>
                                    
                                    <?= $form->field($model, 'email', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">

                            <label class="control-label">管理员姓名</label>

                            <div class="controls">
                                    <?= $form->field($model, 'real_name', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'管理员姓名']])->textInput() ?>
                                    <?= $form->field($model, 'real_name', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">

                            <label class="control-label">管理员密码</label>

                            <div class="controls">
                                    <?= $form->field($model, 'user_pass', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'管理员密码']])->passwordInput() ?>
                                    <?= $form->field($model, 'user_pass', ['template' => '{error}']); ?>
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

                            <a href="/adminuser/admin/list" class="btn">取消</a> 

                    </div>

             <?php $form->end(); ?>

            <!-- END FORM-->  

    </div>
</div>
<script type="text/javascript">
    var id=<?= $model->id?$model->id:0  ?>;
    var rsn='<?= $model->role_sn?$model->role_sn:'0'  ?>';
    function showlayer(){
        crsn = $('#admin-role_sn').val()
        if(crsn=='0'){
            alert('请选择角色');
        }else{
            openwin('/adminuser/admin/authlist?id='+id+"&rsn="+crsn,500,300)
        }
        
    }
</script>
<?php $this->endBlock(); ?>