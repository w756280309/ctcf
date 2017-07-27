<?php
use common\utils\StringUtils;
use common\utils\SecurityUtils;
$this->title = '积分批量发放';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                积分批量发放预览
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/points/init">积分批量导入</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">批量发放</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <?php if($notSendCount > 0) {?>
            <table class="table">
                <tr>
                    <td>有<?= $notSendCount?>条记等待发放</td>
                    <td>
                        <?php $form = \yii\widgets\ActiveForm::begin(['action' => '/growth/points/confirm?batchSn='.Yii::$app->request->get('batchSn'), 'options' => ['class' => 'form-group', 'id' => 'confirm_form']]); ?>
                        <?=  \yii\helpers\Html::submitButton('批量发放', ['id' => 'send_points', 'class' => 'btn btn-default blue'])?>
                        <?php $form->end(); ?>
                    </td>
                </tr>
            </table>
        <?php }?>
        <div class="portlet-body">
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
                'columns' => [
                    [
                        'header' => '手机号',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->isOnline) {
                                return '<a href="/user/user/detail?id='.$model->user_id.'">'.StringUtils::obfsMobileNumber(SecurityUtils::decrypt($model->safeMobile)).'</a>';
                            } else {
                                return '<a href="/user/offline/detail?id='.$model->user_id.'">'.StringUtils::obfsMobileNumber(SecurityUtils::decrypt($model->safeMobile)).'</a>';
                            }
                        }
                    ],
                    [
                        'header' => '身份证号',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return StringUtils::obfsIdCardNo(SecurityUtils::decrypt($model->idCard));
                        }
                    ],
                    [
                        'header' => '用户',
                        'value' => function ($model) {
                            return $model->isOnline ? '线上用户' : '线下用户';
                        }
                    ],
                    [
                        'header' => '积分',
                        'value' => function ($model) {
                            return $model->points;
                        }
                    ],
                    [
                        'header' => '简介',
                        'value' => function ($model) {
                            return $model->desc ?: '---';
                        }
                    ],
                    [
                        'header' => '状态',
                        'value' => function ($model) {
                            if ($model->status === 2) {
                                return '发放失败';
                            } elseif ($model->status === 1) {
                                return '成功';
                            } else {
                                return '待发放';
                            }
                        }
                    ],
                ]
            ]) ?>
        </div>
    </div>
</div>
<script>
    $('#send_points').click(function(e){
        e.preventDefault();
        if (confirm('确认为所有待发放记录发放积分？')) {
            $(this).attr('disabled', true);
            $('#confirm_form').submit();
        }
    });
</script>
<?php $this->endBlock(); ?>
