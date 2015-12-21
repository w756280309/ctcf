<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="#">首页</a>
		</li>
		<li class="arrow">
			<a href="#">产品公告</a>
		</li>
		<li>
			财通宝
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<div class="page-left-content-title product-title-icon-1">金融资产交易类</div>
			<ul>
				<li class="selected">
					<a href="/product/">财通宝</a>
				</li>
				<li>
					<a href="#">同业宝</a>
				</li>
			</ul>
			<div class="page-left-content-title product-title-icon-2">公共事业融资类</div>
			<ul>
				<li>
					<a href="#">资管计划</a>
				</li>
				<li>
					<a href="#">私募债</a>
				</li>
			</ul>
			<div class="page-left-content-title product-title-icon-3">供应链经融类</div>
			<ul>
				<li>
					<a href="#">金产通</a>
				</li>
				<li>
					<a href="#">保理财</a>
				</li>
				<li>
					<a href="#">因收宝</a>
				</li>
			</ul>
			<div class="page-left-content-title product-title-icon-4">其他（特殊资产）类</div>
		</div>
		<div class="page-left-bottom"></div>
	</div>
	<?= $content ?>
</div>
<?php $this->endContent(); ?>

