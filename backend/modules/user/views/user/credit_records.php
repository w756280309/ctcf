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
                        <a href="javascript:void(0)">债权投资明细</a>
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
                            <span class="title">投资金额总计（元）：<?=number_format(bcdiv($txRes['totalInvestAmount'], 100, 2),2)?></span>
                        </td>
                        <td>
                            <span class="title">成功（次）：<?=$txRes['successCount']?></span></td>
                        <td>
                            <span class="title">失败（次）：<?=$txRes['errorCount']?></span></td>
                        </td>
                    </tr>
            </table>
        </div>

        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>债权订单ID</th>
                        <th>项目名称</th>
                        <th>投资金额（元）</th>
                        <th>投资时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($txRes['data'] as $key => $order) : ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $loan[$order['loan_id']]['title'] ?></td>
                        <td><?= number_format(bcdiv($order['principal'], 100, 2),2) ?></td>
                        <td><?= $order['createTime']?></td>
                        <td><?php 
                                    if($order['status'] == 0){
                                        echo "未处理";
                                    }elseif($order['status']==1){
                                        echo "投资成功";
                                    }elseif($order['status']==2){
                                        echo "失败";
                                    }else{
                                        echo "处理中";
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

