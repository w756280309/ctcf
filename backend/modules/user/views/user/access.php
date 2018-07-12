<?php
use yii\widgets\ActiveForm;

$this->title = '用户访问控制';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>用户访问</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/detail?id=<?php echo $user->id;?>">会员详情</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:;">用户访问控制</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/user/user/user-access?id='.$user->id,
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">账户状态</label>
                <div class="controls">
                    <?=
                    $form->field($user, 'status', [
                        'template' => '{input}',
                        'inputOptions' => [
                            'class' => 'm-wrap span12',
                        ]])->radioList(['0' => '禁用', '1' => '正常'])
                    ?>
                    <?= $form->field($user, 'status', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i>确认</button>&nbsp;&nbsp;&nbsp;
                <a href="/user/user/listt" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>

    <?php $this->endBlock(); ?>
