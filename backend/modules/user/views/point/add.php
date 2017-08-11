<?php
use yii\widgets\ActiveForm;

$this->title = '发放积分';
?>
<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>积分明细</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="<?= $isOffline === 0 ? '/user/user/detail?id='.$userId.'&type=1' : '/user/offline/detail?id='.$userId ?>">积分明细</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="">发放积分</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/user/point/add?userId='.$userId.'&isOffline='.$isOffline.'&backUrl='.$backUrl,
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">发放积分描述</label>
                <div class="controls">
                    <?= $form->field($PointRecord, 'remark', ['template' => '{input}<span class="notice">*最多填写20个汉字</span>', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发放积分描述']])->textInput() ?>
                    <?= $form->field($PointRecord, 'remark', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">发放积分数额</label>
                <div class="controls">
                    <?= $form->field($PointRecord, 'incr_points', ['template' => '{input}<span class="notice">*正/负数对应加/减积分</span>', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发放积分数额']])->textInput() ?>
                    <?= $form->field($PointRecord, 'incr_points', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i>发放</button>&nbsp;&nbsp;&nbsp;
                <a href="<?php
                    if($backUrl) {
                        echo $backUrl;
                    } else {
                        echo $isOffline === 0 ? '/user/user/detail?id='.$userId.'&type=1' : '/user/offline/detail?id='.$userId;
                    }
                ?>" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
<?php $this->endBlock(); ?>