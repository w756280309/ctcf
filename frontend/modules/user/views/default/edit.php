<?php
use yii\widgets\ActiveForm;

$this->title = "编辑用户信息";
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
				<ul style="display:block;width:700px;height:67px;">
					<li style="font-size:24px;height:35px;"><?= $model->username ?></li>
					<li>编号：<?= $model->usercode ?> </li>
				</ul>
				<p style="float:left;font-size:12px;text-indent:21px;">会员类型：<?= $user_cat ?></p>
				<p style="float:left;font-size:12px;text-indent:24px;">入会时间：<?= date('Y-m-d',$model->in_time) ?></p>
				
			</div>
			<link href="/css/member.css" rel="stylesheet">
			<?php $form = ActiveForm::begin(['id'=>'member_form', 'action' => "/user/default/edit"]); ?>
				<table cellspacing="0" cellpadding="0">
					<tr style="visibility:hidden;">
						<td class="tab_1 tab_8"> </td>
						<td class="tab_7 tab_8"> </td>
						<td class="tab_5 tab_8"> </td>
						<td class="tab_2 tab_8"> </td>
					</tr>
                                        <tr>
						<td class="tab_1">会员分类</td>               
						<td class="tab_2" colspan="3">
                                                    <?php if($model->type==1){ ?>
                                                        <?=$user_cat ?>
                                                    <?php }else{ ?>
                                                        <?php if(empty($user_cat)){ ?>
                                                            <?= $form->field($model, 'cat_id',
                                                            [
                                                            'template' => '{input}'
                                                            ])->dropDownList(array('0'=>"--请选择--")+$category);?>
                                                            <?= $form->field($model, 'cat_id', ['template' => '{error}']); ?>
                                                        <?php }else{ ?>
                                                            <?=$user_cat ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>   
					</tr>
					<tr>
						<td class="tab_1">联系人姓名</td>               
						<td class="tab_2" colspan="3">
                                                    <?php if($model->idcard_status){?>
                                                        <?= $model->real_name ?>
                                                    <?php }else{ ?>
                                                        <?= $form->field($model, 'real_name', ['template' => '{input}'])->textInput(); ?>
                                                        <?= $form->field($model, 'real_name', ['template' => '{error}']); ?>
                                                    <?php } ?>
                                                    
                                                </td>   
					</tr>
                                        <?php if($model->type==1){ ?>
					<tr>
						<td class="tab_1">身份证号</td>               
						<td class="tab_2" colspan="2">
                                                    <?php if($model->idcard_status){?>
                                                        <?= $model->idcard ?>
                                                    <?php }else{ ?>
                                                        <?= $form->field($model, 'idcard', ['template' => '{input}'])->textInput(); ?>
                                                        <?= $form->field($model, 'idcard', ['template' => '{error}']); ?>
                                                    <?php } ?>
                                                </td>   
						<td class="tab_6"><span class="tab_3"></span>
                                                    
                                                    <?php if($model->idcard_status){?>
                                                    <span class="col3 type-ok">已绑定</span>
                                                    <?php }else{ ?>
                                                        
                                                    <?php } ?>
                                                </td>
					</tr>
                                        <?php } ?>
					<tr>
						<td class="tab_1">联系人手机号</td>               
						<td class="tab_2"  colspan="2"><?= $model->mobile ?></td>   
						<td class="tab_6">
                                                    <span class="tab_3"></span><span class="col3 type-ok">已绑定</span>
						</td>
					</tr>
					<tr>
						<td class="tab_1">登录密码</td>               
						<td class="tab_2" colspan="2">******</td>   
						<td class="tab_6">
							
						</td>
					</tr>
					<tr>
						<td class="tab_1">绑定邮箱</td>               
						<td class="tab_2"  colspan="2">
                                                    <?php if($model->email_status){?>
                                                        <?=$model->email?>
                                                    <?php }else{ ?>
                                                        <?= $form->field($model, 'email', ['template' => '{input}'])->textInput(); ?>
                                                        <?= $form->field($model, 'email', ['template' => '{error}']); ?>
                                                    <?php } ?>
                                                        
                                                </td>  
                                                <td class="tab_6"><span class="tab_3"></span>
                                                    
                                                    <?php if($model->email_status){?>
                                                    <span class="col3 type-ok">已绑定</span>
                                                    <?php }else{ ?>
                                                        
                                                    <?php } ?>
                                                </td>
					</tr>
					<?php if($model->type==2){ ?>
					<tr>
						<td class="tab_1">法定代表人姓名</td>               
						<td class="tab_7">
                                                    <?= $form->field($model, 'law_master', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'law_master', ['template' => '{error}']); ?>
                                                <?php $model->law_master; ?>
                                                </td>   
						<td class="tab_5">法定代表人身份证</td>               
						<td class="tab_2"><?php $model->law_master_idcard ?>
                                                    <?= $form->field($model, 'law_master_idcard', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'law_master_idcard', ['template' => '{error}']); ?>
                                                </td>  
					</tr>
					<tr>
						<td class="tab_1">办公电话</td>               
						<td class="tab_7"><?php $model->tel ?>
                                                    <?= $form->field($model, 'tel', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'tel', ['template' => '{error}']); ?>
                                                </td>  
						<td class="tab_5">公司网址</td>               
						<td class="tab_2"><?php $model->org_url ?>
                                                    <?= $form->field($model, 'org_url', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'org_url', ['template' => '{error}']); ?>
                                                </td>   
					</tr>
					<tr>
						<td class="tab_1">机构名称</td>               
						<td class="tab_2" colspan="3"><?php $model->org_name ?>
                                                    <?= $form->field($model, 'org_name', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'org_name', ['template' => '{error}']); ?>
                                                </td>   
					</tr>
					<tr>
						<td class="tab_1">营业执照编号</td>               
						<td class="tab_2" colspan="3"><?php $model->business_licence ?>
                                                    <?= $form->field($model, 'business_licence', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'business_licence', ['template' => '{error}']); ?>
                                                </td>   
					</tr>
					<tr>
						<td class="tab_1">组织机构代码</td>               
						<td class="tab_2" colspan="3"><?php $model->org_code ?>
                                                    <?= $form->field($model, 'org_code', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'org_code', ['template' => '{error}']); ?>
                                                </td>   
					</tr>
					<tr>
						<td class="tab_1">税务登记证号</td>               
						<td class="tab_2" colspan="3"><?php $model->shui_code ?>
                                                    <?= $form->field($model, 'shui_code', ['template' => '{input}'])->textInput(); ?>
                                                    <?= $form->field($model, 'shui_code', ['template' => '{error}']); ?>
                                                </td>   
					</tr>
					<?php } ?>    
                                        <tr>
                                            <td colspan="4">
                                                
                                                <span class="emgchange"><input type="submit" class="button" value="提交保存"/></span>
                                            </td> 
                                        </tr>
				</table>
			<?php $form->end(); ?>

				
			</div>
		</div>
	</div>
