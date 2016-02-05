<?php

use yii\bootstrap\ActiveForm;
use common\models\user\User;

$this->title = '添加/编辑会员';
$this->params['breadcrumbs'][] = $this->title;

$id = Yii::$app->request->get('id');

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="row-fluid">
    <div class="span12">
        <h3 class="page-title">
            会员管理 <small>会员管理模块【主要包括融资会员的、编辑，投资会员的编辑】</small>
        </h3>
        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/user/user/listr">会员管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <?php if ($category != User::USER_TYPE_PERSONAL) { ?>
                <li>
                    <a href="/user/user/listr">融资会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <?php if (empty($id)) { ?>
                        <a href="javascript:void(0);">添加新融资用户</a>
                    <?php } else { ?>
                        <a href="javascript:void(0);">编辑融资用户</a>
                    <?php } ?>
                </li>

            <?php } else { ?>
                <li>
                    <a href="/user/user/listr">投资会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">编辑投资用户</a>
                </li>
            <?php } ?>

    </div>

    <div class="portlet-body form">
        <?php
        $is_add = empty($id) && 2 === $category;
        $action = $is_add ? "/user/user/add" : "/user/user/edit?id=" . $id . "&type=$category";
        $form = ActiveForm::begin(['id' => 'admin_form',
                'action' => $action,
                'options' => ['class' => 'form-horizontal form-bordered form-label-stripped'],
        ]);
        ?>

        <?php if ($category == User::USER_TYPE_PERSONAL) { ?>
            <!--投资用户-->
            <div class="control-group">
                <div class="controls"><label >会员ID：<?= $create_usercode ?></label>
                </div>
                <div class="controls"><label >手机号：</label>
                    <?= $form->field($model, 'mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '手机号']])->textInput() ?>
                    <?= $form->field($model, 'mobile', ['template' => '{error}']); ?>
                </div>
                <div class="controls"><label >真实姓名：</label>
                    <?= $form->field($model, 'real_name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '真实姓名']])->textInput() ?>
                    <?= $form->field($model, 'real_name', ['template' => '{error}']); ?>
                </div>

            <?php } else { ?>
                <!--融资用户-->
                <div class="control-group">
                    <?php if ($id) { ?>
                        <div class="controls"><label >会员ID：</label>
                            <?= $model->usercode; ?>
                        </div>
                    <?php } ?>
                    <div class="controls"><label >企业名称：</label>
                        <?= $form->field($model, 'org_name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '企业名称']])->textInput() ?>
                        <?= $form->field($model, 'org_name', ['template' => '{error}']); ?>
                    </div>
                    <div class="controls"><label >企业账号：</label>
                        <?= $form->field($model, 'username', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '6-20位字母与数字组合']])->textInput($is_add ? [] : ['readonly' => true]) ?>
                        <?= $form->field($model, 'username', ['template' => '{error}']); ?>
                    </div>

                    <div class="controls"><label >企业初始密码：</label>
                        <?= $form->field($model, 'password_hash', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12']])->textInput($is_add ? ['readonly' => true] : []) ?>
                        <?= $form->field($model, 'password_hash', ['template' => '{error}']); ?>
                    </div>

                    <div class="controls"><label >联动用户ID号：</label>
                        <?= $form->field($epayuser, 'epayUserId', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '联动商户ID号']])->textInput($is_add ? [] : ['readonly' => true]) ?>
                        <?= $form->field($epayuser, 'epayUserId', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">企业法人</label>

                    <div class="controls"><label >姓名：</label>
                        <?= $form->field($model, 'law_master', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '姓名']])->textInput() ?>
                        <?= $form->field($model, 'law_master', ['template' => '{error}']); ?>
                    </div>
                    <div class="controls"><label >身份证号：</label>
                        <?= $form->field($model, 'law_master_idcard', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '身份证号']])->textInput() ?>
                        <?= $form->field($model, 'law_master_idcard', ['template' => '{error}']); ?>
                    </div>
                    <div class="controls"><label >联系电话：</label>
                        <?= $form->field($model, 'law_mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '联系电话']])->textInput() ?>
                        <?= $form->field($model, 'law_mobile', ['template' => '{error}']); ?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">企业联系人</label>

                    <div class="controls"><label >姓名：</label>
                        <?= $form->field($model, 'real_name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '姓名']])->textInput() ?>
                        <?= $form->field($model, 'real_name', ['template' => '{error}']); ?>
                    </div>
                    <div class="controls"><label >身份证号：</label>
                        <?= $form->field($model, 'idcard', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '身份证号']])->textInput() ?>
                        <?= $form->field($model, 'idcard', ['template' => '{error}']); ?>
                    </div>
                    <div class="controls"><label >联系电话：</label>
                        <?= $form->field($model, 'mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '联系电话']])->textInput() ?>
                        <?= $form->field($model, 'mobile', ['template' => '{error}']); ?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">营业执照号</label>
                    <div class="controls">
                        <?= $form->field($model, 'business_licence', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '营业执照号']])->textInput() ?>
                        <?= $form->field($model, 'business_licence', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">组织机构代码</label>
                    <div class="controls">
                        <?= $form->field($model, 'org_code', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '组织机构代码']])->textInput() ?>
                        <?= $form->field($model, 'org_code', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">税务登记号</label>
                    <div class="controls">
                        <?= $form->field($model, 'shui_code', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '税务登记号']])->textInput() ?>
                        <?= $form->field($model, 'shui_code', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">办公电话</label>
                    <div class="controls">
                        <?= $form->field($model, 'tel', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '办公电话']])->textInput() ?>
                        <?= $form->field($model, 'tel', ['template' => '{error}']); ?>
                    </div>
                </div>
            <?php } ?>



            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                <a href="/user/user/<?= $category == 1 ? "listt" : "listr" ?>" class="btn">取消</a>
            </div>

            <?php $form->end(); ?>
        </div>
    </div>

    <?php $this->endBlock(); ?>