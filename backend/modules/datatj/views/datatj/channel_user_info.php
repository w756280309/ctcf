<?php
$this->title = '渠道用户注册及投资转化率';
use yii\grid\GridView;
use yii\web\YiiAsset;
use common\utils\StringUtils;
use yii\widgets\LinkPager;

$this->title = '渠道用户注册及投资转化率';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

?>
<?php $this->beginBlock('blockmain'); ?>
<style>
    #platform_rate tr td.td_content{
        width: 200px;
        text-align: left;
        color: red;
        height: 30px;
        line-height: 30px;
    }
    #platform_rate tr td.td_title{
        width: 60px;
    }
    #platform_rate td.td_title span{
        white-space: nowrap;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                渠道用户注册及投资转化率
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">渠道用户信息</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <form action="" method="get" target="_self">
            <table class="table">
                <tr>
                    <td>
                        <span class="title">渠道商</span>
                    </td>
                    <td>
                        <input type="text" placeholder="多个渠道商用逗号隔开" value="<?= $label ?>"
                        autocomplete="off"
                        name="label" class="m-wrap">
                    </td>
                    <td>
                        <span class="title">时间范围</span>
                    </td>
                    <td>
                        <input type="text" placeholder="开始时间" value="<?= $startDate ? $startDate : '' ?>"
                               autocomplete="off"
                               name="startDate" class="m-wrap"
                               onclick="WdatePicker()">-
                        <input type="text" placeholder="结束时间" value="<?= $endDate ? $endDate : '' ?>"
                               autocomplete="off"
                               name="endDate" class="m-wrap"
                               onclick="WdatePicker()">
                    </td>
                    <td align="right" class="span2">
                        <button type='submit' class="btn blue btn-block">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <hr>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'label' => '渠道',
                    'value' => function ($data) {
                        return $data['label'];
                    },
                ],
                [
                    'label' => '页面访问总数',
                    'value' => function ($data) {
                        return $data['nb_visits'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '注册人数',
                    'value' => function ($data) {
                        return $data['registerUserCount'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '订单数量',
                    'value' => function ($data) {
                        return $data['registerOrderCount'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '注册转化率(%)',
                    'value' => function ($data) {
                        return StringUtils::amountFormat2($data['registerConversionRate']);
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '订单转化率(%)',
                    'value' => function ($data) {
                        return StringUtils::amountFormat2($data['orderConversionRate']);
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '订单金额(元)',
                    'value' => function ($data) {
                        return StringUtils::amountFormat2($data['registerOrderMoneySum']);
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '新注册购买总额(元)',
                    'value' => function ($data) {
                        return StringUtils::amountFormat2($data['firstInvestAmount']);
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '渠道订单数量',
                    'value' => function ($data) {
                        return $data['orderCount'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '渠道订单金额(元)',
                    'value' => function ($data) {
                        return $data['orderMoneySum'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ]
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
</div>
<?php $this->endBlock(); ?>
