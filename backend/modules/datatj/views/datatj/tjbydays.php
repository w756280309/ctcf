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
                    <small>（结算结果在次日告知）</small>
            </h3>        
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="">数据统计</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">统计</a>
                    </li>
            </ul>
                
        </div>
        
         
        <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">充值：234笔</span>
                        </td>
                        <td>
                            <span class="title">结算：1000笔</span>
                        </td>
                        <td>
                            <span class="title">充值总额：234000</span>
                        </td>
                        <td>
                            <span class="title">结算总额：1000000</span>
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">     
            <form action="/datatj/datatj/tjbydays" method="get" target="_self">
                <table class="table">
                    
                    <tbody>
                        <tr>
                            <td><span class="title">对账时间</span></td>
                            <td>
                                <input type="text" value="<?=$params['start']?>" name = "start" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d",strtotime("-1 day"))?>"});'/>
                                ---
                                <input type="text" value="<?=$params['end']?>" name = "end" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d",strtotime("-1 day"))?>"});'/>
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
                        <th>时间</th>
                        <th>充值笔数</th>
                        <th>充值金额</th>
                        <th>结算笔数</th>
                        <th>结算金额</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($model as $data){ ?>
                    <tr>
                        <td><?=$data->tx_date ?></td> 
                        <td><?=$data->recharge_count ?></td>
                        <td><?=$data->recharge_sum ?></td>                        
                        <td><?=$data->jiesuan_count ?></td>
                        <td><?=$data->jiesuan_sum ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="pagination" style="text-align:center"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
    </div>
                                    
</div>
<?php $this->endBlock(); ?>

