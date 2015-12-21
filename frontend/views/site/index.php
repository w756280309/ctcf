<?php

use frontend\models\ProductCategoryData;
use yii\widgets\ActiveForm;
error_reporting(E_ALL^E_NOTICE);
$pcd = new ProductCategoryData();
$cat_data = $pcd->category(['status' => 1, 'parent_id' => 0,'home_status' => 1]);
$this->title = '南京金融资产交易中心 - 首页';

$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJsFile('/js/index.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);

$this->registerJsFile('js/jquery.kinMaxShow-1.1.src.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJsFile('js/jquery.SuperSlide.2.1.1.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);

?>
<div id="kinMaxShow">
    <?php if(empty($index_adv['index_header'])||empty($index_adv['index_header']['adv'])){ ?>
    <div>
        <a href="javascript:return;">
            <img src="/images/1.png" />
        </a>
    </div>
    <div>
        <a href="javascript:return;">
            <img src="/images/2.gif" />
        </a>
    </div>
    <?php }else{        foreach($index_adv['index_header']['adv'] as $val){ ?>
    <div>
        <a href="<?= $val['link'] ?>">
            <img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" />
        </a>
    </div>
    <?php } ?>
    <div>
        <a href="javascript:return;">
            <img src="/images/1.png" />
        </a>
    </div>
<!--    <div>
        <a href="javascript:return;">
            <img src="/images/2.gif" />
        </a>
    </div>-->
    <?php } ?>
    
</div>

<div class="body">
    <div class="line">
        <ul>
            <li>
                <!--0417换首页轮播
                <div id="imgslider1" class="part0">
                    <div class="hd">
                        <ul>
                            <?php if(empty($index_adv['index_left'])||empty($index_adv['index_left']['adv'])){ ?>
                            <li>
                                <a href="javascript:return;">
                                    <img src="/images/p.png"/>
                                </a>										
                            </li>
                            <li>
                                <a href="javascript:return;">
                                    <img src="/images/1.png"/>
                                </a>										
                            </li>
                            <?php }else{        foreach($index_adv['index_left']['adv'] as $val){ ?>
                            <li>
                                <a href="<?= $val['link'] ?>">
                                    <img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" />
                                </a>										
                            </li>
                            <?php } } ?>
                        </ul>
                    </div>
                </div>
             -->
            <div id="" class="claier">
			      <div class="bd">
			            <ul>
			                  <?php if(empty($index_adv['index_left'])||empty($index_adv['index_left']['adv'])){ ?>
			                  <li> <a href="javascript:return;"> <img src="/images/p.png" width="303" height="212"/> </a> </li>
			                  <li> <a href="javascript:return;"> <img src="/images/1.png" width="303" height="212"/> </a> </li>
			                  <?php }else{        foreach($index_adv['index_left']['adv'] as $val){ ?>
			                  <li> <a href="<?= $val['link'] ?>"> <img src="<?= $val['image']  ?>" width="303" height="212" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" /> </a> </li>
			                  <?php } } ?>
			            </ul>
			      </div>
			</div>
			<script type="text/javascript">
				  jQuery(".claier").slide({mainCell:".bd ul",autoPlay:true})
			</script>
            </li>
            <li>
                <div class="part2">
                    <div class="part2-title part2-1">
                        <a href="/news?cid=2">更多 &gt;&gt;</a>
                    </div>
                    <div class="part2-content">
                        <ul class="title-list">
                            <li style="height:10px;"></li>
<?php foreach ($news[2] as $val) { ?>
                                <li>
                                    <a href="/news?cid=2&nid=<?= $val['id'] ?>"><?= $pcd->msubstr($val['title'],0,18); ?></a>
                                    <span><?= date('Y-m-d', $val['news_time']); ?></span>
                                </li>
<?php } ?>
                        </ul>
                    </div>
                    <div class="part2-bottom"></div>
                </div>
            </li>
            <li>
                <div class="part3">
                    <div class="part3-title part3-1"></div>
                    <div class="part3-content">
                        <?php if (Yii::$app->user->isGuest) {?>
                            <?php $form = ActiveForm::begin(['id'=>'user_form', 'action' =>"/" ]); ?>
                            <div class="login-input-bg">
                                 <img class="login-img" src="/images/login-user-bg.png" />
                                <?=$form->field($user_model, 'username', ['inputOptions'=>['class'=>'login-input','placeholder'=>'用户名'], 'template' => '{input}']);?>
                                 <!--<input class="login-input" type="text" placeholder="用户名"  />-->
                             </div>
                             <div class="login-input-bg" style="margin:10px auto 5px;">
                                 <img class="login-img" src="/images/login-pwd-bg.png" />
                                 <?=$form->field($user_model, 'password', ['inputOptions'=>['class'=>'login-input','placeholder'=>'密 码'], 'template' => '{input}'])->passwordInput();?>
                                 <!--<input class="login-input" type="text" placeholder="密 码" />-->
                             </div>
<!--                        <div>
                            
                        </div>-->
                             <div class="login-link">
                                 <a href="/user/find?step=1" class="login-link_forgetpaw">忘记密码</a>
                                 <a href="/user/register/prereg">免费注册</a>
                             </div>
                             <div class="error_yran_yhm">
                                 <span>
                                    <?=$form->field($user_model, 'username', ['template' => '{error}']);?>
                                 </span>
                             </div>
                             <div class="error_yran_mm" id="enna">
	                             <span>
	                                   <?=$form->field($user_model, 'password', ['template' => '{error}']);?>
	                             </span>
                             </div>
                             <script>
                              var username = document.getElementById("loginform-username").value.replace(/[]/g,"");
                                    if(username.length < 5){
                                    	$("#enna").css("display","none");
                                    } 
                                    <?php if(!$calert){ ?>
                                        var boolalert = 0;
                                        jQuery(document).ready(function () {
                                            $('#loginform-username').bind('click',function(){
                                                if(boolalert==0){
                                                    alert('如果您是通过京东金融平台购买项目的用户，请点击网页上部的“ 分销平台用户查询入口 ”进行登录。');
                                                }
                                                boolalert=1;
                                            })
                                        })
                                    <?php } ?>
                             </script>
                             <div class="login-btn">
                                 <input type="submit" class="login-btn" value=" "  />
                             </div>
                        <script type="text/javascript">
//                        $('.login-btn').click(function(){
//                            if($("#loginform-username").val()==""||$("#loginform-password").val()==""){
//                                alert("请输入用户名密码");return false;
//                            }
//                            //$("#user_form").submit();
//                            $.post("/user/login/alog", {username:$("#loginform-username").val(),password:$("#loginform-password").val()}, function (data) {  
////                                /res(data,"/adv/adv/index");
//                            console.log(data);
//                             }); 
//                        })
                        </script>
                            <?php ActiveForm::end(); ?>

                        <?php }else {?>
                                 <div class="login-ok login-ok-username">注册会员名
                                     <?=  Yii::$app->user->identity->username ?>
                                 </div>
                                 <div class="login-ok login-ok-welcome">欢迎登录南京金融资产交易中心</div>
                                 <div class="login-ok">
                                      <input type="button" onclick="window.location.href='/user/'"  class="login-ok-btn" value=""  />
                                 </div>
                        <?php }?>
                    </div>
                    <div class="part3-bottom"></div>
                </div>
            </li>
        </ul>
    </div>

    <div class="line">
        <ul>
            <li>
                <div class="part1">
                    <div class="part1-title part1-2">
                        <a href="/news?cid=4">更多 &gt;&gt;</a>
                    </div>
                    <div class="part1-content" style="height: 160px;">
                        <ul class="title-list">
                            <li style="height:10px;"></li>
<?php foreach ($news[4] as $val) { ?>
                                <li>
                                    <a href="/news?cid=4&nid=<?= $val['id'] ?>"><?= $pcd->msubstr($val['title'],0,17); ?></a>
                                </li>
<?php } ?>
                        </ul>
                    </div>
                    <div class="part1-bottom"></div>
                </div>
            </li>
            <li>
                <div class="part2">
                    <div class="part2-title part2-2">
                        <a href="/news?cid=3">更多 &gt;&gt;</a>
                    </div>
                    <div class="part2-content" style="height: 160px;">
                        <ul class="title-list">
                            <li style="height:10px;"></li>
<?php foreach ($news[3] as $val) { ?>
                                <li>
                                    <a href="/news?cid=3&nid=<?= $val['id'] ?>"><?= $pcd->msubstr($val['title'],0,18); ?></a>
                                    <span><?= date('Y-m-d', $val['news_time']); ?></span>
                                </li>
<?php } ?>
                        </ul>
                    </div>
                    <div class="part2-bottom"></div>
                </div>
            </li>
            <li>
                <div class="part3">
                    <div class="part3-title part3-2">
                        <a href="/news?cid=1">更多 &gt;&gt;</a>
                    </div>
                    <div class="part3-content" style="height: 160px;">
                        <ul class="title-list">
                            <li style="height:10px;"></li>
<?php foreach ($news[1] as $val) { ?>
                                <li>
                                    <a href="/news?cid=1&nid=<?= $val['id'] ?>"><?= $pcd->msubstr($val['title'],0,12); ?></a>
                                </li>
<?php } ?>
                        </ul>
                    </div>
                    <div class="part3-bottom"></div>
                </div>
            </li>
        </ul>
    </div>

   <!--0506换首页轮播
    <div id="imgslider2" class="line">
        <div class="hd" style="height:155px">
            <ul>
                <?php if(empty($index_adv['index_middle'])||empty($index_adv['index_middle']['adv'])){ ?>
                <li>
                    <a href="javascript:return;"><img src="/images/1.png"  /></a>
                </li>
                <li><a href="javascript:return;"><img src="/images/2.gif"  /></a></li>
                <?php }else{        foreach($index_adv['index_middle']['adv'] as $val){ ?>
                <li>
                    <a href="<?= $val['link'] ?>"><img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" /></a>
                </li>
                <?php } } ?>
            </ul>
        </div>
        <div class="bd">
            <ul>

            </ul>
        </div>
    </div>
     -->
     <div id="" class="line claierenna">
        <div class="bd" style="height:155px">
            <ul>
                <?php if(empty($index_adv['index_middle'])||empty($index_adv['index_middle']['adv'])){ ?>
                <li>
                    <a href="javascript:return;"><img src="/images/1.png"  /></a>
                </li>
                <li><a href="javascript:return;"><img src="/images/2.gif"  /></a></li>
                <?php }else{        foreach($index_adv['index_middle']['adv'] as $val){ ?>
                <li>
                    <a href="<?= $val['link'] ?>"><img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" /></a>
                </li>
                <?php } } ?>
            </ul>
        </div>
        <div class="hd">
            <ul>
                 <li></li>
                 <li></li>
                 <li></li>
                 <li></li>
            </ul>
        </div>
    </div>
        <script type="text/javascript">
				  jQuery(".claierenna").slide({mainCell:".bd ul",autoPlay:true})
		</script>
    <!-- start -->
<?php foreach ($cat_data as $key => $val) { ?>
        <div class="line">
            <div class="part4">
                <div class="part4-title" style="background:url('../images/part4-<?= $key ?>-bg.png') no-repeat">
                    <ul class="table-list-tab">

                        <?php
                                $child_cat_data = $pcd->getSubCat(['status' => 1, 'parent_id' => $key]);
                                foreach ($child_cat_data as $k => $v) {
                                ?>
                        <li <?php if ($k == 0) {
                            echo 'class="selected"';
                        } ?> data-index="pro_show_<?= $v['id'] ?>">
        <?= $v['name'] ?>
        <!--                                                                    <a href="/product?cid=<?= $v['id'] ?>"><?= $v['name'] . '-' . $k ?></a>-->
                            </li>
    <?php } ?> 
                            <?php if(count($child_cat_data)==0){ ?>
                            <li data-index="pro_show_<?= $key ?>">
                            </li>
                            <?php } ?>

                    </ul>
                    <a href="/product?cid=<?= $key ?>">更多 &gt;&gt;</a>
                </div>


                <div class="part4-content">
    <?php
    foreach ($child_cat_data as $k => $v) {
        ?>
                        <table class="pro_show_<?= $v['id'] ?> pro-table-list cat_<?= $v['id'] ?>" <?php if ($k != 0) {
            echo 'style="display:none"';
        } ?>>
                            <tr>
                                
                                <?php if($key!=4){   //4是特殊资产单独处理 ?>
                                    <th class="name" style="width:15%">名称</th>
                                    <th style="width:15%">编号</th>
                                    <th style="width:14%">预期收益</th>
                                    <th style="width:14%">规模</th>
                                    <th style="width:14%">期限</th>
                                    <th style="width:14%">起投金额</th>
                                    <th style="width:14%">项目状态</th>
                                <?php }else{ ?> 
                                    <th class="name" style="width:15%">资产名称</th>
                                    <th style="width:15%">编号</th>
                                    <th style="width:14%">类型</th>
                                    <th style="width:14%">挂牌底价</th>
                                    <th style="width:14%">报价截止日期</th>
                                <?php } ?> 
                                
                            </tr>
                        </table>
        <?php } ?> 
                    
                    <?php if(count($child_cat_data)==0){ ?>
                            <table class="pro_show_<?=$key ?> pro-table-list cat_<?= $key ?>">
                                <tr>
                                <?php if($key!=4){   //4是特殊资产单独处理 ?>
                                    <th class="name" style="width:15%">名称</th>
                                    <th style="width:15%">编号</th>
                                    <th style="width:14%">预期收益</th>
                                    <th style="width:14%">规模</th>
                                    <th style="width:14%">期限</th>
                                    <th style="width:14%">起投金额</th>
                                    <th style="width:14%">项目状态</th>
                                <?php }else{ ?> 
                                    <th class="name" style="width:15%">资产名称</th>
                                    <th style="width:15%">编号</th>
                                    <th style="width:14%">类型</th>
                                    <th style="width:14%">挂牌底价</th>
                                    <th style="width:14%">报价截止日期</th>
                                <?php } ?>
                            </tr>
                        </table>
                            <?php } ?>
                    
                </div>


                <div class="part4-bottom"></div>
            </div>
        </div>
<?php } ?>
    <!-- end -->



    <div class="line">
    	<div class="part4">
    		<div class="part4-title part4-5"></div>
    		
    	</div>
        <div class="friend-list">
        	<div class="hd">
	            <ul>
                        
                        <?php if(empty($index_adv['index_partner'])||empty($index_adv['index_partner']['adv'])){ ?>
                        <li>
	                    <img src="/images/nanjing-logo.png"  />
	                </li>
	                <li>
	                    <img src="/images/pingan-logo.png"  />
	                </li>
	                <li>
	                    <img src="/images/sohao-logo.png"  />
	                </li>
	                <li>
	                    <img src="/images/yunrun-logo.png"  />
	                </li>
                        <?php }else{        foreach($index_adv['index_partner']['adv'] as $val){ ?>
                        <li>
                            <a href="<?php if(empty($val['link'])){echo "javascript:void(0);";}else{echo $val['link'];} ?>"><img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" /></a>
                        </li>
                        <?php } } ?>
                        
                        
                        
	                
	            </ul>
            </div>
        </div>
        <div class="part4-bottom"></div>
    </div>

</div>