<?php
$this->title = '代金券列表';

use common\utils\StringUtils;
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                代金券管理 <small>运营模块</small>
                <a href="/coupon/coupon/add" class="btn green float-right">
                    <i class="icon-plus"></i> 添加代金券
                </a>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/coupon/coupon/list">代金券管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">代金券列表</a>
                    </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/coupon/coupon/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">代金券名称</span></td>
                            <td>
                                <input type="text" class="m-wrap span8" name='name' value="<?= Html::encode($name) ?>"/>
                            </td>
                            <td>
                                <div class="search-btn" align="right">
                                    <button type='submit' class="btn blue btn-block button-search">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!--search end -->

        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>名称</th>
                        <th class="money">面值</th>
                        <th class="money">起投金额</th>
                        <th>有效期</th>
                        <th>发放时间</th>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= ++$key ?></td>
                        <td><?= $val->name ?></td>
                        <td class="money"><?= StringUtils::amountFormat2($val->amount) ?>元</td>
                        <td class="money"><?= StringUtils::amountFormat2($val->minInvest) ?>元</td>
                        <td><?= empty($val->expiresInDays) ? $val->useEndDate : $val->expiresInDays.'天' ?></td>
                        <td><?= empty($val->issueStartDate) ? '---' : $val->issueStartDate.' - '.$val->issueEndDate ?></td>
                        <td>
                            <center>
                                <?php if (!$val->isAudited) { ?>
                                <a href="/coupon/coupon/edit?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                <a href="javascript:void(0)" data-id="<?= $val->id ?>" class="btn mini green examine"><i class="icon-edit"></i> 审核</a>
                                <?php } else { ?>
                                <a href="/coupon/coupon/edit?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i> 查看</a>
                                <a href="/coupon/coupon/owner-list?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i> 领取记录</a>
                                <?php } ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination text-align-ct"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".examine").bind('click', function() {
            var id = $(this).attr("data-id");
            $.get('/coupon/coupon/audit', {id: id}, function(data) {
                if (0 === data.code) {
                    alert("操作成功");
                    location.reload();
                } else {
                    alert("操作失败");
                }
            });
        });
    })
</script>
<?php $this->endBlock(); ?>