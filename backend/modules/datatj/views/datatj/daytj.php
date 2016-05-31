<?php
$this->title = '日历史数据';
use yii\grid\GridView;
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                日历史数据
            </h3>
        </div>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items} ',
            'columns' => [
                [
                    'attribute' => 'bizDate',
                    'label' => '日期',
                ],
                [
                    'attribute' => 'totalInvestment',
                    'label' => '交易额',
                    'value' => function ($data) {
                        return number_format($data['totalInvestment'], 2);
                    }
                ],
                [
                    'attribute' => 'rechargeMoney',
                    'label' => '充值金额',
                    'value' => function ($data) {
                        return number_format($data['rechargeMoney'], 2);
                    }
                ],
                [
                    'attribute' => 'drawAmount',
                    'label' => '提现金额',
                    'value' => function ($data) {
                        return number_format($data['drawAmount'], 2);
                    }
                ],
                [
                    'attribute' => 'rechargeCost',
                    'label' => '充值手续费',
                    'value' => function ($data) {
                        return number_format($data['rechargeCost'], 2);
                    }
                ],
                [
                    'attribute' => 'investmentInWyb',
                    'label' => Yii::$app->params['pc_cat'][2] . '销售额',
                    'value' => function ($data) {
                        return number_format($data['investmentInWyb'], 2);
                    }
                ],
                [
                    'attribute' => 'investmentInWyj',
                    'label' => Yii::$app->params['pc_cat'][1] . '销售额',
                    'value' => function ($data) {
                        return number_format($data['investmentInWyj'], 2);
                    }
                ],
                [
                    'attribute' => 'reg',
                    'label' => '注册用户',
                    'value' => function ($data) {
                        return intval($data['reg']);
                    }
                ],
                [
                    'attribute' => 'idVerified',
                    'label' => '实名认证',
                    'value' => function ($data) {
                        return intval($data['idVerified']);
                    }
                ],
                [
                    'attribute' => 'qpayEnabled',
                    'label' => '绑卡用户数',
                    'value' => function ($data) {
                        return intval($data['qpayEnabled']);
                    }
                ],
                [
                    'attribute' => 'investor',
                    'label' => '投资人数',
                    'value' => function ($data) {
                        return intval($data['investor']);
                    }
                ],
                [
                    'attribute' => 'newRegisterAndInvestor',
                    'label' => '当日注册当日投资人数',
                    'value' => function ($data) {
                        return intval($data['newRegisterAndInvestor']);
                    }
                ],
                [
                    'attribute' => 'newInvestor',
                    'label' => '新增投资人数',
                    'value' => function ($data) {
                        return intval($data['newInvestor']);
                    }
                ],
                [
                    'attribute' => 'investAndLogin',
                    'label' => '已投用户登录数',
                    'value' => function ($data) {
                        return intval($data['investAndLogin']);
                    }
                ],
                [
                    'attribute' => 'notInvestAndLogin',
                    'label' => '未投用户登录数',
                    'value' => function ($data) {
                        return intval($data['notInvestAndLogin']);
                    }
                ],
                [
                    'attribute' => 'successFound',
                    'label' => '融资项目',
                    'value' => function ($data) {
                        return intval($data['successFound']);
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

