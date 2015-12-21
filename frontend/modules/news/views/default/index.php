<?php
use yii\widgets\LinkPager;
use frontend\models\NewsCategoryData;
$nc_model = new NewsCategoryData();
$cid = Yii::$app->request->get('cid');
if(empty($cid)){
    $cid=1;
}
$current_news = $nc_model->getNewsCat(["id"=>$cid]);
$this->title=$current_news->name."-".$news_info->title;

?>
	<div class="fr page-right"  class="top">
            
            <?php if($news_info->id){?>
            <div class="page-right-detail">
			<h2 style="font-size:24px; font-weight:normal;color:#474747;"><?=$news_info->title?></h2>
                        <?php if(!empty($news_info->child_title)){ ?><h4 style="text-align: center;font-family:'微软雅黑';font-size:16px;color:#929292;padding-top:13px;font-weight:normal;">——&nbsp;<?=$news_info->child_title?></h4><?php } ?>
			<ul class="info">
                            <li>发布时间：<?= date("Y-m-d",$news_info->news_time)?></li>
				<li>来源：<?= $news_info->source?></li>
			</ul>
			<div>
				<?= $news_info->body?>
			</div>
                        
                        <div style="margin-top: 30px;"></div>
                        <?php foreach($filse as $key=>$val){ ?>
                        <div>
                            <a href="/upload/news/<?=$val['content']?>" target="_blank">附件<?=$key+1?></a>
			</div>
                        <?php } ?>
                        
		</div>
            <?php }else { ?>
		<ul class="page-right-list">
			
                    <?php foreach ($news_model as $val) {?>
                       <li>
				<a href="/news/default?nid=<?=$val->id ?>&cid=<?= $model->id?>"><?=$val->title ?><span class="fr"><?=  date('Y-m-d',$val->news_time) ?></span></a>
                                
			</li>
                    <?php }?>  
                       
		</ul>
             <?= LinkPager::widget(['pagination' => $pages]); ?>
            <?php } ?>
	</div>
	<style>
		.body .page-left {width:207px;margin-bottom:11%;}
	</style>

