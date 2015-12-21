<?php 
$this->beginContent('@app/views/layouts/main.php'); 
$this->title="账户中心";
$current = Yii::$app->request->get('current');
if(empty($current)){
    $current=1;
}
$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJs('var laySum=0;', 1);
$this->registerJsFile('/js/layer/layer.min.js',['position'=>2]);
$this->registerJsFile('/js/layer/extend/layer.ext.js',['position'=>2]);
$this->registerJsFile('/js/loadua.js', ['position'=>2]);
?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/user">账户中心</a>
		</li>
		<li>
			账户信息
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<div class="page-left-content-title user-title-icon">账户中心</div>
			<ul>
                             <?php if(Yii::$app->user->getIdentity()->channel_id){ ?>
                                <li <?php if($current==2){ echo "class='selected'";} ?>>
					<a href="/user/default/means?current=2" >我的资产</a>
				</li>
                            <?php  }else{  ?>
				<li <?php if($current==1){ echo "class='selected'";} ?>>
					<a href="/user?current=1" >账户信息</a>
				</li>
				<li <?php if($current==2){ echo "class='selected'";} ?>>
					<a href="/user/onlinetender/means?current=2" >我的资产</a>
				</li>
				<li <?php if($current==3){ echo "class='selected'";} ?>>
					
					<a href="/user/default/fundmanage?current=3" >资金管理</a>
				</li>
                            <?php  }  ?>    
                                
			</ul>
		</div>
		<div class="page-left-bottom"></div>
	</div>
	<?= $content ?>
</div>
<?php $this->endContent(); ?>

