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
                    数据统计
            </h3>        
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="">数据统计</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="">充值结算记录</a>
                    </li>
            </ul>
                
        </div>
        
         
        <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">冲值成功笔数（笔）：<?= $arr['succ_num'] ?></span>
                        </td>
                        <td>
                            <span class="title">充值失败笔数（笔）：<?= $arr['fail_num'] ?></span>
                        </td>
                        <td>
                            <span class="title">充值金额总计（元）：<?= $arr['fund_sum'] ?></span>
                        </td>
                        <td>
                            <span class="title">待结算笔数（笔）：<?= $arr['djs_num'] ?></span>
                        </td>
                        <td>
                            <span class="title">待结算金额总计（元）：<?= $arr['djs_sum'] ?></span>
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">     
            <form action="/datatj/datatj/rechargejs" method="get" target="_self">
                <table class="table">
                    
                    <tbody>
                        <tr>
                            <td><span class="title">时间</span></td>
                            <td>
                                <input type="text" value="<?=Yii::$app->request->get('start')?>" name = "start" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>
                                ---
                                <input type="text" value="<?=Yii::$app->request->get('end')?>" name = "end" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>
                            </td>
                            <td><span class="title">状态</span></td>
                            <td>
                                <select name="status" >
                                    <option value="">---未选择---</option>
                                    <option value="0" 
                                        <?php if($status==='0'){
                                            echo "selected='selected'";
                                        }?>
                                            >待结算</option>
                                    <option value="10" 
                                        <?php if($status==='10'){
                                            echo "selected='selected'";
                                        }?>
                                            >结算已受理</option>
                                    <option value="30" 
                                        <?php if($status==='30'){
                                            echo "selected='selected'";
                                        }?>
                                            >结算处理中</option>
                                    <option value="40" 
                                        <?php if($status==='40'){
                                            echo "selected='selected'";
                                        }?>
                                            >结算成功</option>
                                    <option value="50" 
                                        <?php if($status==='50'){
                                            echo "selected='selected'";
                                        }?>
                                            >结算失败</option>
                                    <option value="3" 
                                        <?php if($status==='3'){
                                            echo "selected='selected'";
                                        }?>
                                            >充值处理中</option>
                                    <option value="4" 
                                        <?php if($status==='4'){
                                            echo "selected='selected'";
                                        }?>
                                            >充值成功</option>
                                    <option value="5" 
                                        <?php if($status==='5'){
                                            echo "selected='selected'";
                                        }?>
                                            >充值失败</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span class="title">用户名</span></td>
                            <td>
                                <input type="text" value="<?= Yii::$app->request->get('name') ?>" name = "name"/>
                            </td>
                            <td></td>
                            <td></td>
                            <td style=" text-align: right;">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
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
                        <th>订单号</th>
                        <th>用户名</th>
                        <th>充值金额（元）</th>
                        <th>充值状态</th>
                        <th>充值时间</th>
                        <th>结算状态</th>
                        <th>结算金额（元）</th>
                        <th>结算时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($model as $val): ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $val['real_name']?$val['real_name']:"--" ?></td>                        
                        <td><?= $val['fund'] ?></td>                        
                        <td><?= Yii::$app->params['recharge']['status'][$val['status']] ?></td>
                        <td><?= date('Y-m-d H:i',$val['created_at']) ?></td>
                        <td><?= $val['status'] == 1?Yii::$app->params['recharge']['settlement'][$val['settlement']]:"--" ?></td>                        
                        <td><?= $val['status'] == 1 && ($val['settlement'] == 40 || $val['settlement'] == 50)?$val['fund']:"--" ?></td>
                        <td><?= $val['status'] == 1 && ($val['settlement'] == 40 || $val['settlement'] == 50)?date('Y-m-d H:i',$val['updated_at']):"--" ?></td>
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

