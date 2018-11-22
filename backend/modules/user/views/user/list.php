<?php

use common\models\user\User;
use common\utils\StringUtils;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$isPersonal = $category === User::USER_TYPE_PERSONAL;
$accounts = Yii::$app->params['borrowerSubtype'];
?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .search_form td input {
        margin: 0px;
    }
</style>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员管理模块【主要包含投资会员和融资会员的管理】</small>
                <?php if (!$isPersonal) { ?>
                    <a href="/user/user/add" id="sample_editable_1_new" class="btn green" style="float: right;">
                        <i class="icon-plus"></i> 添加新融资客户
                    </a>
                <?php } ?>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/<?= $isPersonal ? 'listt' : 'listr' ?>">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <?php if ($isPersonal) { ?>
                    <li>
                        <a href="/user/user/listt">投资会员</a>
                        <i class="icon-angle-right"></i>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="/user/user/listr">融资会员</a>
                        <i class="icon-angle-right"></i>
                    </li>
                <?php } ?>
                <li>
                    <a href="javascript:void(0);">会员列表<?= !$isPersonal ? '(共计' . $pages->totalCount . '条)' : null ?></a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/user/<?= $isPersonal ? 'listt' : 'listr' ?>" method="get" target="_self">
                <input type="hidden" name="search" value="user_list">
                <table class="table search_form">
                    <tbody>
                        <?php if ($isPersonal) { ?>
                            <tr>
                                <td>
                                    <span class="title">真实姓名</span>
                                    <input type="text" name='name' value="<?= Html::encode($request['name']) ?>" placeholder="真实姓名" class="m-wrap span4">
                                </td>
                                <td>
                                    <span class="title">手机账号</span>
                                    <input type="text" name='mobile' value="<?= Html::encode($request['mobile']) ?>" placeholder="手机号" class="m-wrap span8">
                                </td>
                                <td>
                                    <span class="title">未投时长</span>
                                    <input type="text" class="m-wrap span4"  name='noInvestDaysMin' value="<?= Html::encode($request['noInvestDaysMin']) ?>">
                                    <input type="text" class="m-wrap span4"  name='noInvestDaysMax' value="<?= Html::encode($request['noInvestDaysMax']) ?>">
                                </td>
                                <td>
                                    <span class="title">注册来源</span>
                                    <?php $regContext = Html::encode($request['regContext']) ?>
                                    <select name="regContext" class="m-wrap span6">
                                        <option value="">全部</option>
                                        <option <?= $regContext === 'm' ? 'selected' : '' ?> value="m">wap注册</option>
                                        <option <?= $regContext === 'm_intro1611' ? 'selected' : '' ?> value="m_intro1611">wap落地页注册</option>
                                        <option <?= $regContext === 'pc' ? 'selected' : '' ?> value="pc">pc注册</option>
                                        <option <?= $regContext === 'pc_landing' ? 'selected' : '' ?> value="pc_landing">pc落地页注册</option>
                                        <option <?= $regContext === 'promo' ? 'selected' : '' ?> value="promo">活动落地页注册</option>
                                        <option <?= $regContext === 'other' ? 'selected' : '' ?> value="other">未知</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">投资次数</span>
                                    <input type="text" name='investCountMin' value="<?= Html::encode($request['investCountMin']) ?>" class="m-wrap span4">
                                    <input type="text" name='investCountMax' value="<?= Html::encode($request['investCountMax']) ?>" class="m-wrap span4">
                                </td>
                                <td>
                                    <span class="title">投资总额</span>
                                    <input type="text" name='investTotalMin' value="<?= Html::encode($request['investTotalMin']) ?>" class="m-wrap span4">
                                    <input type="text" name='investTotalMax' value="<?= Html::encode($request['investTotalMax']) ?>" class="m-wrap span4">
                                </td>
                                <td>
                                    <span class="title">可用余额</span>
                                    <input type="text"  class="m-wrap span4" name="balanceMin" value="<?= Html::encode($request['balanceMin']) ?>">
                                    <input type="text"  class="m-wrap span4" name="balanceMax" value="<?= Html::encode($request['balanceMax']) ?>">
                                </td>
                                <td>
                                    <span class="title">注册时间</span>
                                    <input type="text" class="m-wrap span4"  name='regTimeMin' value="<?= Html::encode($request['regTimeMin']) ?>"  onclick="WdatePicker()">
                                    <input type="text" class="m-wrap span4"  name='regTimeMax' value="<?= Html::encode($request['regTimeMax']) ?>"  onclick="WdatePicker()">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">已用代金券</span>
                                    <input type="text" class="m-wrap span4"  name='couponAmountMin' value="<?= Html::encode($request['couponAmountMin']) ?>">
                                    <input type="text" class="m-wrap span4"  name='couponAmountMax' value="<?= Html::encode($request['couponAmountMax']) ?>">
                                </td>
                                <td colspan="2">
                                    <span class="title">兑吧用户ID</span>
                                    <input type="text" class="m-wrap span8"  name='publicUserId' value="<?= Html::encode($request['publicUserId']) ?>">
                                </td>
                                <td>
                                    <span class="title">分销商名称</span>
                                    <select name="affiliator_name" class="m-wrap span6">
                                        <option value="">全部</option>
                                        <?php foreach ($affList as $record) { ?>
                                            <option <?= $request['affiliator_name'] === $record->name ? 'selected' : '' ?> value="<?= $record->name ?>"><?= $record->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">被邀请人</span>
                                    <input type="checkbox" name="isAffiliator" value="1" <?php if($request['isAffiliator']) { ?>checked="checked"<?php } ?>>
                                </td>
                                <td colspan="2">
                                    <span class="title">投资时间</span>
                                    <input type="text" class="m-wrap span4"  name='investTimeMin' value="<?= Html::encode($request['investTimeMin']) ?>"  onclick="WdatePicker()">
                                    <input type="text" class="m-wrap span4"  name='investTimeMax' value="<?= Html::encode($request['investTimeMax']) ?>"  onclick="WdatePicker()">
                                </td>
                                <td>
                                    <span class="title">会员ID</span>
                                    <input type="text" class="m-wrap span6" name="usercode" value="<?= Html::encode($request['usercode']) ?>" placeholder="会员ID">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">账户状态 </span>
                                    <?php $status =  null === $request['status'] ? (string)USER::NORMAL_STATUS : Html::encode($request['status']); ?>
                                    <select name="status" class="m-wrap span6">
                                        <option <?= '' === $status  ? 'selected' : '' ?> value="">全部</option>
                                        <option <?= '1' === $status ? 'selected' : '' ?> value="1">正常</option>
                                        <option <?= '0' === $status ? 'selected' : '' ?> value="0">禁用</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div align="left" class="search-btn">
                                        <button type='submit' class="btn blue btn-block button-search">搜索
                                            <i class="m-icon-swapright m-icon-white"></i></button>
                                    </div>
                                </td>
                                <?php if (Html::encode($request['search'])) { ?>
                                    <td colspan="3">
                                        <div align="right" class="search-btn">
                                            <a class="btn green btn-block" style="width: 140px;" href="/user/user/lenderstats?<?= http_build_query($request)?>">导出筛选用户信息</a>
                                        </div>
                                    </td>
                                <?php } else { ?>
                                    <td colspan="3">
                                        <div align="right" class="search-btn">
                                            <a class="btn green btn-block" style="width: 140px;" href="/user/user/export">导出全部用户信息</a>
                                        </div>
                                    </td>
                               <?php } ?>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td>
                                    <span class="title">企业名称</span>
                                    <input id="orgName" type="text" class="m-wrap span6" name='name' value="<?= Html::encode($request['name']) ?>" placeholder="企业名称">
                                </td>
                                <td>
                                    <span class="title">账户类型</span>
                                    <select name="accountType" class="m-wrap span6">
                                        <option value="">全部</option>
                                        <?php foreach ($accounts as $key => $account) :?>
                                            <option <?php echo  Html::encode($request['accountType']) == $key ? 'selected' : ''; ?> value="<?php echo $key;?>"><?php echo $account;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                                <td>
                                    <div align="right" class="search-btn">
                                        <button id="orgSearch" type='submit' class="btn blue btn-block button-search">搜索
                                            <i class="m-icon-swapright m-icon-white"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div align="left" class="search-btn">
                                        <a id="org_user_info_export" class="btn green btn-block" style="width: 140px;" href="javascript:void(0);">导出融资会员信息</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->
        <!--会员列表-->
        <?= $this->renderFile('@backend/modules/user/views/user/_online_user.php', ['model' => $model, 'category' => $category, 'affiliators' => $affiliators, 'hideOrgList' => $hideOrgList]); ?>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>

<script>
    $('.menuBtn').click(function(){
        var dis = $('.dropDownMenu').css('display');
        if (dis == 'block'){
            $('.dropDownMenu').slideUp();
        } else {
            $('.dropDownMenu').slideDown();
        }
    });
    $('body').mouseup(function(e) {
        var _con = $('.dropDownBox');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            var dis = $('.dropDownMenu').css('display');
            if (dis == 'block'){
                $('.dropDownMenu').slideUp();
            }
        }
    });
    $('#org_user_info_export').click(function() {
        var objs = $("input[name='choose[]']").parent();
        var ids = new Array();
        var ckidkey = 0;
        for (var i = 0; i < objs.length; i++) {
            if ($(objs[i]).hasClass('checked')) {
                ids[ckidkey] = $($("input[name='choose[]']").get(i)).val();
                ckidkey++;
            }
        }
        $(this).attr('href','/user/user/org-user-info-export?ids='+ids.join(',')).trigger(click);
    });
    $('#orgSearch').on('click',function(event){
        var event = event || window.event;
        event.preventDefault();
        if($('#orgName').val().trim()){
            $('form').submit();
        } else {
            alert('请输入企业名称');
        }
    })
</script>
<?php $this->endBlock(); ?>

