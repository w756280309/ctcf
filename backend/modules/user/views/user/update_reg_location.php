<?php

use yii\widgets\ActiveForm;

$this->title = '修改注册IP位置信息';

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER-->
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    会员管理
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/listt">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/listt">投资会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $user->id ?>">投资会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">修改注册IP位置信息</a>
                    </li>
                </ul>
            </div>

            <div class="portlet-body form">
                <?php
                    $form = ActiveForm::begin([
                        'action' => '',
                        'options' => [
                            'class' => 'form-horizontal form-bordered form-label-stripped',
                        ],
                    ]);
                ?>

                <div class="control-group">
                    <label class="control-label">手机号</label>
                    <div class="controls"><?= $user->getMobile() ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">真实姓名</label>
                    <div class="controls"><?= empty($user->real_name) ? '---' : $user->real_name ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">IP地址</label>
                    <div class="controls"><?= empty($user->registerIp) ? '---' : $user->registerIp ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">注册IP位置信息</label>
                    <div class="controls">
                        <?= $form->field($user, 'regLocation', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '注册IP位置信息']])->textInput() ?>
                        <?= $form->field($user, 'regLocation', ['template' => '{error}']) ?>
                    </div>
                </div>

                <!--普通提交-->
                <div class="form-actions">
                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                    <a href="/user/user/detail?id=<?= $user->id ?>" class="btn">取消</a>
                </div>
                <?php $form->end(); ?>
            </div>
        </div>
    </div>
<?php $this->endBlock(); ?>