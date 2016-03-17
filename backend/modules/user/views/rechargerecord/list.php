<?php
use yii\widgets\LinkPager;
use common\models\user\User;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
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
                        <a href="/user/user/<?= $type==2?'listr':'listt'?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if($type==User::USER_TYPE_PERSONAL){?>
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
                        <a href="/user/user/<?=$type==2?'listr':'listt'?>">会员列表</a>
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
                   <?php if($type==User::USER_TYPE_PERSONAL){?>
                        <td>
                            <span class="title">用户名：<?= $user['real_name'] ?></span>
                        </td>
                        <td>
                            <span class="title">充值金额总计（元）：<?=  number_format($moneyTotal,2)?></span>
                        </td>
                        <td>
                            <span class="title">成功（次）：<?=$successNum?></span></td>
                        <td>
                            <span class="title">失败（次）：<?=$failureNum?></span></td>
                        </td>
                    <?php }else{?>
                      <td>
                            <span class="title">企业名：<?= $user['org_name'] ?></span>
                        </td>
                        <td>
                            <span class="title">充值金额总计（元）：<?=  number_format($moneyTotal,2)?></span>
                        </td>
                    <?php }?>
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
                        <?php if ($type==User::USER_TYPE_PERSONAL) {?>
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
                        <th>银行</th>
                        <th>充值时间</th>
                        <th>状态</th>
                        <?php if($type==User::USER_TYPE_ORG) { ?>
                        <th>操作</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= number_format($val['fund'],2) ?></td>
                        <td><?= $val['bankName'] ?></td>
                        <td><?= date('Y-m-d H:i',$val['created_at'])?></td>
                        <td>
                            <?php if($type==User::USER_TYPE_PERSONAL) { ?>
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
                             <?php } else { ?>
                             <?php
                                    if($val['status']==0){
                                        echo "待审核";
                                    }elseif($val['status']==1){
                                        echo "审核成功";
                                    }else {
                                        echo "审核失败";
                                    }
                             ?>
                             <?php } ?>
                        </td>
                        <?php if($type==User::USER_TYPE_ORG) { ?>
                        <td>
                            <?php if($val['status'] == 0) { ?>
                            <a class="btn mini green ajax_op" op="status" index="<?= $val['id'] ?>"><i class="icon-edit"></i>审核</a>
                            <?php }else if($val['status'] == 2) { ?>
                            <a class="btn mini green ajax_op" op="status" index="<?= $val['id'] ?>"><i class="icon-edit"></i>重新审核</a>
                            <?php }else{ ?>
                            已审核
                            <?php } ?>
                        </td>
                        <?php } ?>
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
    var uid = '<?= $uid ?>';
    $(function () {

        $('.ajax_op').bind('click', function () {
            op = $(this).attr('op');
            index = $(this).attr('index');
            csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
            layer.confirm('确定审核通过吗？',{title:'充值审核',btn:['通过','不通过','关闭'],closeBtn:false},function(){
                openLoading();//打开loading
                $.post("/user/rechargerecord/recharge-sh", {op: op, id: index, type:1, uid:uid, _csrf:csrf}, function (result) {
                    cloaseLoading();//关闭loading
                    newalert(result['res'],'');
                    location.reload();
                });
            },function(){
                openLoading();//打开loading
                $.post("/user/rechargerecord/recharge-sh", {op: op, id: index,type:0, uid:uid, _csrf:csrf}, function (result) {
                    cloaseLoading();//关闭loading
                    newalert(result['res'],'');
                    location.reload();
                });
            })

        });


    })
</script>
<?php $this->endBlock(); ?>