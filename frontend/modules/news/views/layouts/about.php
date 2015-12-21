<?php $this->beginContent('@app/views/layouts/main.php'); 
use frontend\models\NewsCategoryData;
$cid =  Yii::$app->request->get('cid');
$aid =  Yii::$app->request->get('aid');
$news = NewsCategoryData::getMemberNews($cid,'sort desc');
$news_info = NewsCategoryData::getNews(["id"=>$aid]);
$this->title = $news_info['title'];
?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/news/about?cid=<?=$cid ?>">关于我们</a>
		</li>
		<li>
			中心简介
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<ul>
                            
                            <?php foreach($news as $key=>$val){ ?>
                            <li <?php if($aid==$val->id||(empty($aid)&&$key==0)){echo "class='selected'";} ?> >
                                    <a href="/news/about?aid=<?= $val->id ?>&cid=<?= $val->category_id ?>"><?= $val->title ?></a>
                            </li>
                            <?php } ?>
                            
			</ul>
		</div>
		<div class="page-left-bottom"></div>
	</div>
	<?= $content ?>
</div>
<?php $this->endContent(); ?>

