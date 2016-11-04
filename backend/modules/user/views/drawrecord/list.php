<?php

use common\models\user\User;
use common\utils\StringUtils;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

$drawStatus = Yii::$app->params['draw_status'];
$time = Html::encode($time);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员提现管理模块【主要包含投资会员的提现明细管理】</small>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?= $type === User::USER_TYPE_ORG ? 'listr' : 'listt' ?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                     <?php if($type === User::USER_TYPE_PERSONAL) { ?>
                    <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }else{?>
                        <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }?>
                    <li>
                        <a href="/user/user/<?= $type === 2 ? 'listr' : 'listt' ?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $user->id ?>&type=<?= $type ?>">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">提现流水明细</a>
                    </li>
            </ul>
        </div>

         <div class="portlet-body">
            <table class="table">
                <tr>
                    <?php if($type === User::USER_TYPE_PERSONAL) { ?>
                        <td>
                            <span class="title">用户名：<?= $user->real_name ?></span>
                        </td>
                        <td>
                            <span class="title">成功提现金额总计（元）：<?= StringUtils::amountFormat3($moneyTotal) ?></span>
                        </td>
                        <td>
                            <span class="title">成功（次）：<?= $successNum ?></span></td>
                        <td>
                            <span class="title">失败（次）：<?= $failureNum ?></span></td>
                        </td>
                    <?php } else { ?>
                        <td>
                            <span class="title">企业名：<?= $user['org_name'] ?></span>
                        </td>
                        <td>
                            <span class="title">成功提现金额总计（元）：<?= StringUtils::amountFormat3($moneyTotal) ?></span>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/drawrecord/detail" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                        <input type="hidden" name="type" value="<?= $type ?>">
                        <?php if($type == User::USER_TYPE_PERSONAL){?>
                            <td>
                                <span class="title" >状态</span>
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <?php foreach ($drawStatus as $k => $v) : ?>
                                        <option value="<?= $k + 1 ?>" <?= $status === $k + 1 ? "selected='selected'" : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <?php }?>
                            <td><span class="title">提现时间</span></td>
                            <td>
                                <input type="text" value="<?= $time ?>" name="time" onclick='WdatePicker({dateFmt: "yyyy-MM-dd", maxDate: "<?=  date('Y-m-d') ?>"});'>
                            </td>
                            <td>
                                <div align="right" class="search-btn">
                                    <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
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
                        <th>流水号</th>
                        <th>提现金额（元）</th>
                        <th>银行</th>
                        <th>提现时间</th>
                        <th>状态</th>
                        <th>联动状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= StringUtils::amountFormat2($val['money']) ?></td>
                        <td><?= $val['bankName'] ?></td>
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?= $drawStatus[$val['status']] ?></td>
                        <td><button class="btn btn-primary get_order_status" drawid="<?= $val['id'] ?>">查询流水在联动状态</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        //点击获取流水在联动的状态
        $('.get_order_status').on('click', function () {
            var _this = $(this);
            var id = _this.attr('drawid');
            if (id) {
                var xhr = $.get('/user/drawrecord/ump-status?id='+id, function (data) {
                    _this.parent().html(data.message);
                });

                xhr.fail(function() {
                    newalert(0, '联动接口请求失败');
                });
            }
        });
    })
</script>
<?php $this->endBlock(); ?>

