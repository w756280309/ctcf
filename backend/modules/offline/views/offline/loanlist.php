<?php

$this->title = '标的列表';

use common\utils\StringUtils;
use yii\widgets\LinkPager;
use yii\helpers\Html;

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div>
            <h3 class="page-title">
                标的列表
                <a href="addloan" class="btn green float-right">
                    <i class="icon-plus"></i> 新增产品
                </a>
            </h3>

            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">标的列表</a>
                </li>
            </ul>
        </div>
    </div>
    <!--search start-->
    <div class="portlet-body">
        <form action="/offline/offline/loanlist" method="get" target="_self" id="loanFilter">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="title">产品名称</span></td>
                    <td><input id="title" type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='title' value="<?= Yii::$app->request->get('title') ?>"  placeholder="请输入产品名称"/></td>
                    <td><span class="title">序号</span></td>
                    <td><input id="sn" type="text" class="m-wrap span6" style="margin-bottom: 0px;width:200px" name='sn' value="<?= Yii::$app->request->get('sn') ?>"  placeholder="请输入序号"/></td>
                    <td>
                        <div align="right" style="margin-right: 20px">
                            <input type="button"  class="btn" value="重置" style="width: 60px;" onclick="formReset()"/>
                            <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <!--search end -->

    <div class="portlet-body">
        <div class="portlet-body">
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                'columns' => [
                    [
                        'attribute' => 'sn',
                        'value' => $model->sn,
                    ],
                    [
                        'attribute' => 'title',
                        'value' => function ($model) {
                            return $model->title ? $model->title : "---";
                        }
                    ],
                    [
                        'attribute' => 'yield_rate',
                        'value' => function ($model) {
                            return $model->yield_rate ? $model->yield_rate : "---";
                        }
                    ],
                    [
                        'attribute' => 'expires',
                        'value' => function ($model) {
                            return $model->expires ? $model->expires.$model->unit : "---";
                        }
                    ],
                    [
                        'attribute' => 'jixi_time',
                        'value' => function ($model) {
                            return $model->jixi_time ? $model->jixi_time : "---";
                        }
                    ],
                    [
                        'attribute' => 'finish_date',
                        'value' => function ($model) {
                            return $model->finish_date ? $model->finish_date : "---";
                        }
                    ],
                    [
                        'attribute' => 'repaymentMethod',
                        'value' => function ($model) {
                            return ($model->repaymentMethod && isset(Yii::$app->params['refund_method'][$model->repaymentMethod])) ? Yii::$app->params['refund_method'][$model->repaymentMethod] : "---";
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{repayment} {list} {edit} {jixi} {confirm_jixi} {huankuan} {letter}',//只需要展示删除和更新
                        'headerOptions' => ['width' => '140'],
                        'buttons' => [
                            'repayment' => function($url, $model, $key){
//                                return Html::a('分期列表',
//                                    ['repayment', 'id' => $key],
//                                    [
//                                        'class' => 'btn mini green',
//                                    ]
//                                );
                            },
                            'list' => function($url, $model, $key){
                                return Html::a('投资记录',
                                    ['list', 'loan_id' => $key],
                                    ['class' => 'btn mini green']
                                );
                            },
                            'edit' => function($url, $model, $key){
                                return Html::a('编辑',
                                    ['editloan', 'id' => $key],
                                    [
                                        'class' => 'btn mini green',
                                    ]
                                );
                            },
                            'jixi' => function($url, $model, $key){
                                if (0 == $model->is_jixi) {
                                    return "<a href=\"javascript:void(0)\" onclick=\"openwin('/offline/offline/jixi?id=$model->id&type=loan', 500, 300)\" class=\"btn mini green\">
                                    设置起息日</a>";
                                }

                            },
                            'confirm_jixi' => function($url, $model, $key){
                                if (null !== $model->jixi_time && $model->is_jixi == 0) {
                                    return '<a href="/offline/offline/loan-confirm?id='.$model->id.'" onclick="return confirm(\'确认要计息吗？\')" class="btn mini green confirm_jixi"><i
                                                class="icon-edit"></i>确认计息</a>';
                                }

                            },
                            'huankuan' => function($url, $model, $key){
                                if (null !== $model->jixi_time && $model->is_jixi == 1) {
                                    return '<a href="/offline/offline/repayment-plan?id='.$model->id.'" class="btn mini green confirm_jixi"><i
                                                class="icon-edit"></i>还款计划</a>';
                                }

                            },
                            'letter' => function ($irl, $model, $key) {
                                return "<a href='/product/growth/letter?loanId=".$model->id."&isOnline=0' class='btn mini green' target='_blank'>打印确认函</a>";
                            }
                        ],
                    ],
                ]
            ]) ?>
        </div>
    </div>

</div>
    <script type="text/javascript">

        function formReset()
        {
            $.removeCookie('loanListFilterIsTest', { path: '/' });
            window.location.href = '/offline/offline/loanlist';
        }
        //确认计息
        $('confirm_jixi').on('click', function(){
            alert('11111111');
        })
    </script>
<?php $this->endBlock(); ?>