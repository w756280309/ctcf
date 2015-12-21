<?php
use yii\widgets\LinkPager;
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
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
                        <a href="">对账单</a>
                    </li>
            </ul>
                
        </div>
        
         
        <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">对账失败笔数（笔）：<?=$params['fail_count']?></span>
                        </td>
                        <td>
                            <span class="title">对账失败金额（元）：<?=$params['fail_sum']?></span>
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">     
            <form action="/datatj/datatj/accountsta" method="get" target="_self">
                <table class="table">
                    
                    <tbody>
                        <tr>
                            <td><span class="title">提现时间</span></td>
                            <td>
                                <input type="text" value="<?=$params['start']?>" name="start" class="m-wrap span4" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d",strtotime("-1 day"))?>"});'/>
                                ---
                                <input type="text" value="<?=$params['end']?>" name="end" class="m-wrap span4" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d",strtotime("-1 day"))?>"});'/>
                            </td>
                            <td>
                                <select name="check" >
                                    <option value="">---未选择---</option>
                                    <option value="0" 
                                        <?php if($params['check']==='2'){
                                            echo "selected='selected'";
                                        }?>
                                            >对账失败</option>
                                    <option value="1" 
                                        <?php if($params['check']==1){
                                            echo "selected='selected'";
                                        }?>
                                            >对账成功</option>
                                </select>
                            </td>
                            <td colspan="6" align="right" style=" text-align: right">
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
                        <th>交易流水号</th>
                        <th>类型</th>
                        <th>交易金额</th>
                        <th>时间</th>
                        <th>实收金额</th>
                        <th>手续费</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($model as $data){ ?>
                    <tr>
                        <td><?=$data->order_no ?></td>
                        <td><?=$data->tx_sn ?></td>                        
                        <td><?=Yii::$app->params['trade_type'][$data->tx_type] ?></td>                        
                        <td><?=$data->tx_amount ?></td>
                        <td><?=$data->bank_notification_time ?></td>
                        <td><?=$data->payment_amount ?></td>                        
                        <td><?=$data->institution_fee ?></td>
                        <td>
                            <?php if($data->is_auto_okay==1){ ?>
                            <span style="color:green">对账成功</span>
                            <?php }else if($data->is_auto_okay==2){ ?>
                                <span style="color:red">对账失败</span>
                                <?php if($data->is_okay==1){ ?>
                                    <span style="color:green">已处理</span>
                                <?php }else{ ?>
                                    <span style="color:red">未处理</span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="pagination" style="text-align:center"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
        
    </div>
                                    
</div>
<?php $this->endBlock(); ?>

