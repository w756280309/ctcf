<?php
use yii\widgets\LinkPager;
use common\models\user\User;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
$type = (int) $type;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员充值管理模块【主要包含投资会员的充值明细管理】</small>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?= $type === 2 ? 'listr' : 'listt' ?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if ($type === User::USER_TYPE_PERSONAL) {?>
                    <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php } else {?>
                        <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="/user/user/<?= $type === 2 ? 'listr' : 'listt' ?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $uid ?>&type=<?= $type ?>">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">充值流水明细</a>
                    </li>
            </ul>
        </div>

         <div class="portlet-body">
            <table class="table">
                    <tr>
                   <?php if ($type === User::USER_TYPE_PERSONAL) {?>
                        <td>
                            <span class="title">用户名：<?= $user['real_name'] ?></span>
                        </td>
                        <td>
                            <span class="title">充值金额总计（元）：<?=  number_format($moneyTotal, 2)?></span>
                        </td>
                        <td>
                            <span class="title">成功（次）：<?= $successNum ?></span></td>
                        <td>
                            <span class="title">失败（次）：<?= $failureNum ?></span></td>
                    <?php } else { ?>
                      <td colspan="2">
                            <span class="title">企业名：<?= $user['org_name'] ?></span>
                        </td>
                        <td colspan="2">
                            <span class="title">充值金额总计（元）：<?=  number_format($moneyTotal, 2)?></span>
                        </td>
                    <?php }?>
                    </tr>
                <tr>
                    <td colspan="2">（本平台账户）当前可用余额（元）：<?= $available_balance ?></td>
                    <td colspan="2">（第三方托管平台账户）联动账户余额（元）：<?= $user_account ?></td>
                </tr>
            </table>
        </div>


        <!--search start-->
        <div class="portlet-body">
            <form action="/user/rechargerecord/detail" method="get" target="_self">
                <table class="table">

                    <tbody>
                        <tr>
                        <input type="hidden" name="id" value="<?= $uid ?>">
                        <input type="hidden" name ='type' value = '<?= $type ?>'>
                        <?php if ($type === User::USER_TYPE_PERSONAL) {?>
                            <td>
                                <span class="title">状态</span>
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <?php foreach(Yii::$app->params['rechargeMingxi'] as $key => $val): ?>
                                    <option value="<?= $key ?>"
                                        <?php if($key === (int) $status){
                                            echo "selected='selected'";
                                        }?>
                                            ><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <?php }?>
                            <td><span class="title">充值时间</span></td>
                            <td>
                                <input type="text" value="<?= $time ?>" name = 'time' onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>
                            </td>
                            <td><div align="right" style="margin-right: 20px">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                                </div></td>
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
                        <th>充值金额（元）</th>
                        <?php
                            if (2 === $type) {
                        ?>
                                <th>充值前可用余额（元）</th>
                        <?php
                            }
                        ?>
                        <th>银行</th>
                        <th>充值时间</th>
                        <th>状态</th>
                        <th>联动状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= number_format($val['fund'], 2) ?></td>
                        <?php
                            if (2 === $type) {
                        ?>
                                <td>
                                    <?php
                                        if (!empty($val['balance'])) {
                                            echo number_format($val['balance'], 2);
                                        }
                                    ?>
                                </td>
                        <?php
                            }
                        ?>
                        <td><?= $val['bank_name'] ?></td>
                        <td><?= date('Y-m-d H:i',$val['created_at'])?></td>
                        <td>
                            <?php
                                    if (0 === (int) $val['status']) {
                                        $desc = "充值未处理";
                                    } elseif (1 === (int) $val['status']) {
                                        $desc = "充值成功";
                                    } else {
                                        $desc = "充值失败";
                                    }

                                    if (3 === (int) $val['pay_type']) {
                                        echo $desc."-线下pos";
                                    } else {
                                        echo $desc."-线上充值";
                                    }
                             ?>
                        <td>
                            <button class="btn btn-primary get_order_status" sn="<?= $val['sn'] ?>">查询流水在联动状态</button>
                        </td>
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
    $(function () {
        //点击获取流水状态
        $('.get_order_status').bind('click', function () {
            var _this = $(this);
            var sn = _this.attr('sn');
            if (sn) {
                $.get('/user/rechargerecord/get-order-status?sn=' + sn, function (data) {
                    if (data.code) {
                        _this.parent().html(data.message);
                    } else {
                        newalert(0, data.message);
                    }
                });
            }
        });
    })
</script>
<?php $this->endBlock(); ?>

