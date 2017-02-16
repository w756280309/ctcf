<?php
    use yii\helpers\Html;
    use common\models\product\OnlineProduct as Plan;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
$this->title = '回款查询';
?>
<?php $this->beginBlock('blockmain'); ?>
<style>
    .search_form td input {
        margin: 0px;
    }
    .search_form .title {
        height: 34px;
        line-height: 34px;
        vertical-align: middle;
        font-size: 14px;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                回款查询
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/repayment/search/index">回款查询</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">回款列表</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <form action="?" method="get">
                <table class="table search_form">
                    <tr>
                        <td>
                            <span class="title">项目名称</span>
                            <input type="text" name='loanTitle' value="<?= $searchModel->loanTitle ?>" placeholder="项目名称" class="m-wrap span6"/>
                        </td>
                        <td>
                            <span class="title">回款时间</span>
                            <input type="text" class="m-wrap span4"  name='refundTimeStart' value="<?= $searchModel->refundTimeStart ?>"  onclick="WdatePicker()"/> -
                            <input type="text" class="m-wrap span4"  name='refundTimeEnd' value="<?= $searchModel->refundTimeEnd ?>"  onclick="WdatePicker()"/>
                        </td>
                        <td>
                            <button class="btn btn-summary blue">查询</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="title">回款状态</span>
                            <select name="isRefunded" id=""  class="m-wrap span6">
                                <option value="-1">全部</option>
                                <option value="0" <?= (intval($searchModel->isRefunded ) === 0)? 'selected' : ''?> >待回款</option>
                                <option value="1" <?= (intval($searchModel->isRefunded) === 1)  ? 'selected' : ''?> >已回款</option>
                            </select>
                        </td>
                        <td>
                            <span class="title">回款金额</span>
                            <input type="text" name='refundMoneyStart' value="<?= $searchModel->refundMoneyStart ?>" class="m-wrap span4"/> -
                            <input type="text" name='refundMoneyEnd' value="<?= $searchModel->refundMoneyEnd ?>"  class="m-wrap span4"/>
                        </td>
                        <td>
                            <a class="btn btn-summary blue" href="/repayment/search/export?<?= http_build_query(Yii::$app->request->get())?>">导出回款信息</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="span12">
            <table>
                <tr><td>当前待回款项目数:<?= $noPaidLoanCount ?></td><td>待回款金额:<?= number_format($noPaidMoney, 2) ?></td></tr>
                <tr><td>当前已回款项目数:<?= $paidLoanCount ?></td><td>已回款金额:<?= number_format($paidMoney, 2) ?></td></tr>
            </table>
        </div>
        <div class="span12"></div>
        <div class="span12">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                    'columns' => [
                        [
                            'header' => '序号',
                            'value' => function ($model) {
                                return $model->loan->sn;
                            }
                        ],
                        [
                            'header' => '项目名称',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<a href="/product/productonline/list?sn='.urlencode($model->loan->sn).'">'.$model->loan->title.'</a>';
                            }
                        ],
                        [
                            'header' => '项目类型',
                            'value' => function ($model) {
                                $types = Yii::$app->params['pc_cat'];
                                return isset($types[$model->loan->cid]) ? $types[$model->loan->cid] : '';
                            }
                        ],
                        [
                            'header' => '期限',
                            'value' => function ($model) {
                                $type = intval($model->loan->refund_method);
                                return $model->loan->expires . ($type === Plan::REFUND_METHOD_DAOQIBENXI ? '天' : '月');
                            }
                        ],
                        [
                            'header' => '利率',
                            'value' => function ($model) {
                                return \common\view\LoanHelper::getDealRate($model->loan) . ($model->loan->jiaxi ? '+' . \common\utils\StringUtils::amountFormat2($model->loan->jiaxi) : '');
                            }
                        ],
                        [
                            'header' => '实际募集金额',
                            'value' => function ($model) {
                                return number_format($model->loan->funded_money, 2);
                            }
                        ],
                        [
                            'header' => '起息日',
                            'value' => function ($model) {
                                return date('Y-m-d', $model->loan->jixi_time);
                            }
                        ],
                        [
                            'header' => '到期日',
                            'value' => function ($model) {
                                return date('Y-m-d', $model->loan->finish_date);
                            }
                        ],
                        [
                            'header' => '宽限期',
                            'value' => function ($model) {
                                return $model->loan->kuanxianqi . '天';
                            }
                        ],
                        [
                            'header' => '当前期数',
                            'value' => function ($model) {
                                return $model->term;
                            }
                        ],
                        [
                            'header' => '回款时间',
                            'value' => function ($model) {
                                if ($model->isRefunded) {
                                    return date('Y-m-d', strtotime($model->refundedAt));
                                } else {
                                    return $model->dueDate;
                                }
                            }
                        ],
                        [
                            'header' => '回款金额',
                            'value' => function ($model) {
                                return number_format($model->amount, 2);
                            }
                        ],
                        [
                            'header' => '回款状态',
                            'value' => function ($model) {
                                if ($model->isRefunded) {
                                    return '已回款';
                                } else {
                                    return '待回款';
                                }
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
