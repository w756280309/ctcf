<?php
use yii\helpers\Html;
use wap\assets\AppAsset;
//AppAsset::register($this);
$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
	<?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
		<div class="header">
			<div class="top">
				这是主测试头部
			</div>
			
		</div>

	    
		<?= $content ?>
	    

		<div class="footer">
			这是主测试底部
		</div>
		

    <?php $this->endBody() ?>
		
</body>
</html>
<?php $this->endPage() ?>



