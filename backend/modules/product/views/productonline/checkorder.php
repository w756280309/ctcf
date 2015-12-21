<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="tab" id="tab">
    <a class="selected" href="/product/productonline/index">查看所有订单列表</a>
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
            </table>
            
        </div>
</div>
<?php $this->endBlock(); ?>