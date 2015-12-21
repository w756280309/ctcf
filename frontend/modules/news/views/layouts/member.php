<?php 
$this->beginContent('@app/views/layouts/main.php');
use frontend\models\NewsCategoryData;

$aid =  Yii::$app->request->get('aid');
$list =  Yii::$app->request->get('list');
$news = NewsCategoryData::getNews(['id'=>$aid]);
//$this->title=$news['title'];
?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/user">会员中心</a>
		</li>
		<li>
			机构会员
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<div class="page-left-content-title product-title-icon-1">机构会员</div>
			<ul>
                            <?php foreach (NewsCategoryData::getMemberType() as $val){ ?>
                            <li <?php if(!empty($list)&&$aid==$val->id){ echo "class='selected'";} ?> ><a href="/news/member?aid=<?=$val->id?>&list=<?=  urlencode($val->name)?>"><?=$val->name?></a></li>
                            <?php } ?>
				
			</ul>
			<div class="page-left-content-title product-title-icon-2">会员规则</div>
			<ul>
                                <?php foreach (NewsCategoryData::getMemberNews(6) as $val){ ?>
                                    <li <?php if(empty($list)&&$aid==$val->id){ echo "class='selected'";} ?> ><a href="/news/member?aid=<?=$val->id?>"><?=$val->title?></a></li>
                                <?php } ?>
			</ul>
		</div>
		<div class="page-left-bottom"></div>
	</div>
	<?= $content ?>
</div>
<?php $this->endContent(); ?>

