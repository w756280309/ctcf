<?php

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
                        <?php if($category==User::USER_TYPE_ORG){?>
                        <a href="/user/user/edit?type=2" id="sample_editable_1_new" class="btn green" style="float: right;">
                        <i class="icon-plus"></i> 添加新融资客户
                        </a>
                        <?php }?>
                </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?=$category==1?"listt":"listr"?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if($category==User::USER_TYPE_PERSONAL){?>
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
                            <a href="javascript:void(0);">会员列表</a>
                        </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/user/<?=$category==1?"listt":"listr"?>" method="get" target="_self">

                <table class="table">
                    <tbody>
                        <tr>
                            <?php if($category==User::USER_TYPE_PERSONAL){ ?>
                                <td>
                                    <span class="title">真实姓名</span>
                                </td>
                                <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= Yii::$app->request->get('name') ?>"  placeholder="真实姓名"/></td>
                                <td><span class="title">手机号</span></td>
                                <td>
                                    <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='mobile' value="<?= Yii::$app->request->get('mobile') ?>"  placeholder="手机号"/>
                                </td>
                            <?php }else{?>
                                <td>
                                    <span class="title">企业名称</span>
                                </td>
                                <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= Yii::$app->request->get('name') ?>"  placeholder="企业名称"/></td>
                            <?php }?>


                            <td><div align="right" style="margin-right: 20px">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                            </div></td>

                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->


        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>会员ID</th>
                <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <th>手机号</th>
                        <th>真实姓名</th>
                <?php }else{?>
                        <th>企业名称</th>
                 <?php }?>
                        <th>注册时间</th>
                        <th>可用余额（元）</th>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['usercode'] ?></td>
                <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <td><?= $val['mobile'] ?></td>
                        <td><?= $val['real_name']?'<a href="">'.$val['real_name'].'</a>':"---" ?></td>
                <?php }else{?>
                        <td><?= $val['org_name'] ?></td>
                 <?php }?>
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?= number_format($val->lendAccount['available_balance'],2) ?></td>
                        <td>
                        <center>
                             <?php if($category==User::USER_TYPE_PERSONAL){?>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                             <?php }else{?>
                                <a href="/user/user/edit?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                            <?php }?>
                        </center>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>

</div>

<?php $this->endBlock(); ?>

