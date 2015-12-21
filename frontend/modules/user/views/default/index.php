<?php
$this->registerJs("var laySum = 0; ;",1);//定义变量 介绍layer的index
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/tradepaw.js', ['depends' => 'yii\web\YiiAsset']);
?>

<div class="fr page-right">
	<div class="page-rigth-title">
		账户信息
	</div>
	<div class="page-right-detail">
		<div class="userinfo">
			<div class="line1">
				<div class="fl photo">
					<img src="/images/user-photo.png"  />
				</div>
				<ul style="display:block;width:570px;height:67px;float:left;">
					<li style="font-size:24px;height:35px;"><?= $model->username ?></li>
					<li>编号：<?= $model->usercode ?> </li>
					<li><?php if(($model->type==1&&empty($model->real_name)&&empty($model->email))||($model->type==1&&$model->idcard_status<>1)||($model->type==2&&$model->examin_status<>1)){ ?>
				<span class="emgchange" style="top:39px;  right: 42px;"><a href="/user/default/edit" target="_self">修改</a></span>
                                <?php } ?>
                    </li>
				</ul>
				<p style="float:left;font-size:12px;text-indent:21px;">会员类型：<?= $user_cat ?></p>
				<p style="float:left;font-size:12px;text-indent:24px;">入会时间：<?= date('Y-m-d',$model->in_time) ?></p>
                                
			</div>
			<link href="/css/member.css" rel="stylesheet">
			<form>
				<table cellspacing="0" cellpadding="0">
					<tr style="visibility:hidden;">
						<td class="tab_1 tab_8"> </td>
						<td class="tab_7 tab_8"> </td>
						<td class="tab_5 tab_8"> </td>
						<td class="tab_2 tab_8"> </td>
					</tr>
                                        
					<tr>
						<td class="tab_1">联系人姓名</td>               
						<td class="tab_2" colspan="3"><?= $model->real_name ?></td>   
					</tr>
                                        
                                        <?php if($model->type==1){ ?>
					<tr>
						<td class="tab_1">身份证号</td>               
						<td class="tab_2" colspan="2"><?= $model->idcard ?></td>   
						<td class="tab_6"><span class="tab_3"></span>
                                                    
                                                    <?php if($model->idcard_status){?>
                                                    <span class="col3 type-ok">已验证</span>
                                                    <?php }else{ ?>
                                                        <span class="col3 type-no"><?php if(empty($model->idcard)){ echo "未填写"; }else{ echo "未验证"; } ?></span>
                                                    <?php } ?>
                                                </td>
					</tr>
                                        <?php } ?>
                                        
					<tr>
						<td class="tab_1">联系人手机号</td>               
						<td class="tab_2"  colspan="2"><?= $model->mobile ?></td>   
						<td class="tab_6">
                                                    
                                                    <span class="tab_3"></span>
                                                    <?php if($model->mobile_status){?>
                                                    <span class="col3 type-ok">已绑定</span>
                                                    <span class="type-word col3" style="border-right:0px;"><a href="javascript:void(0);" class="userbind" op="mobile_bind" title="修改手机号" wh="430-320" >修改</a></span>
                                                    <?php }else{ ?>
                                                        <span class="col3 type-no">未验证</span>
                                                        <span class="type-word col3" style="border-right:0px;"><a href="javascript:void(0);" class="userbind" op="mobile_verify" title="立即验证" wh="430-320" >立即验证</a></span>
                                                    <?php } ?>
                                                
                                                </td>
						
					</tr>
					<tr>
						<td class="tab_1">登录密码</td>               
						<td class="tab_2" colspan="2">******</td>   
						<td class="tab_6">
							<span class="type-word col3" style="border-right:0px;"><a href="javascript:void(0);" class="userbind" op="pwd_edit" title="修改密码" wh="430-320" >修改</a></span>
						</td>
					</tr>
                                        <tr>
						<td class="tab_1">交易密码</td>               
						<td class="tab_2" colspan="2">
                                                    <?php if(!empty($model->trade_pwd)){ ?>******<?php } ?>
                                                </td>   
						<td class="tab_6">
							<span class="type-word col3" style="border-right:0px;">
                                                            <?php if(!empty($model->trade_pwd)){ ?>
                                                            <a href="javascript:void(0);" class="settradepwd" title="修改交易密码" wh="430-320" >
                                                                修改
                                                            </a>
                                                            
                                                            <?php }else{ ?>
                                                            <a href="javascript:void(0);" class="settradepwd" title="设置交易密码" wh="430-320" >
                                                                设置
                                                            </a>
                                                            <?php } ?>
                                                        </span>
						</td>
					</tr>
					<tr>
						<td class="tab_1">绑定邮箱</td>               
						<td class="tab_2"  colspan="2"><?= $model->email ?></td>  
                                                <td class="tab_6"><span class="tab_3"></span>
                                                    
                                                    <?php if($model->email_status){?>
                                                    <span class="col3 type-ok">已绑定</span>
                                                    <?php }else{ ?>
                                                        <span class="col3 type-no"><?php if(empty($model->email)){ echo "未填写"; }else{ ?>
                                                            <a href="javascript:void(0);" class="userbind" op="email" title="验证邮箱" wh="395-200" >未验证</a>
                                                            <?php } ?></span>
                                                        
                                                    <?php } ?>
                                                        
                                                        <span class="type-word col3" style="border-right:0px;"><a href="javascript:void(0);" class="userbind" op="email_edit" title="修改邮箱" wh="395-200" >修改</a></span>
                                                </td>
					</tr>
					<?php if($model->type==2){ ?>
					<tr>
						<td class="tab_1">法定代表人姓名</td>               
						<td class="tab_7"><?= $model->law_master ?></td>   
						<td class="tab_5">法定代表人身份证</td>               
						<td class="tab_2"><?= $model->law_master_idcard ?></td>  
					</tr>
					<tr>
						<td class="tab_1">办公电话</td>               
						<td class="tab_7"><?= $model->tel ?></td>  
						<td class="tab_5">公司网址</td>               
						<td class="tab_2"><?= $model->org_url ?></td>   
					</tr>
					<tr>
						<td class="tab_1">机构名称</td>               
						<td class="tab_2" colspan="3"><?= $model->org_name ?></td>   
					</tr>
					<tr>
						<td class="tab_1">营业执照编号</td>               
						<td class="tab_2" colspan="3"><?= $model->business_licence ?></td>   
					</tr>
					<tr>
						<td class="tab_1">组织机构代码</td>               
						<td class="tab_2" colspan="3"><?= $model->org_code ?></td>   
					</tr>
					<tr>
						<td class="tab_1">税务登记证号</td>               
						<td class="tab_2" colspan="3"><?= $model->shui_code ?></td>   
					</tr>
					<?php } ?>                   
				</table>
			</form>

				<!--ul class="line2">
					<li class="col1">
						实名认证
					</li>
					<li class="col2">
						<span><?= $model->real_name ?></span>
						身份证号 <?= $model->idcard ?>
					</li>
					<li class="col3 type-ok">已绑定</li>
				</ul>
				<ul class="line2">
					<li class="col1">
						绑定手机
					</li>
					<li class="col2">
						<?= $model->mobile ?>
					</li>
					<li class="col3 type-ok">已绑定</li>
					<li class="col3 type-no">未绑定</li>
					<li><a href="#">绑定</a></li>
				</ul>

				<ul class="line2">

					<li class="col1">
						登录密码
					</li>
					<li class="col2">
						为了您的账户安全，请定期更换登录密码
					</li>
					<li class="col3 type-ok">已设置</li>
					<li><a href="#" >修改</a></li>

				</ul>

				<ul class="line2">
					<li class="col1">
						邮箱
					</li>
					<li class="col2">
						<?= $model->email ?>
					</li>

				</ul>

				<ul class="line2">
					<li class="col1">
						机构名称
					</li>
					<li class="col2">
						<?= $model->org_name ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						办公电话
					</li>
					<li class="col2">
						<?= $model->tel ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						营业执照编号
					</li>
					<li class="col2">
						<?= $model->business_licence ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						组织机构代码
					</li>
					<li class="col2">
						<?= $model->org_code ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						税务登记证号
					</li>
					<li class="col2">
						<?= $model->shui_code ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						法定代表人姓名
					</li>
					<li class="col2">
						<?= $model->law_master ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						法定代表人身份证
					</li>
					<li class="col2">
						<?= $model->law_master_idcard ?>
					</li>

				</ul>
				<ul class="line2">
					<li class="col1">
						公司网址
					</li>
					<li class="col2">
						<?= $model->org_url ?>
					</li>

				</ul-->
			</div>
		</div>
	</div>
<script>
function getlay() {  //利用这个方法向子页面传递layer的index
    return laySum;
}
$(function(){
    $('.userbind').bind('click',function(){
        var op = $(this).attr('op');
        var title = $(this).attr('title');
        var wh = $(this).attr('wh').split('-');

        laySum = $.layer({
            type: 2,
            title: [
                title, 
                'background:#f7f8fa; height:40px; color:#black; border:none; font-weight:bold;' //自定义标题样式
            ], 
            border:[0],
            area: [wh[0]+'px', wh[1]+'px'],
            close: function(index){
                            layer.close(index);
                            location.reload();
            },
            iframe: {src: '/user/default/popup?op='+op}
        })
    });    
})
</script>