<?php

use yii\widgets\LinkPager;
use common\models\user\User;
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$status = Yii::$app->request->get('status');
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
                        <a href="/user/user/<?= $type==2?'listr':'listt'?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $id ?>&type=<?= $type ?>">会员详情</a>
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
		<?php if($type==User::USER_TYPE_PERSONAL){?>
                        <td>
                            <span class="title">用户名：<?=$user['username']?></span>
                        </td>
                        <td>
                            <span class="title">成功提现金额总计（元）：<?= number_format($moneyTotal, 2) ?></span>
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
                            <span class="title">成功提现金额总计（元）：<?= number_format($moneyTotal, 2) ?></span>
                        </td>
		<?php }?>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">
            
                
            <form action="/user/drawrecord/detail" method="get" target="_self">
                <table class="table">
                    
                    <tbody>
                        <tr>
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="type" value="<?= $type ?>">
                        <?php if($type == User::USER_TYPE_PERSONAL){?>
                            <td>
                                <span class="title" >状态</span>
                            </td>
                            <td>
                                <select name="status" >
                                    <option value=""
                                        >---未选择---</option>
                                    <option value="-1" 
                                        <?php if ($status === '-1') {
                                            echo "selected='selected'";
                                        }?>
                                            >未处理</option>
                                    <option value="1" 
                                        <?php if($status==1){
                                            echo "selected='selected'";
                                        }?>
                                            >已审核</option>
                                    <option value="21" 
                                        <?php if($status==21){
                                            echo "selected='selected'";
                                        }?>
                                            >提现不成功</option>
                                    <option value="2" 
                                        <?php if($status==2){
                                            echo "selected='selected'";
                                        }?>
                                            >提现成功</option>
                                    <option value="11" 
                                        <?php if($status==11){
                                            echo "selected='selected'";
                                        }?>
                                            >提现驳回</option>
                                </select>
                            </td>
                            <?php }?>
                            <td><span class="title">提现时间</span></td>
                            <td>
                                <input type="text" value="<?= Yii::$app->request->get('time') ?>" name = "time" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d') ?>"});'/>                 
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
                        <th>提现金额（元）</th>
                        <th>银行</th>
                        <th>提现时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= number_format($val['money'], 2) ?></td>                        
                        <td><?= Yii::$app->params['bank'][$val['bank_id']]['bankname'] ?></td>                        
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td>
                            <?php
                                if ($val['status'] === '0') {
                            ?>
                                    <a class="btn mini green ajax_op" index="<?= $val['id'] ?>"><i class="icon-edit"></i>审核</a>
                            <?php
                                } elseif ($val['status'] === '1') {
                                    echo "已审核";
                                } elseif ($val['status'] === '3') {
                                    echo "提现不成功";
                                } elseif ($val['status'] === '2') {
                                    echo "提现成功";
                                } elseif ($val['status'] === '4') {
                                    echo "已放款";
                                } elseif ($val['status'] === '5') {
                                    echo "已经处理";
                                } else {
                                    echo "提现驳回";
                                }
                            ?>
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
    var uid = "<?= $id ?>";
    function doop(id,status){
        csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
        openLoading();//打开loading
        $.post("/user/drawrecord/drawexamin", {id: id, status:status,uid:uid, _csrf:csrf}, function (result) {
            cloaseLoading();//关闭loading
            newalert(result['res'],'');
            location.reload();
        });
    }
    $(function () {
         
        $('.ajax_op').bind('click', function () {
            index = $(this).attr('index');
            csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
            layer.confirm('确定审核通过吗？',{title:'充值审核',btn:['提现完成','撤销','关闭'],closeBtn:false},function(){ 
                    doop(index,2);
            },function(){
                doop(index,3);  
            })
        
        });
            
            
    })
</script>
<?php $this->endBlock(); ?>

