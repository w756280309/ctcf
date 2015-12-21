<?php
$this->title = $cat_model->name . "-" . $model->title;
error_reporting(E_ALL ^ E_NOTICE);

use frontend\models\ProductCategoryData;

$pcd = new ProductCategoryData();
$this->registerJs('var laySum=0;', 1);
$this->registerJsFile('/js/layer/layer.min.js', ['position' => 2]);
?>
<style type="text/css">
    .body .product-detail .prodetinfo{
        height:200px;
    }
    .body .product-detail .prodetinfo .prodetinfo-part1 li{
        margin-top: 0px;
    }
    .body .product-detail .prodetinfo .prodetinfo-part1 .text1{
        line-height: 40px;
        text-align:left;
        color: black;
    }
    .body .product-detail .prodetinfo .prodetinfo-part1 .text1 span{
        font-size: 22px;
        color: black;
		/*        font-weight: bold;*/
    }
    .special_prodetinfo{
        width:610px
    }
    .special_prodetinfo-part2 li{
        float: left;
        width: 240px;
    }
    .special_prodetinfo .clear{
        clear: both;
    }
    .special_prodetinfo-part2 li .text1{
        width:100px;
        float: left;
        height: 40px;
        line-height: 40px;
        vertical-align: middle;
        color:#8d8d8d;
    }
    .special_prodetinfo-part2 li .text2{
        width:100px;
        float: left;
        height: 40px;
        line-height: 40px;
        vertical-align: middle;
        font-size: 16px;
    }

    .special_prodetinfo-part2 li .special_prodetinfo_title{
        width:600px;
        float: left;
        height: 40px;
        line-height: 40px;
        vertical-align: middle;
        font-size: 16px;
        padding-top:30px;
        font-size: 22px;
    }
</style>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页1</a>
		</li>
		<li class="arrow">
			<a href="/product">产品公告</a>
		</li>
		<li class="arrow">
			<a href="/product?cid=<?= $cat_model->id ?>"><?= $cat_model->name ?></a>
		</li>
		<li>
<?= $model->title ?>
		</li>
	</ul>

	<div class="product-detail">

		<div class="part">
			<div class="fl prodetinfo" style="width:200px">
				<ul class="prodetinfo-part1">
					<li>
						<img src="/images/product_detail/default.png" />
					</li>
				</ul>
			</div>

			<div class="fl special_prodetinfo">
				<div>
					<ul class="special_prodetinfo-part2">
						<li>
							<div class="special_prodetinfo_title"><?= $model->title ?></div>
						</li>
					</ul>
				</div>
				<div class="clear"></div>

				<div>
					<ul class="special_prodetinfo-part2">
						<li>
							<div class="text1">项目编号：</div>
							<div class="text2"><?= $model->sn ?></div>
						</li>

						<li>
							<div class="text1">类型：</div>
							<div class="text2"><?= $model->special_type_title ?></div>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
				<div>
					<ul class="special_prodetinfo-part2">
						<li>
							<div class="text1">挂牌底价：</div>
							<div class="text2">
<?= $pcd->toFormatMoney($model->money) ?>
							</div>
						</li>

						<li>
							<div class="text1">项目结束时间：</div>
							<div class="text2">
<?= date("Y.m.d", $model->end_time); ?>
							</div>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
				<div>
					<ul class="special_prodetinfo-part2">
						<li>
							<div class="text1">项目联系人：</div>
							<div class="text2">
<?= $model->contact; ?>
							</div>
						</li>

						<li>
							<div class="text1">项目联系电话：</div>
							<div class="text2">
<?= $model->contact_mobile ?>
							</div>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
				<div>
					<ul class="special_prodetinfo-part2">
						<li>
							<div class="text1" style="color:red">当前最高报价：</div>
							<div class="text2" style="color:red; font-size: 26px; width: 140px;">
<?= $max_price ?>
							</div>
						</li>
					</ul>
				</div>
			</div>

			<div class="fl special_prodetinfo" style="width:100px; padding-top:40px;">

<?php if ($p_status == 11) { ?>
						<?php if (time() > $model->end_time) { ?>
						<div id="ljtz_click" style="width:99px;height:25px;background-color:#8d8d8d;border-radius:6px;text-indent:24px;line-height:25px;font-family:'微软雅黑';">
							<a style="color:#fff;" href="javascript:return;" onclick="alert('报价结束');">我要报价</a>
							<?php } else { ?>
							<div id="ljtz_click" style="width:99px;height:25px;background-color:#ff9600;border-radius:6px;text-indent:24px;line-height:25px;font-family:'微软雅黑';">
								<a style="color:#fff;" href="javascript:return;" onclick="myPrice();">我要报价</a>
							<?php } ?>

							<?php } else if ($p_status == 12) { ?>
							<div id="ljtz_click" style="width:99px;height:25px;background-color:#8d8d8d;border-radius:6px;text-indent:24px;line-height:25px;font-family:'微软雅黑';">
								<a style="color:#fff;" href="javascript:return;">我要报价</a>
								<?php } else if ($p_status == 13) { ?>
								<div id="ljtz_click" style="width:99px;height:25px;background-color:#8d8d8d;border-radius:6px;text-indent:24px;line-height:25px;font-family:'微软雅黑';">
	                                <a style="color:#fff;" href="javascript:return;">交割完成</a>
<?php } ?>
							</div>
						</div>
					</div>

					<div class="part">
						<div class="label" style="margin: 0px"></div>
<?= $model->description ?>

					</div>


				</div>
			</div>
			<script type="text/javascript">
				function getlay() {  //利用这个方法向子页面传递layer的index
					return laySum;
				}

				function myPrice() {
<?php if (Yii::$app->user->isGuest) { ?>
						alert('请登录');
<?php } else { ?>

						var pid = '<?= $model->id ?>';
						laySum = $.layer({
							type: 2,
							title: [
								'我要报价',
								'background:#f7f8fa; height:40px; color:#black; border:none; font-weight:bold;' //自定义标题样式
							],
							border: [0],
							area: ['400px', '300px'],
							close: function (index) {
								layer.close(index);
								location.reload();
							},
							iframe: {src: '/product/default/quote?pid=' + pid}
						})
<?php } ?>




				}
			</script>