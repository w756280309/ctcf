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
            </h3>
            <a class="btn green btn-block" style="width: 140px;" href="/datatj/datatj/month-export">月统计数据导出</a>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/datatj/datatj/monthtj">月历史数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">月历史数据</a>
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
                    'attribute' => 'bizDate',
                    'label' => '日期',
                    'value' => function ($data) {
                        return date('Y-m', strtotime($data['bizDate']));
                    }
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
                    'label' => '温盈宝销售额',
                    'value' => function ($data) {
                        return number_format($data['investmentInWyb'], 2);
                    }
                ],
                [
                    'attribute' => 'investmentInWyj',
                    'label' => '温盈金销售额',
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
                    'format' => 'html',
                    'value' => function ($data) use ($monthInvestor) {
                        $investor = (key_exists(date('Y-m', strtotime($data['bizDate'])), $monthInvestor) ? $monthInvestor[date('Y-m', strtotime($data['bizDate']))] : 0);
                        return '<a href="/datatj/datatj/list?type=month&field=investor&date=' . date('Y-m', strtotime($data['bizDate'])) . '&result=' . intval($investor) . '">' . intval($investor) . '</a>';
                    }
                ],
                [
                    'attribute' => 'newRegisterAndInvestor',
                    'label' => '当日注册当日投资人数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=month&field=newRegisterAndInvestor&date=' . date('Y-m', strtotime($data['bizDate'])) . '&result=' . intval($data['newRegisterAndInvestor']) . '">' . intval($data['newRegisterAndInvestor']) . '</a>';
                    }
                ],
                [
                    'attribute' => 'newInvestor',
                    'label' => '非当月注册于当月投资新增人数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=month&field=newInvestor&date=' . date('Y-m', strtotime($data['bizDate'])) . '&result=' . intval($data['newInvestor']) . '">' . intval($data['newInvestor']) . '</a>';
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
    </div>

    <?php $this->endBlock(); ?>

