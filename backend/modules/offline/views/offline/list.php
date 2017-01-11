<?php

use common\utils\StringUtils;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = '线下数据';
$bid = (int)Yii::$app->request->get('bid');

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                线下数据
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/list">线下数据</a>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="/offline/offline/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr style="text-align: right">
                        <td>募集规模</td>
                        <td><?= StringUtils::amountFormat2($stats->tradedAmount) ?>元</td>
                        <td>兑付本金</td>
                        <td><?= StringUtils::amountFormat2($stats->refundedPrincipal) ?>元</td>
                        <td>兑付利息</td>
                        <td><?= StringUtils::amountFormat2($stats->refundedInterest) ?>元</td>
                        <td class="span2"><a href="/offline/offline/edit-stats" class="btn green btn-block">编辑统计项</a></td>
                        <td class="span1"><a href="/offline/offline/add" class="btn green btn-block">导入新数据</a></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="title" style="display: inline-block;line-height:27px;">分销商</span>
                            <select name="bid" style="margin-left:5px;">
                                <option value="">全部</option>
                                <?php foreach($branches as $branch) : ?>
                                    <option value="<?= $branch->id ?>" <?php if($bid === $branch->id){ ?> selected="selected" <?php } ?>><?= $branch->name ?></option>
                                <?php endforeach;?>
                            </select>
                            <br>
                            <span class="title" style="display: inline-block;line-height:27px;">姓&nbsp;名</span>
                            <input style="margin-left:5px;" type="text" name="realName" value="<?= Html::encode(Yii::$app->request->get('realName')) ?>" placeholder="请输入姓名">
                        </td>
                        <td colspan="4">
                            <span class="title" style="display: inline-block;line-height:27px;">产品名称</span>
                            <input style="margin-left:5px;" type="text" name="title" value="<?= Html::encode(Yii::$app->request->get('title')) ?>" placeholder="请输入产品名称">
                            <br>
                            <span class="title" style="display: inline-block;line-height:27px;">联系方式</span>
                            <input style="margin-left:5px;" type="text" name="mobile" value="<?= Html::encode(Yii::$app->request->get('mobile')) ?>" placeholder="请输入联系方式">
                        </td>
                        <td>
                            <button class="btn blue btn-block" style="width: 100px;float: right;">
                                查询 <i class="m-icon-swapright m-icon-white"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div>
                                总交易量：<span><?= number_format($totalmoney, 2) ?></span>万元
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover table-center">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>网点</th>
                    <th>产品名称</th>
                    <th>产品期限</th>
                    <th>客户姓名</th>
                    <th>证件号</th>
                    <th>联系电话</th>
                    <th>开户行名称</th>
                    <th>银行卡账号</th>
                    <th class="money">认购金额（万）</th>
                    <th>认购日期</th>
                    <th>起息日期</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $key => $order) : ?>
                    <tr>
                        <td>
                            <?= $order->id ?>
                        </td>
                        <td>
                            <?= $order->affliator->name ?>
                        </td>
                        <td>
                            <?= $order->loan->title ?>
                        </td>
                        <td>
                            <?= $order->loan->expires . $order->loan->unit ?>
                        </td>
                        <td>
                            <?= $order->realName ?>
                        </td>
                        <td>
                            <?= $order->idCard ?>
                        </td>
                        <td>
                            <?= $order->mobile ?>
                        </td>
                        <td>
                            <?= $order->accBankName ?>
                        </td>
                        <td>
                            <?= $order->bankCardNo ?>
                        </td>
                        <td class="money">
                            <?= number_format($order->money, 2) ?>
                        </td>
                        <td>
                            <?= $order->orderDate ?>
                        </td>
                        <td>
                            <?= $order->valueDate ?>
                        </td>
                        <td width="7%">
                            <a href="javascript:del('/offline/offline/delete','<?= $order->id ?>')" class="btn mini red ajax_op">
                                <i class="icon-minus-sign"></i>删除
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination" style="text-align:center;"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<script>
    function del(url, id)
    {
        if (confirm('确认删除？')) {
            var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
            var xhr = $.post(url, {id: id, _csrf: csrftoken}, function(data) {
                newalert(data.code, data.message, 1);
            });
        }
    }
</script>
<?php $this->endBlock(); ?>