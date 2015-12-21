<?php

/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-18
 * Time: 下午7:46
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
  // * @var $model common\models\news\Category
 * @var yii\web\View $this
 * @var $categories

  $this->registerJsFile('/admin/js/validate.js', ['depends' => 'yii\web\YiiAsset']);
 *  */
$this->registerJsFile('/js/layer/layer.min.js', ['position' => 1,'depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/layer/extend/layer.ext.js', ['position' => 1,'depends' => 'yii\web\YiiAsset']);


?>
<?php $this->beginBlock('blockmain'); ?>


<div class="tab" id="tab">
    <a class="selected" href="/product/productonline/index">订单列表</a>
</div>

<div class="page_form">
        <div class="page_table form_table table_list">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <th><input type="checkbox" class="selectall " />全选</th>
                <th>批次号</th>
                <th>订单编号</th>
                <th>投标时间</th>
                <th>投资人</th>
                <th>金额</th>
                <th>状态</th>
                 <?php
                    foreach($list as $key=>$val)
                    {
                ?>
                <tr class="datalist">
                    <td><input name="s[]" type="checkbox" value="<?= $val['id'] ?>" data-batch="<?php echo empty($val['fkid'])?"—":$val['fkid'];?>"></td>
                    <td><?php echo empty($val['fksn'])?"—":$val['fksn'];?></td>
                    <td><?=$val['sn']?></td>
                    <td><?=  date('Y-m-d H:i:s',$val['order_time'])?></td>
                    <td><?=$val['username']?></td>
                    <td><?=$val['order_money']?></td>
                    <td><?php 
                            if(empty($val['fksn'])){
                                echo "未审核";
                            }else{
                                if($val['fkstatus']==2){
                                    echo "审核未通过";
                                }else if($val['fkstatus']==1){
                                    echo "已审核";
                                }else if($val['fkstatus']==3){
                                    echo "已放款";
                                }else{
                                    echo "未审核";
                                }
                            }
                            ?></td>
                </tr>
                
                 <?php
                    }
                ?>
                <tr><td colspan="7" align="center"><input type="button" value="审核通过" class="button" data-index="0" />&emsp;<input type="button" value="审核不通过" class="button" data-index="1"/>&emsp;<input type="button" value="放款" class="button" data-index="2" /></td></tr>
            </table>
            
        </div>
</div>
<script type="text/javascript">
    var urls = [
        '/order/order/checkfk',//审核通过
        '/order/order/fkdeny',//审核不通过
        '/order/order/fkop',//放款
    ];
//    function checkFangkuan(){
//        var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
//        var trs = $(".table_list .datalist input[type='checkbox']:checked");
//        var pid = <?=$pro_id ?>;
//        var oidarr = new Array();
//        for(i=0;i<trs.length;i++){
//            oidarr[i] = $(trs.get(i)).val();
//        }
//        if(trs.length){
//            $.post("/order/order/checkfk",{oids:oidarr.join('_'),pid:pid,_csrf:csrftoken},function(data)
//            {
//                console.log(data);
//            });
//        }
//    }


    jQuery(document).ready(function () {
        
        
        
        $('.button').bind('click',function(){
            var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
            key = $(this).attr('data-index');
            var trs = $(".table_list .datalist input[type='checkbox']:checked");
            var pid = <?=$pro_id ?>;
            var fkarr = new Array();
            var oidarr = new Array();
            for(i=0;i<trs.length;i++){
                oidarr[i] = $(trs.get(i)).val();
                fkarr[i] = $(trs.get(i)).attr('data-batch');
            }
            
            if(trs.length){
                remark = "";
                if(key==1){
                     layer.prompt({title: '请描述原因',type: 3}, function(val){
                        $.post(urls[key],{oids:oidarr.join('_'),pid:pid,fksn:fkarr.join('_'),remark:val,_csrf:csrftoken},function(data)
                        {
                            alert(data.msg);
                            if(data.res==1){
                                location.reload();
                            }
                        }); 
                    });
                }else{
                    bool = true;
                    if(key==2){
                        if(confirm('确认放款吗？')){
                            bool=true;
                        }else{
                            bool = false;
                        }
                    }
                    if(bool){
                        $.post(urls[key],{oids:oidarr.join('_'),pid:pid,fksn:fkarr.join('_'),remark:remark,_csrf:csrftoken},function(data)
                         {
                             alert(data.msg);
                             if(data.res==1){
                                 location.reload();
                             }
                         }); 
                     }
                }
                
            }else{
                alert('没有操作数据');
            }
        })
        $('.selectall').bind('click', function () {
            $(".table_list input[type='checkbox']").prop("checked", $(this).is(":checked"));
            //checkFangkuan();
            console.log(urls)
        })
    })
    </script>
<?php $this->endBlock(); ?>