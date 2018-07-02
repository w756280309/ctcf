<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-5-29
 * Time: 上午9:18
 */
use yii\widgets\LinkPager;
use common\models\product\Asset;

$this->title = '资产包管理';
?>
<?php $this->beginBlock('blockmain'); ?>
<style>
    table.table td{
        text-align: right;
    }
    table.table th{
        text-align: right;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                资产包管理 <small>资产包管理模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/product/productonline/list">贷款管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/asset/index">资产包管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">资产包列表</a>
                </li>
            </ul>
        </div>
    </div>
    <!--搜索部分-->
    <div class="portlet-body">
        <form action="/product/asset/index" method="get" target="_self" id="loanFilter">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="title">资产包编号</span></td>
                    <td><input  type="text" class="m-wrap span4 " style="margin-bottom: 0px;width:200px" name='sn' value="<?= Yii::$app->request->get('sn') ?>" /></td>
                    <td><span class="title">融资方</span></td>
                    <td><input  type="text" class="m-wrap span6" style="margin-bottom: 0px;width:200px" name='borrowerName' value="<?= Yii::$app->request->get('borrowerName') ?>" /></td>
                    <td>
                        <div align="right" style="margin-right: 20px">
                            <input type="button"  class="btn" value="重置" style="width: 60px;" onclick="location='/product/asset/index'"/>
                            <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <!--表格部分-->
    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
            <tr>
                <th>序号</th>
                <th>融资方</th>
                <th>资产包编号</th>
                <th>借款期限</th>
                <th style="text-align: right">资产包金额(元)</th>
                <th>还款方式</th>
                <th>资产打包利率(%)</th>
                <th>签约状态</th>
                <th style="text-align: center">操作</th>
                <th>标的名称</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $model) : ?>
                <tr>
                    <td><?= $model->id ?></td>
                    <td><?= $model->borrowerName ?></td>
                    <td><?= $model->sn ?></td>
                    <td><?= $model->expiresValue ?></td>
                    <td style="text-align: right"><?= number_format($model->amount, 2) ?></td>
                    <td><?= $model->repaymentMethod ?></td>
                    <td><?= round($model->rate, 2) ?></td>
                    <td><?= $model->signState ?></td>
                    <td style="text-align: center">
                        <?php if ($model->status == Asset::STATUS_INIT) : ?>
                            <a href="/product/asset/create-product?id=<?= $model->id ?>" >发布标的</a>
                        <?php endif; ?>
                    </td>
                    <td><?= $model->product ? $model->product->title : null ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?>
</div>
<?php $this->endBlock(); ?>
