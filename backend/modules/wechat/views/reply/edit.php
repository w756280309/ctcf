<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-6
 * Time: 下午6:20
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '添加/编辑角色';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $this->beginBlock('blockmain'); ?>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    运营管理 <small>公众号管理</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/wechat/reply/index">公众号管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><?php if($model->id){echo "编辑";}else{echo "添加";} ?>回复</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="portlet-body form">

            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['id' => '', 'action' => "/wechat/reply/edit?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
            <div class="control-group">
                <label class="control-label">类型</label>
                <div class="controls">
                    <?= $form->field($model, 'type', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'']])->dropDownList(['0' => '请选择'] + $types) ?>
                    <?= $form->field($model, 'type', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">关键字</label>
                <div class="controls">
                    <?= $form->field($model, 'keyword', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'关键字']])->textInput()?>
                    <?= $form->field($model, 'keyword', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">回复内容</label>
                <div class="controls">
                    <?= $form->field($model, 'content', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'回复内容']])->textInput() ?>
                    <?= $form->field($model, 'content', ['template' => '{error}']); ?>
                    <?= $form->field($model, 'media', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'回复内容']])->fileInput() ?>
                    <?= $form->field($model, 'media', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">状态</label>
                <div class="controls">
                    <?= $form->field($model, 'isDel', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'']])->radioList(['1' => '禁用', '0' => '启用']); ?>
                    <?= $form->field($model, 'isDel', ['template' => '{error}']); ?>
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