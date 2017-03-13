<?php
$this->title = '月度代金券';

use common\utils\SecurityUtils;
use common\utils\StringUtils;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>月度代金券</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/coupon/coupon/month-list">月度代金券</a>
                    <i class="icon-angle-right"></i>
                </li>

            </ul>
        </div>

        <!--根据时间查询数据 start-->
        <form action="?" method="get">
            <table class="table search_form">
                <tr>
                    <td>
                        <span class="title">代金券使用时间</span>
                        <input type="text" class="m-wrap span4"  name='listTimeStr' value="<?= $listTimeStr ?>"  onclick="WdatePicker()"/> -
                        <input type="text" class="m-wrap span4"  name='listTimeEnd' value="<?= $listTimeEnd ?>"  onclick="WdatePicker()"/>
                    </td>

                    <td>
                        <button class="btn btn-summary blue">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                    </td>
                    <td>
                        <a class="btn btn-summary green" href="/coupon/coupon/export?<?= http_build_query(Yii::$app->request->get())?>">Excel导出</a>
                    </td>

                </tr>
            </table>
        </form>

        <!--根据时间查询数据 end-->

        <!--数据项包含 用户名称、用户手机号、单张代金券使用金额、单张代金券使用时间-->

        <div class="portlet-body">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                    'columns' => [
                        [
                            'header' => '用户名称',
                            'format' => 'html',
                            'value' => function ($model) {
                                if ($model->user->real_name) {
                                    return '<a href="/user/user/detail?id='.($model->user->id).'">'.$model->user->real_name.'</a>';
                                } else {
                                    return '---';
                                }
                            }
                        ],
                        [
                            'header' => '用户手机号',
                            'value' => function ($model) {
                                return isset($model->user->safeMobile) ? SecurityUtils::decrypt($model->user->safeMobile) : '---';
                            }
                        ],
                        [

                            'contentOptions' => [
                                'class' => 'text-center',
                            ],
                            'header' => '单张代金券使用金额',
                            'value' => function ($model) {
                                return isset($model->couponType->amount) ? $model->couponType->amount : '---';
                            },
                            'options' => ['float' => 'right']

                        ],
                        [
                            'header' => '单张代金券使用时间',
                            'value' => function ($model) {
                                return date("Y-m-d H:i:s",$model->order->created_at);
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>

    </div>
</div>
<?php $this->endBlock(); ?>