<?php 
use frontend\models\NewsCategoryData;
$nc_model = new NewsCategoryData();
$ncd=$nc_model->category(['status'=>1]);
$cid = Yii::$app->request->get('cid');
if(empty($cid)){
    $cid=1;
}
$current_news = $nc_model->getNewsCat(["id"=>$cid]);
$this->beginContent('@app/views/layouts/main.php');
?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/news">新闻动态</a>
		</li>
		<li>
			<?=$current_news->name ?>
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<ul>
                            <?php foreach ($ncd as $key=>$val) {
                            if($key>4){ break;}    
                            ?>
                            <li <?php if($cid==$key){ echo "class='selected'";} if(empty($cid)&&$key==1){ echo "class='selected'";} ?>>
					<a href="/news?cid=<?= $key?>"><?= $val ?></a>
				</li>
                                <?php }?>
			</ul>
		</div>
		<div class="page-left-bottom"></div>
	</div>
	<?= $content ?>
</div>
<?php $this->endContent(); ?>

