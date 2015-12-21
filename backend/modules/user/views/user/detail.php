<?php

    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    use common\models\user\User;
 
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员管理模块【主要包含投资会员和融资会员的管理】</small>
                </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?=Yii::$app->request->get('type')==2?'listr':'listt'?>">会员管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if($type=Yii::$app->request->get('type')==User::USER_TYPE_PERSONAL){?>
                        <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }else{?>
                        <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }?>
                        <li>
                            <a href="javascript:void(0)">会员列表</a>
                        </li>
            </ul>
        </div>
        
    <?php if(Yii::$app->request->get('type')==User::USER_TYPE_PERSONAL){?>
        <div class="portlet-body">
            <div class="detail_font">会员账户详情</div>
            <ul class="breadcrumb_detail">
                <li><span>会员ID</span><?=$userinfo['usercode']?></li>
                <li><span>真实姓名</span><?=$userinfo['real_name']?></li>
                <li><span>手机号</span><?=$userinfo['mobile']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>注册时间</span><?=date("Y-m-d H:i:s",$userinfo['created_at'])?></li>
                <li><span>充值时间</span><?php echo empty($czTime)?"--":date("Y-m-d H:i:s",$czTime);?></li>
                <li><span>投资时间</span><?php echo empty($tzTime)?"--":date("Y-m-d H:i:s",$tzTime);?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>身份证号</span><?=$userinfo['idcard']?></li>
                <li><span>实名认证</span><?php 
//                      echo $userinfo['idcard_status'];
                    if($userinfo['idcard_status']=='-1'){
                            echo "未通过";
                        }else if ($userinfo['idcard_status']=='1') {
                            echo "验证通过";
                        }else{
                            echo "未验证";
                        }
                ?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>注册渠道</span>
                    <?php
                            if($userinfo['login_from']==0){
                                echo "官网注册";
                            }elseif($userinfo['login_from']==1){
                                echo "wap注册";
                            }else{
                                echo "app注册";
                            }
                    ?>
                    </li>
                    <li><span>最后登录时间</span><?php echo empty($userinfo['last_login'])?"--":date("Y-m-d H:i:s",$userinfo['last_login']);?></li>
            </ul>
            <hr />
            
            <div class="detail_font">会员资金详情</div>
            <ul class="breadcrumb_detail">
                <li><span>理财资产（元）</span><?=$userLiCai?></li>
                <li><span>账户余额（元）</span><?php echo empty($userYuE)?'0.00':$userYuE?></li>
            </ul>
            <ul class="breadcrumb_detail">                
                <li><span>充值次数（次）</span><?=$czNum?></li>
                <li><span>充值总计（元）</span><?php echo empty($czMoneyTotal)?'0.00':$czMoneyTotal?></li>
                <li><span>充值流水明细</span><a href="/user/rechargerecord/detail?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type');?>">查看</a>&nbsp;</li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>提现次数（次）</span><?=$txNum?></li>
                <li><span>提现总计（元）</span><?php echo empty($txMoneyTotal)?'0.00':$txMoneyTotal?></li>
                <li><span>提现流水明细</span><a href="/user/drawrecord/detail?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type');?>">查看</a>&nbsp;</li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>投资次数（次）</span><?=$tzNum?></li>
                <li><span>投资总计（元）</span><?php echo empty($tzMoneyTotal)?'0.00':$tzMoneyTotal?></li>
                <li><span>投资流水明细</span><a href="/order/onlineorder/detailt?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type')?>">查看</li>
            </ul>
            <hr />
        </div>
    <?php }else{?>
        
        <div class="portlet-body">
            <div class="detail_font">会员账户详情</div>
            <ul class="breadcrumb_detail">
                <li><span>会员ID</span><?=$userinfo['usercode']?></li>
                <li><span>企业名称</span><?=$userinfo['org_name']?></li>
                <li><span>办公电话</span><?=$userinfo['tel']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>注册时间</span><?=date("Y-m-d H:i:s",$userinfo['created_at'])?></li>
                <li><span>平台首次融资时间</span><?php echo empty($czTime)?"--":date("Y-m-d H:i:s",$czTime);?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业法人</span></li>                
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>姓名</span><?=$userinfo['law_master']?></li>
                <li><span>身份证号</span><?=$userinfo['law_master_idcard']?></li>
                <li><span>联系电话</span><?=$userinfo['law_mobile']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业联系人</span></li>                
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>姓名</span><?=$userinfo['real_name']?></li>
                <li><span>身份证号</span><?=$userinfo['idcard']?></li>
                <li><span>联系电话</span><?=$userinfo['mobile']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业证照</span></li>                
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>组织机构代码</span><?=$userinfo['org_code']?></li>
                <li><span>营业执照</span><?=$userinfo['business_licence']?></li>
                <li><span>税务登记号</span><?=$userinfo['shui_code']?></li>
            </ul>
            <hr />
            
            <div class="detail_font">会员资金详情</div>
            <ul class="breadcrumb_detail">
                <li><span>已还款金额（元）</span><?=$ret['yihuan']?></li>
                <li><span>待还款金额（元）</span><?=$ret['wait']?></li>
                <li><span>账户余额（元）</span><?=$userYuE?></li>
            </ul>
            <ul class="breadcrumb_detail">                
                <li><span>充值次数（次）</span><?=$czNum?></li>
                <li><span>充值总计（元）</span><?=$czMoneyTotal ?></li>
                <li><span>充值流水明细</span><a href="/user/rechargerecord/detail?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type');?>">查看</a>&nbsp;<a href="/user/rechargerecord/edit?id=<?=Yii::$app->request->get('id')?>&type=<?=Yii::$app->request->get('type')?>">数据录入</a></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>提现次数（次）</span><?=$txNum?></li>
                <li><span>提现总计（元）</span><?= $txMoneyTotal?></li>
                <li><span>提现流水明细</span><a href="/user/drawrecord/detail?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type');?>">查看</a>&nbsp;<a href="/user/drawrecord/edit?id=<?=Yii::$app->request->get('id')?>&type=<?=Yii::$app->request->get('type')?>">数据录入</a></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>融资成功次数（次）</span><?=$rzNum?></li>
                <li><span>融资成功总计（元）</span><?php echo $rzMoneyTotal ?></li>
                <li><span>融资明细</span><a href="/order/onlineorder/detailr?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type')?>">查看</a></li>
            </ul>
            <hr />
        </div>
        
    <?php }?>
    </div>
                                    
</div>
<style type="text/css">
    .breadcrumb_detail{font-size:14px;padding:8px 15px;margin:0 5 20px;list-style:none;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
    .breadcrumb_detail>li{width: 300px;display:inline-block;*display:inline;text-shadow:0 1px 0 #fff;*zoom:1}
    .breadcrumb_detail>li>span{width: 120px;font-weight: bold;margin:0 20px;display:inline-block;*display:inline;text-shadow:0 1px 0 #fff;*zoom:1}
    .detail_font{
        width: 200px;
        margin-left:40px;
        margin-bottom: 15px;
        font-family: 微软雅黑;
        font-weight: bold;
        font-size: 15px;
        color: blue;
    }
    .huibai{
        color: grey;
        font-size: 16px;
    }
</style>

<script type="text/javascript">
    $(function(){
        
    })
</script> 
<?php $this->endBlock(); ?>

