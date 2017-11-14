<?php
$this->title = '日历史数据';
use yii\grid\GridView;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/datatj/datatj/daytj">日历史数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">日历史数据</a>
                </li>
            </ul>
            <p style="color: red;">每5分钟更新一次，上次更新时间：<?= $lastUpdateTime ?>
            <a class="btn green" href="/datatj/datatj/day-export">日统计数据导出</a>
</p>
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
                    'label' => '总交易额',
                    'value' => function ($data) {
                        return number_format($data['totalInvestment'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'onlineInvestment',
                    'label' => '线上交易额',
                    'value' => function ($data) {
                        return number_format($data['onlineInvestment'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'offlineInvestment',
                    'label' => '线下交易金额',
                    'value' => function ($data) {
                        return number_format($data['offlineInvestment'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'rechargeMoney',
                    'label' => '充值金额',
                    'value' => function ($data) {
                        return number_format($data['rechargeMoney'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'drawAmount',
                    'label' => '提现金额',
                    'value' => function ($data) {
                        return number_format($data['drawAmount'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'rechargeCost',
                    'label' => '充值手续费',
                    'value' => function ($data) {
                        return number_format($data['rechargeCost'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'investmentInWyb',
                    'label' => Yii::$app->params['pc_cat'][2] . '销售额',
                    'value' => function ($data) {
                        return number_format($data['investmentInWyb'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'attribute' => 'investmentInWyj',
                    'label' => Yii::$app->params['pc_cat'][1] . '销售额',
                    'value' => function ($data) {
                        return number_format($data['investmentInWyj'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
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
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=day&field=investor&date=' . $data['bizDate'] . '&result=' . intval($data['investor']) . '">' . intval($data['investor']) . '</a>';
                    }
                ],
                [
                    'attribute' => 'newRegisterAndInvestor',
                    'label' => '新客新投人数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a title="当日注册当日投资人数" href="/datatj/datatj/list?type=day&field=newRegisterAndInvestor&date=' . $data['bizDate'] . '&result=' . intval($data['newRegisterAndInvestor']) . '">' . intval($data['newRegisterAndInvestor']) . '</a>';
                    }
                ],
                [
                    'attribute' => 'newInvestor',
                    'label' => '老客新投人数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a title="非今日注册于今日投资新增人数" href="/datatj/datatj/list?type=day&field=newInvestor&date=' . $data['bizDate'] . '&result=' . intval($data['newInvestor']) . '">' . intval($data['newInvestor']) . '</a>';
                    }
                ],
                [
                    'attribute' => 'investAndLogin',
                    'label' => '已投用户登录数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=day&field=investAndLogin&date=' . $data['bizDate'] . '&result=' . intval($data['investAndLogin']) . '">' . intval($data['investAndLogin']) . '</a>';
                    }
                ],
                [
                    'attribute' => 'notInvestAndLogin',
                    'label' => '未投用户登录数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=day&field=notInvestAndLogin&date=' . $data['bizDate'] . '&result=' . intval($data['notInvestAndLogin']) . '">' . intval($data['notInvestAndLogin']) . '</a>';
                    }
                ],
                [
                    'label' => '签到用户数',
                    'value' => function ($data) {
                        return intval($data['checkIn']);
                    }
                ],
                [
                    'attribute' => 'successFound',
                    'label' => '融资项目',
                    'value' => function ($data) {
                        return intval($data['successFound']);
                    }
                ],
                [
                    'label' => '已回款金额',
                    'value' => function ($data) {
                        return number_format($data['repayMoney'], 2);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '已回款用户数',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/datatj/datatj/list?type=day&field=repayUser&date='.$data['bizDate'].'">'.intval($data['repayUserCount']).'</a>';
                    }
                ],
                [
                    'label' => '已回款项目数',
                    'value' => function ($data) {
                        return intval($data['repayLoanCount']);
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover', 'style' => 'background-color: #fff']
        ]) ?>
        <div class="pagination" style="text-align:center;clear: both">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
    <?php $this->endBlock(); ?>

