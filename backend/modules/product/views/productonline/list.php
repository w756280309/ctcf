<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">

                        贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
                        <a href="edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                        <i class="icon-plus"></i> 新增项目
                        </a>
                </h3>

                <ul class="breadcrumb">

                        <li>

                                <i class="icon-home"></i>
                                <a href="/product/productonline/list">贷款管理</a> 
                                <i class="icon-angle-right"></i>

                        </li>

                        <li>
                                <a href="javascript:void(0);">项目列表</a>
                        </li>
                        

                </ul>
        </div>

        
        <!--search start-->
        <div class="portlet-body">
            <form action="/product/productonline/list" method="get" target="_self">
            <table class="table">
                <tbody>
                <tr>
                    <td>
                        <span class="title">项目名称</span>
                    </td>
                    <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= Yii::$app->request->get('name') ?>"  placeholder="请输入项目名称"/></td>
                    <td><span class="title">状态</span></td>
                    <td>
                        <select class="m-wrap" style="margin-bottom: 0px;width:300px" name = 'status'>
                            <option value="">--请选择--</option>
                            <option value="0" <?= Yii::$app->request->get('status')=='0'?'selected':"" ?>>未上线</option>
                            <?php foreach ($status as $key => $val): ?>                            
                                <option value="<?= $key ?>" 
                                <?php
                                if (Yii::$app->request->get('status') == $key) {
                                        echo 'selected';
                                }
                                ?> ><?= $val ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><div align="right" style="margin-right: 20px">
                        <button type='submit' class="btn blue btn-block" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
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
                                        <th><input class="chooseall" type='checkbox'></th>
                                        <th>序号</th>
                                        <th>项目名称</th>
                                        <th>期限（天）</th>
                                        <th>利率（%）</th>
                                        <th>募集金额（元）</th>
                                        <th>满标时间</th>
                                        <th>起息时间</th>
                                        <th>状态</th>
                                        <th><center>操作</center></th>
                                </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $key => $val) : ?>
                                <tr>
                                        <td>
                                             <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                                        </td>
                                        <td><?= $val['sn'] ?></td>
                                        <td><?= $val['title'] ?></td>
                                        <td><?= $val['expires'] ?></td>
                                        <td><?= doubleval(100*$val['yield_rate']) ?></td>
                                        <td><?= number_format($val['money'],2) ?></td>
                                        <td>
                                            <?= ($val['status'] > 2 && $val['status'] != 4)?date('Y-m-d H:i:s',$val['full_time']):'--'?>
                                        </td>
                                        <td>
                                            <?= !empty($val['jixi_time'])?date('Y-m-d',$val['jixi_time']):'--'?>
                                        </td>
                                        <td><?= $val['online_status']?$status[$val['status']]:"未上线" ?></td>
                                        <td>
                                            <a href="/product/productonline/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                            <?php if ($val['status'] < 2){ ?>
                                            | <a href="javascript:del('/product/productonline/del','<?= $val['id'] ?>')" class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>删除</a>
                                            <?php } ?>
                                            <?php if ($val['online_status'] == 1 && $val['status']==3 && empty($val['fk_examin_time'])){ ?>
                                            | <a href="javascript:openwin('/order/onlinefangkuan/examinfk?pid=<?= $val['id'] ?>',800,400)" class="btn mini green"><i class="icon-edit"></i> 放款审核</a>
                                            <?php } ?>
                                            <?php if ($val['online_status'] == 1 && $val['status'] > 1){ ?>
                                            | <a href="/order/onlineorder/list?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 投标记录</a>
                                            <?php } ?>
                                            <?php if ($val['status'] == 5||$val['status'] == 6){ ?>
                                            | <a href="/repayment/repayment?pid=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 还款</a>
                                            <?php } ?>
                                            <?php if (($val['fk_examin_time'])>0&&$val['status'] == 3){ ?>
                                            | <a href="javascript:fk('<?= $val['id'] ?>');" class="btn mini green"><i class="icon-edit"></i> 放款</a>
                                            <?php } ?>
                                            <?php if ($val['online_status']==1&&(in_array($val['status'],[3,5,7]))&&$val['is_jixi']==0){ ?>
                                            | <a href="javascript:void(0)" onclick="openwin('/product/productonline/jixi?product_id=<?= $val['id'] ?>',500,300)" class="btn mini green"><i class="icon-edit"></i> 计息</a>
                                            | <a href="javascript:corfirmJixi('<?= $val['id'] ?>');" class="btn mini green"><i class="icon-edit"></i> 确认计息</a>
                                            <?php } ?>
                                            <?php if ($val['online_status']==1&&$val['status']==2){ ?>
                                            | <a href="javascript:endproduct('<?= $val['id'] ?>')" class="btn mini green"><i class="icon-edit"></i> 结束项目</a>
                                            <?php } ?>
                                        </td>
                                </tr>
                                <?php endforeach; ?>   
                        </tbody>
                </table>
                <button class="btn green btn-block btn-block-line-on" style="width: 100px;float:left;"><i class="icon-edit"></i>上线</button>
        </div>
                
         
              
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?>
       
</div>


<script type="text/javascript">
    $(function(){
        $(".chooseall").click(function(){
            var isChecked = $(this).parent().hasClass('checked');
            if(!isChecked){
                $("input[name='choose[]']").parent().addClass('checked');
            }else{
                $("input[name='choose[]']").parent().removeClass('checked');
            }
        });
        
        $('.btn-block-line-on').click(function(){
            var objs = $("input[name='choose[]']").parent();
            var ids = new Array();
            var ckidkey = 0;
            for(var i=0;i<objs.length;i++){
                if($(objs[i]).hasClass('checked')){
                    ids[ckidkey]=$($("input[name='choose[]']").get(i)).val();
                    ckidkey++;
                }
            }
            if(ids.length==0){
                alert('请选择上线记录');return false;
            }
             var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
             if(confirm('确认将选择的记录上线吗？')){
                 openLoading();
                    $.post('/product/productonline/lineon',{pids:ids.join(','),_csrf:csrftoken},function(data)
                    {
                        alert(data.message);
                        if(data.result==1){
                            location.reload();
                        }
                        cloaseLoading()
                    });     
                }
        })
    })
    
    function fk(pid){
        var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
             if(confirm('确认放款吗？')){
                 openLoading();
                    $.post('/repayment/repayment/fk',{pid:pid,_csrf:csrftoken},function(data)
                    {
                        alert(data.message);
                        if(data.result==1){
                            location.reload();
                        }
                        cloaseLoading()
                    });     
                }
    }
    
    function del(url,id){
        var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
        //alert(1);return false;
        $.post(url,{id:id,_csrf:csrftoken},function(data)
        {   
             newalert(data,'删除成功',1);
        });
    }
    
    function corfirmJixi(pid){
        var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
             if(confirm('确认计息吗？')){
                 //openLoading();
                 $.post('/product/productonline/jixicorfirm',{id:pid,_csrf:csrftoken},function(data)
                    {
                        alert(data.message);
                        //console.log(data);
                        if(data.result==1){
                           // location.reload();
                        }
                        //cloaseLoading()
                    });  
             }
    }
    
    function endproduct(pid){
         var csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
            layer.confirm('是否要提前结束此项目的募集？',{title:'结束项目',btn:['取消','确定']},function(){
                layer.closeAll();
            },function(){
                openLoading();//打开loading
                $.post("/product/productonline/end-product", {pid: pid, _csrf:csrf}, function (result) {
                    cloaseLoading();//关闭loading
                    newalert(result['res'],'');
                    location.reload();
                });   
            })
    }
</script> 
<?php $this->endBlock(); ?>


