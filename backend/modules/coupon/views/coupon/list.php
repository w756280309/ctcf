<?php
$this->title = '代金券列表';

use common\models\coupon\UserCoupon;
use common\utils\StringUtils;
use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                优惠券管理 <small>运营模块</small>
                <a href="/coupon/coupon/add" class="btn green float-right">
                    <i class="icon-plus"></i> 添加优惠券
                </a>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/coupon/coupon/list">优惠券管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">优惠券列表</a>
                    </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/coupon/coupon/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">优惠券名称</span></td>
                            <td>
                                <input type="text" class="m-wrap span8" name='name' value="<?= Html::encode($name) ?>"/>
                            </td>
                            <td><span class="title">券id</span></td>
                            <td>
                                <input type="text" class="m-wrap span8" name='id' value="<?= Html::encode($id) ?>"/>
                            </td>
                            <td><span class="title">优惠券类型</span></td>
                            <td>
                                <select name="type" class="w-wap">
                                    <option value="">---全部---</option>
                                    <option value="0" <?= $type == '0' ? 'selected' : '' ?> >代金券</option>
                                    <option value="1" <?= $type == '1' ? 'selected' : '' ?> >加息券</option>
                                </select>
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
                        <th>券id</th>
                        <th>名称</th>
                        <?php if($type == '0'){ ?>
                            <th class="money">面值</th>
                        <?php } else if ($type == '1') { ?>
                            <th>利率</th>
                            <th>加息天数</th>
                        <?php } else { ?>
                            <th class="money">面值</th>
                            <th>加息利率</th>
                            <th>加息天数</th>
                        <?php } ?>
                        <th class="money">起投金额</th>
                        <th>有效期</th>
                        <th>发放时间</th>
                        <th>发放数量</th>
                        <th>已使用数</th>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val->id ?></td>
                        <td><?= $val->name ?></td>
                        <?php if($type == '0'){ ?>
                            <td class="money"><?= StringUtils::amountFormat2($val->amount) ?>元</td>
                        <?php } else if ($type  == '1') { ?>
                            <td><?= StringUtils::amountFormat2($val->bonusRate) ?>%</td>
                            <td><?= $val->bonusDays ?>天</td>
                        <?php } else { ?>
                            <td class="money"><?= StringUtils::amountFormat2($val->amount) ?>元</td>
                            <td><?= StringUtils::amountFormat2($val->bonusRate) ?>%</td>
                            <td><?= $val->bonusDays ?>天</td>
                        <?php } ?>

                        <td class="money"><?= StringUtils::amountFormat2($val->minInvest) ?>元</td>
                        <td><?= empty($val->expiresInDays) ? $val->useEndDate : $val->expiresInDays.'天' ?></td>
                        <td><?= empty($val->issueStartDate) ? '---' : $val->issueStartDate.' - '.$val->issueEndDate ?></td>
                        <td>
                            <?=
                                UserCoupon::findCouponByConfig($val->id)->count();
                            ?>
                        </td>
                        <td>
                            <?=
                                UserCoupon::findCouponByConfig($val->id, UserCoupon::STATUS_USED)->count();
                            ?>
                        </td>
                        <td>
                            <center>
                                <a href="/coupon/coupon/edit?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                <?php if (!$val->isAudited): ?>
                                <a href="javascript:void(0)" data-id="<?= $val->id ?>" class="btn mini green examine"><i class="icon-edit"></i> 审核</a>
                                <?php else: ?>
                                <a href="/coupon/coupon/owner-list?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i> 领取记录</a>
                                <?php endif; ?>
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