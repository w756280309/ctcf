<?php
use yii\widgets\ActiveForm;
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
                        <a href="/product/productonline/list">贷款管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    
                    <li>
                        <a href="/product/productonline/list">项目列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">还款计划</a>
                    </li>
            </ul>
        </div>
        
         <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">项目名称：<?=$deal->title?></span>
                        </td>
                        <td>
                            <span class="title">应还款人数：<?=$count?></span>
                        </td>
                        <td>
                            <span class="title">应还款本金：<?=$yhbj?>元</span>
                        <td>
                            <span class="title">应还款利息：<?=$yhlixi?>元</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="title">应还款本息：<?=$total_bx?>元</span>
                        </td>
                        <td>                            
                        </td>
                        <td>
                        </td>
                        <td>                        
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>真实姓名</th>
                        <th>手机号</th>
                        <th>应还款本金（元）</th>
                        <th>应还款利息（元）</th>
                        <th>应还款本息（元）</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?=$key+1 ?></td>
                        <td><?= $val['real_name'] ?></td>
                        <td><?= $val['mobile'] ?></td>                   
                        <td><?= $val['benjin'] ?></td>                   
                        <td><?= $val['lixi'] ?></td>                   
                        <td><?= bcadd($val['benjin'], $val['lixi'],2)?></td>
                    </tr>
                    <?php endforeach; ?>   
                </tbody>
            </table>
        </div>
        
        <?php if($deal->status==5){ ?>
        <div class="form-actions" style="text-align:right">
                <button type="button" class="btn blue button-repayment"><i class="icon-ok"></i> 确认还款</button>
        </div>
        <?php } ?>
    </div>                            
</div>
<script type="text/javascript">
    
    $(function(){
        $('.button-repayment').click(function(){
            var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
            var pid = '<?= Yii::$app->request->get('pid');?>';
            if(confirm('确认还款吗？')){
                $(this).attr('disabled','disabled');
                $(this).html('正在处理……');
                openLoading();
                   $.post('/repayment/repayment/dorepayment',{pid:pid,_csrf:csrftoken},function(data)
                   {
                       var dat;
                       if(!isJson(data)){
                           dat = eval("(" + data + ")");
                       }else{
                            dat = data;
                            if(data.result){
                                location.reload();
                            }
                        }
                        alert(dat.message);
                       cloaseLoading();
                       $('.button-repayment').removeAttr('disabled');
                        $('.button-repayment').html('<i class="icon-ok"></i> 确认还款');
                   }); 
                   
               }
        });
    })
</script>
<?php $this->endBlock(); ?>

