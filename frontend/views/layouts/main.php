<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
use frontend\models\ProductCategoryData;
use frontend\models\NewsCategoryData;

AppAsset::register($this);
$pcd = new ProductCategoryData();
$cat_data = $pcd->category(['status'=>1,'parent_id'=>0]);
error_reporting(E_ALL^E_NOTICE);

$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <link rel="shortcut icon" href="/images/favicon.ico" type="imagend.microsoft.icon">
	<?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
		<div class="header">
			<div class="top">
				<div class="content">
					<div class="fl">
						<span>服务电话：025-8570-8888</span>
						<span>工作时间：9:00 - 17:00</span>
			<!--	<img src="/images/wechat_icon.png" >  -->
					</div>
					<div class="fr">
						<ul>
                                                    
                                                    <li><a href="http://channel.njfae.cn" style="font-weight: bold;font-size: 14px; color: #ff9600 ">分销平台用户查询入口</a></li>  
                                     <li><span class="split"></span></li>
                            <?php if(Yii::$app->user->isGuest){ ?>
                                <li><a href="/">登录</a></li>
                                <li><span class="split"></span></li>
                                <li><a href="/user/register/prereg">注册</a></li>
                            <?php }else{ ?>
                                <li>欢迎:<a href="/user/onlinetender/means?current=2">  <?= Yii::$app->user->getIdentity()->username ?></a></li>
                                <li><span class="split"></span></li>
                                <li><a href="/user/login/logout">退出</a></li>
                             <?php  }  ?>

							<li><span class="split"></span></li>
							<li><a href="/news/helper?cid=8">帮助中心</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="nav">
				<a href="/" target="_self"><img class="logo" src="/images/logo.png"  /></a>
				<div id="navlist" class="navlist">
					<ul>
						<li><a href="/">首页</a></li>
						<li><a href="/news/">新闻动态</a></li>
						<li class="down">
							<a href="javascript:void(0);">产品公告</a>
							<div class="nav-sub none" style="width: 165px;<!--left:-15px;-->">
								<ul>
                                                                    <?php foreach ($cat_data as $key=>$val){ ?>
									<li><a href="/product?cid=<?= $key ?>"><?= $val['front_name'] ?></a></li>
                                                                   <?php
                                                                   }
                                                                    ?>
								</ul>
							</div>							
						</li>
						<li class="down">
							<a href="#">会员服务</a>
							<div class="nav-sub none">
								<ul>
									<li><a href="/user/">账户查询</a></li>
									<li class="left">
										<a href="javascript:void(0)">机构会员</a>
										<div class="nav-subs none2" style="left:100px;top:38px;border-top:1px solid #F2F2F2">
											<ul>
                                                                                            <?php foreach (NewsCategoryData::getMemberType() as $val){ ?>
                                                                                                <li><a href="/news/member?aid=<?=$val->id?>&list=<?=$val->name?>"><?=$val->name?></a></li>
                                                                                            <?php } ?>
											</ul>
										</div>
									</li>
									<li class="left"><a href="javascript:void(0)">会员规则</a>
                                                                        
                                                                                <div class="nav-subs none2" style="left:100px;top:38px;border-top:1px solid #F2F2F2">
											<ul>
                                                                                            <?php foreach (NewsCategoryData::getMemberNews(6) as $val){ ?>
                                                                                                <li><a href="/news/member?aid=<?=$val->id?>"><?=$val->title?></a></li>
                                                                                            <?php } ?>
											</ul>
										</div>
                                                                        </li>
								</ul>
							</div>
						</li>
						<li style="padding-right: 0;"><a href="/news/about?cid=7">关于我们</a></li>
					</ul>
				</div>
			</div>
		</div>

	    
		<?= $content ?>
	    

		<div class="footer">
			<div class="footer-content">
				<div class="fl" style="width: 740px;">
					<div class="fl address">
						<p>
							<!-- <a href="#">网站地图</a> -->
							<a href="/news/about/job/">招贤纳士</a>
							<a href="/news/about/contact/">联系我们</a>
						</p>
						<p>地址：南京市太平南路450号斯亚财富中心8F</p>
					</div>
					<div class="fr contact">
						<p>客服热线（工作时间9:00 - 17:00）</p>
						<p>025-8570-8888</p>
					</div>
					<div class="clear copyright">
						版权所有 &copy; 2014-2015 苏ICP备15001775号-1
						<span fl></span>
					</div>
				</div>
				<div class="fr">
					<img src="/images/eqcode.png"  />
				</div>
			</div>
		</div>
		

    <?php $this->endBody() ?>
		<script type="text/javascript">
			$(function(){
                            $(".down").mouseover(function(){
                                    $(this).find(".nav-sub").removeClass("none");
                            })
                            .mouseout(function(){
                                    $(this).find(".nav-sub").addClass("none");
                            })
                            $(".left").mouseover(function(){
                                    $(this).find("a:first").css({"color":"red"});
                                    $(this).css({"background":"url(/images/nav-left1.png) 82px 17px no-repeat"});
                                    $(this).find(".nav-subs").removeClass("none2");
                            })
                            .mouseout(function(){
                                    $(this).find("a:first").css({"color":"#8c8c8c"});
                                    $(this).css({"background":"url(/images/nav-left2.png) 82px 17px no-repeat"});
                                    $(this).find(".nav-subs").addClass("none2");
                            })
                        })
		</script>
</body>
</html>
<?php $this->endPage() ?>



