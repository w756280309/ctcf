<?php
$this->title = '月历史数据';
use yii\grid\GridView;
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                月历史数据
                <small style="color: red;">排列数据新数据在前，每月更新，即4月展示3月前数据（包含3月）</small>
            </h3>
        </div>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'=>'{items} ',
            'columns' => [
                [
                    'attribute' => 'bizDate',
                    'label' => '日期',
                    'value' =>function($model) {
                        return (new DateTime($model->bizDate))->format('Y-m');
                    }
                ],
                [
                    'attribute' => 'totalInvestment',
                    'label' => '交易额',
                    'value' =>function($model) {
                        return number_format($model->totalInvestment,3);
                    }
                ],
                [
                    'attribute' => 'rechargeMoney',
                    'label' => '充值金额',
                    'value' =>function($model) {
                        return number_format($model->rechargeMoney,3);
                    }
                ],
                [
                    'attribute' => 'drawAmount',
                    'label' => '提现金额',
                    'value' =>function($model) {
                        return number_format($model->drawAmount,3);
                    }
                ],
                [
                    'attribute' => 'rechargeCost',
                    'label' => '充值手续费',
                    'value' =>function($model) {
                        return number_format($model->rechargeCost,3);
                    }
                ],
                [
                    'attribute' => 'reg',
                    'label' => '注册用户',
                    'value' =>function($model) {
                        return intval($model->reg);
                    }
                ],
                [
                    'attribute' => 'idVerified',
                    'label' => '实名认证',
                    'value' =>function($model) {
                        return intval($model->idVerified);
                    }
                ],
                [
                    'attribute' => 'successFound',
                    'label' => '融资项目',
                    'value' =>function($model) {
                        return intval($model->successFound);
                    }
                ],
            ],
            'tableOptions'=>['class' => 'table table-striped table-bordered table-advance table-hover']
        ])?>
</div>

<?php $this->endBlock(); ?>

