<?php

    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    
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
                        <a href="/news/">贷款管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/news/news/index">放款列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
            </ul>
        </div>
       
       
        
        <!--search start-->
        <div class="portlet-body">
            <form action="/order/onlinefangkuan/list" method="get" target="_self">
                <table class="table">
                    <input type="hidden" name="uid" value="<?=$uid?>"
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">状态</span>
                                <input type="hidden" name ='type' value = '<?=Yii::$app->request->get('type')?>'
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                   <option value="1" 
                                        <?php if(Yii::$app->request->get('status')==1){
                                            echo "selected='selected'";
                                        }?>
                                            >审核通过</option>
                                    <option value="2" 
                                        <?php if(Yii::$app->request->get('status')==2){
                                            echo "selected='selected'";
                                        }?>
                                            >审核拒绝</option>
                                </select>
                            </td>
                            <td><span class="title">放款时间</span></td>
                            <td>
                                <input type="text" name = 'time' value = "<?=Yii::$app->request->get('time')?>" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/> 
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
                        <th>放款人</th>
                        <th>放款金额（元）</th>
                        <th>手续费金额</th>
                        <th>借款人</th>
                        <th>放款创建时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $adminUsername ?></td>
                        <td><?= $val['order_money'] ?></td>                        
                        <td><?= $val['fee'] ?></td>                        
                        <td><?= $userUsername ?></td>                        
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?php 
                                    if($val['status']==1){
                                        echo "审核通过";
                                    }else {
                                        echo "审核拒绝";
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
        $(".chooseall").click(function(){
            var isChecked = $(this).prop("checked");
            alert("aaaaaaaaaaa");
            $("input[name='choose[]']").prop("checked", isChecked);
            alert("bbbbbbbbb");
        });
    })
</script> 
<?php $this->endBlock(); ?>

