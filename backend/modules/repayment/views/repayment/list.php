<?php

    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    use common\models\user\User;
    
    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <br />
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/news/">会员管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if(Yii::$app->request->get('type')==User::USER_TYPE_PERSONAL){?>
                    <li>
                            <a href="/news/news/index">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }else{?>
                        <li>
                            <a href="/news/news/index">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }?>
                    <li>
                        <a href="/news/news/index">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/news/news/index">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/news/news/index">投资明细</a>
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
                            <span class="title">投资金额总计（元）：<?=$moneyTotal?></span>
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
            <form action="/order/onlineorder/detail" method="get" target="_self">
                <table class="table">
                    <input type="hidden" name="id" value="<?=Yii::$app->request->get('id')?>"
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">状态</span>
                                <input type="hidden" name ='type' value = '<?=Yii::$app->request->get('type')?>'
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <option value="0" 
                                        <?php if($_GET['status']=='0'){
                                            echo "selected='selected'";
                                        }?>
                                            >投标失败</option>
                                    <option value="1" 
                                        <?php if($_GET['status']==1){
                                            echo "selected='selected'";
                                        }?>
                                            >投标成功</option>
                                    <option value="2" 
                                        <?php if($_GET['status']==2){
                                            echo "selected='selected'";
                                        }?>
                                            >撤标</option>
                                    <option value="2" 
                                        <?php if($_GET['status']==3){
                                            echo "selected='selected'";
                                        }?>
                                            >无效</option>
                                </select>
                            </td>
                            <td><span class="title">投资时间</span></td>
                            <td>
                                <input type="text" value="<?=$_GET['time']?>" name = "time" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>  
                            </td>
                            <td colspan="6" align="right" style=" text-align: right">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 </button>
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
                        <th>投资金额（元）</th>
                        <th>投资时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $val['fund'] ?></td>                   
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?php 
                                    if($val['status']==0){
                                        echo "投标失败";
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


<script type="text/javascript">
    $(function(){
        
    })
</script> 
<?php $this->endBlock(); ?>

