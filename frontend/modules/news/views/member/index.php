<?php
if(empty($list)){
    $this->title=$model->title;
}else{
    $this->title=$list;
}
?>	
<div class="fr page-right">

    <?php if(empty($list)){ ?>
    <div class="page-right-detail">
       <h2><?= $model->title ?></h2>
       <ul class="info">
        <li>发布时间：<?= date("Y-m-d",$model->news_time)?></li>
        <li>来源：<?= $model->source?></li>
    </ul>
    <div>
        <?= $model->body?>
    </div>
</div>
<?php }else{ ?>
<div class="page-right-detail">
    <h2 style="font-family:'微软雅黑';color:#474747;font-size:16px;font-weight:normal;text-align:left;line-height: 30px;"><?= $list ?></h2>
			<!--ul class="info">
                                <li>最后更新时间：<?= date("Y-m-d")?></li>
				<li>来源：官网发布</li>
			</ul-->
            <style>
            table,tr,th,td{border: 1px solid #E5E5E5;}
            </style>
            <div>
                <table>
                    <tr>
                        <!-- <td>序号</td> -->
                        <td>会员编号</td>
                        <td>机构名称</td>
                        <td>入会时间</td>
                    </tr>
                    <?php foreach($model as $key=>$val){ ?>
                    <tr>
                    <td><?= $val->usercode ?></td>
                    <td>
                     <?php if(!empty($val->org_url)){ ?>
                     <a href="<?= $val->org_url ?>" target="_blank"><?= $val->org_name ?></a>
                     <?php }else{ ?>
                     <?= $val->org_name ?>
                     <?php } ?>
                 </td>
                 <td><?= date("Y-m-d",$val->in_time) ?></td>
                 </tr>
                 <?php } ?>
             </table>
         </div>
     </div>
     <?php } ?>
 </div>