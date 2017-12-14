<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-12
 * Time: 上午11:47
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
                    <a href="javascript:void(0);"><?php if($model->id){echo "编辑";}else{echo "添加";} ?>全体消息</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="portlet-body form">

        <!-- BEGIN FORM-->
        <?php $form = ActiveForm::begin(['id' => '', 'action' => "/wechat/reply/edit-whole-message?id=".$model->id, 'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
        <div class="control-group">
            <label class="control-label">回复内容</label>
            <div class="controls">
                <?= $form->field($model, 'content', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span4','placeholder'=>'回复内容']])->textarea() ?>
                <?= $form->field($model, 'content', ['template' => '{error}']); ?>
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
