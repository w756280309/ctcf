<?php
use yii\widgets\LinkPager;
use common\models\user\User;
use yii\grid\GridView;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                <?php
                if ('day' === $type) {
                    echo '日历史数据';
                } else {
                    echo '月历史数据';
                }
                ?>
                <small>
                    <?php
                    echo $date;
                    switch ($field) {
                        case "investor" :
                            echo '投资人数';
                            break;
                        case "newRegisterAndInvestor" :
                            echo '当日注册当日投资人数';
                            break;
                        case "newInvestor" :
                            echo '新增投资人数';
                            break;
                        case "investAndLogin" :
                            echo '已投用户登录数';
                            break;
                        case "notInvestAndLogin" :
                            echo '未投用户登录数';
                            break;
                        default :
                            echo '';
                    }
                    ?>
                    用户列表
                </small>
            </h3>
            <a class="btn green btn-block" style="width: 140px;"
               href="/datatj/datatj/list-export?type=<?= $type ?>&field=<?= $field ?>&date=<?= $date ?>">统计详情列表导出</a>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/datatj/datatj/<?= $type ?>tj">
                        <?php
                        if ('day' === $type) {
                            echo '日历史数据';
                        } else {
                            echo '月历史数据';
                        }
                        ?></a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">用户列表</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items} ',
            'columns' => [
                [
                    'attribute' => 'usercode',
                    'label' => '会员ID',
                    'value' => function ($model) {
                        return $model['usercode'];
                    }
                ],
                [
                    'attribute' => 'mobile',
                    'label' => '手机号',
                    'value' => function ($model) {
                        return $model['mobile'];
                    }
                ],
                [
                    'attribute' => 'real_name',
                    'label' => '真实姓名',
                    'value' => function ($model) {
                        return $model['real_name'];
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => '注册时间',
                    'value' => function ($model) {
                        return date('Y-m-d H:i:s', $model['created_at']);
                    }
                ],
                [
                    'label' => '可用余额',
                    'value' => function ($model) {
                        return $model->lendAccount ? number_format($model->lendAccount->available_balance, 2) : 0;
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
        <div class="pagination" style="text-align:center;clear: both">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
    <?php $this->endBlock(); ?>
