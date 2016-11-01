<?php

use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$status = Yii::$app->request->get('status');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员投资管理模块【主要包含投资会员的投资明细管理】</small>
            </h3>
            <ul class="breadcrumb">
                   <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?=$type==2?'listr':'listt'?>">会员管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                     
                    <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                    </li>                    
                    <li>
                        <a href="/user/user/<?= $type==2?'listr':'listt' ?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $id ?>&type=<?= $type ?>">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0)">标的投资明细</a>
                    </li>
            </ul>
        </div>
        
         <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">用户名：<?=$username?></span>
                        </td>
                        <td>
                            <span class="title">投资金额总计（元）：<?=number_format($moneyTotal,2)?></span>
                        </td>
                        <td>
                            <span class="title">成功（次）：<?=$successNum?></span></td>
                        <td>
                            <span class="title">失败（次）：<?=$failureNum?></span></td>
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">
            <form action="/order/onlineorder/detailt" method="get" target="_self">
                <table class="table">
                    <input type="hidden" name="id" value="<?= $id ?>"/>
                    <input type="hidden" name ='type' value = '<?= $type ?>'/>
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">状态</span>
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <option value="0" 
                                        <?php if($status=='0'){
                                            echo "selected='selected'";
                                        }?>
                                            >投标失败</option>
                                    <option value="1" 
                                        <?php if($status==1){
                                            echo "selected='selected'";
                                        }?>
                                            >投标成功</option>
                                    <option value="2" 
                                        <?php if($status==2){
                                            echo "selected='selected'";
                                        }?>
                                            >撤标</option>
                                    <option value="3" 
                                        <?php if($status==3){
                                            echo "selected='selected'";
                                        }?>
                                            >无效</option>
                                </select>
                            </td>
                            <td><span class="title">投资时间</span></td>
                            <td>
                                <input type="text" value="<?= Yii::$app->request->get('time') ?>" name = "time" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>  
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
                        <th>项目名称</th>
                        <th>投资金额（元）</th>
                        <th>投资时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $res[$val['id']] ?></td>
                        <td><?= number_format($val['order_money'],2) ?></td>                   
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?php 
                                    if($val['status']==0){
                                        echo "未支付";
                                    }elseif($val['status']==1){
                                        echo "投标成功";
                                    }elseif($val['status']==2){
                                        echo "撤标";
                                    }else{
                                        echo "无效";
                                    }
                                ?></td>
                    </tr>
                    <?php endforeach; ?>   
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div> 
    </div>
                 
</div>
<?php $this->endBlock(); ?>

