<?php

use common\utils\StringUtils;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\LinkPager;

$this->title = '分销商统计';

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                分销商统计
            </h3>
            <a class="btn green btn-block" style="width: 140px;" href="/datatj/datatj/affiliation-export?start=<?= $start ?>&end=<?= $end ?>">分销商统计导出</a>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/datatj/datatj/affiliation">分销商统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">分销商统计</a>
                </li>
            </ul>
        </div>
    </div>

    <!--search start-->
    <div class="portlet-body">
        <form action="" method="get" target="_self">
            <table class="table">
                <tr>
                    <td>
                        <span class="title">统计日期</span>
                    </td>
                    <td>
                        <input type="text" placeholder="开始日期" value="<?= $start ? date('Y-m-d', $start) : '' ?>" autocomplete="off"
                               name="start" id="start" class="m-wrap span4"
                               onclick="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'end\')}'})">
                         ---
                        <input type="text" placeholder="结束日期" value="<?= $end ? date('Y-m-d', $end) : '' ?>" autocomplete="off"
                               name="end" id="end" class="m-wrap span4"
                               onclick="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'start\')}', maxDate: '%y-%M-%d'})">
                    </td>
                    <td align="right" class="span2">
                        <button type='submit' class="btn blue btn-block">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!--search end -->

    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items} ',
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                ],
                [
                    'attribute' => 'name',
                    'label' => '名称',
                    'value' => function ($data) {
                        return Html::encode($data['name']);
                    }
                ],
                [
                    'attribute' => 'uc',
                    'label' => '注册用户数（人）',
                    'value' => function ($data) {
                        return intval($data['uc']);
                    }
                ],
                [
                    'attribute' => 'oc',
                    'label' => '投资用户数（人）',
                    'value' => function ($data) {
                        return intval($data['oc']);
                    }
                ],
                [
                    'attribute' => 'm',
                    'label' => '投资金额（元）',
                    'value' => function ($data) {
                        return Stringutils::amountFormat2($data['m']);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
        <div class="pagination" style="text-align:center;clear: both">
            <?= LinkPager::widget(['pagination' => $pages]) ?>
        </div>
    </div>
<?php $this->endBlock(); ?>

