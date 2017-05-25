<?php

use common\utils\StringUtils;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = '复投新增数据统计';

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    复投新增数据统计
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/datatj/datatj/huizongtj">数据统计</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">复投新增数据统计</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>PS：年化金额 = sum（投资金额 * 产品期限 / 365天或12个月）</li>
                </ul>
            </div>
        </div>

        <div class="portlet-body">
            <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination" style="text-align:center; clear: both;">{pager}</div>',
                    'columns' => [
                        [
                            'label' => '网点',
                            'value' => function ($data) use ($affiliators) {
                                $affiliator = isset($affiliators[$data->id]) ? $affiliators[$data->id]->affiliator->name : '官方';

                                return Html::encode($affiliator);
                            }
                        ],
                        [
                            'label' => '客户类型',
                            'value' => function ($data) use ($month) {
                                return substr($data->info->firstInvestDate, 0, 7) === $month ? '新客' : '老客';
                            }
                        ],
                        [
                            'label' => '客户姓名',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<a href="/user/user/detail?id='.$data->id.'">'.$data->real_name.'</a>';
                            }
                        ],
                        [
                            'label' => '客户手机号',
                            'value' => function ($data) {
                                return Stringutils::obfsMobileNumber($data->getMobile());
                            },
                        ],
                        [
                            'label' => '老客复投金额',
                            'value' => function ($data) use ($orderAnnual, $repaymentAnnual, $month) {
                                $msg = '---';

                                if (substr($data->info->firstInvestDate, 0, 7) !== $month) {
                                    $oa = isset($orderAnnual[$data->id]) ? $orderAnnual[$data->id]['annual'] : 0;
                                    $ra = isset($repaymentAnnual[$data->id]) ? $repaymentAnnual[$data->id]['annual'] : 0;
                                    $amount = min($oa, $ra);
                                    $msg = Stringutils::amountFormat2($amount);
                                }

                                return $msg;
                            },
                            'contentOptions' => ['class' => 'money'],
                            'headerOptions' => ['class' => 'money'],
                        ],
                        [
                            'label' => '老客新增金额',
                            'value' => function ($data) use ($orderAnnual, $repaymentAnnual, $month) {
                                $msg = '---';

                                if (substr($data->info->firstInvestDate, 0, 7) !== $month) {
                                    $oa = isset($orderAnnual[$data->id]) ? $orderAnnual[$data->id]['annual'] : 0;
                                    $ra = isset($repaymentAnnual[$data->id]) ? $repaymentAnnual[$data->id]['annual'] : 0;
                                    $amount = bcsub($oa, $ra, 2);
                                    $amount = $amount > 0 ? $amount : 0;
                                    $msg = Stringutils::amountFormat2($amount);
                                }

                                return $msg;
                            },
                            'contentOptions' => ['class' => 'money'],
                            'headerOptions' => ['class' => 'money'],
                        ],
                        [
                            'label' => '新客新增金额',
                            'value' => function ($data) use ($orderAnnual, $repaymentAnnual, $month) {
                                $msg = '---';

                                if (substr($data->info->firstInvestDate, 0, 7) === $month) {
                                    $oa = isset($orderAnnual[$data->id]) ? $orderAnnual[$data->id]['annual'] : 0;
                                    $ra = isset($repaymentAnnual[$data->id]) ? $repaymentAnnual[$data->id]['annual'] : 0;
                                    $amount = bcsub($oa, $ra, 2);
                                    $amount = $amount > 0 ? $amount : 0;
                                    $msg = Stringutils::amountFormat2($amount);
                                }

                                return $msg;
                            },
                            'contentOptions' => ['class' => 'money'],
                            'headerOptions' => ['class' => 'money'],
                        ],
                    ],
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
                ])
            ?>
        </div>
    </div>
<?php $this->endBlock(); ?>