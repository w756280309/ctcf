<?php

use common\utils\SecurityUtils;
use common\utils\StringUtils;

$this->title = '融资会员详情';
$this->loadAuthJs = false;

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
                    <a href="/user/user/<?= ($orgUser->isOrgUser()) ? 'listr' : 'listt' ?>">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listr">融资会员</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)">会员详情</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <div class="detail_font">会员账户详情</div>
            <ul class="breadcrumb_detail">
                <li><span>会员ID</span><?=$orgUser['usercode']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li style="width: 100%;"><span>企业名称</span><?=$orgUser['org_name']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>注册时间</span><?=date("Y-m-d H:i:s",$orgUser['created_at'])?></li>
                <li><span>平台首次融资时间</span><?php echo empty($czTime)?"--":date("Y-m-d H:i:s",$czTime);?></li>
                <li><span>办公电话</span><?=$orgUser['tel']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业法人</span></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>姓名</span><?=$orgUser['law_master']?></li>
                <li><span>身份证号</span><?= StringUtils::obfsIdCardNo($orgUser['law_master_idcard']) ?></li>
                <li><span>联系电话</span><?=$orgUser['law_mobile']?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业联系人</span></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>姓名</span><?=$orgUser['real_name']?></li>
                <li><span>身份证号</span><?= StringUtils::obfsIdCardNo($orgUser['idcard']) ?></li>
                <li><span>联系电话</span><?= SecurityUtils::decrypt($orgUser['safeMobile']) ?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span class="huibai">企业证照</span></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>组织机构代码</span><?=$orgUser['org_code']?></li>
                <li><span>营业执照</span><?=$orgUser['business_licence']?></li>
                <li><span>税务登记号</span><?=$orgUser['shui_code']?></li>
            </ul>
            <hr />

            <div class="detail_font">会员资金详情</div>
            <ul class="breadcrumb_detail">
                <li><span>账户余额（元）</span><?= StringUtils::amountFormat3($userYuE) ?></li>
                <li><span>已还款金额（元）</span><?= StringUtils::amountFormat3($ret['yihuan']) ?></li>
                <li><span>待还款金额（元）</span><?= StringUtils::amountFormat3($ret['wait']) ?></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>联动商户号：</span><?= $epayUserID ?></li>
                <li id="ump"><span>联动账户余额（元）</span><label></label></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>充值次数（次）</span><?= $czNum ?></li>
                <li><span>充值总计（元）</span><?= StringUtils::amountFormat3($czMoneyTotal) ?></li>
                <li><span>充值流水明细</span><a href="/user/rechargerecord/detail?id=<?= $orgUser->id ?>&type=<?= $orgUser->type ?>">查看</a></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>提现次数（次）</span><?= $txNum ?></li>
                <li><span>提现总计（元）</span><?= StringUtils::amountFormat3($txMoneyTotal) ?></li>
                <li><span>提现流水明细</span><a href="/user/drawrecord/detail?id=<?= $orgUser->id ?>&type=<?= $orgUser->type ?>">查看</a></li>
            </ul>
            <ul class="breadcrumb_detail">
                <li><span>融资成功次数（次）</span><?= $rzNum ?></li>
                <li><span>融资成功总计（元）</span><?= StringUtils::amountFormat3($rzMoneyTotal) ?></li>
                <li><span>融资明细</span><a href="/order/onlineorder/detailr?id=<?= $orgUser->id ?>&type=<?= $orgUser->type ?>">查看</a></li>
            </ul>
            <hr />
        </div>
    </div>
</div>

<style type="text/css">
    .breadcrumb_detail{font-size:14px;padding:8px 15px;margin:0 5 20px;list-style:none;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
    .breadcrumb_detail>li{width: 315px;display:inline-block;*display:inline;text-shadow:0 1px 0 #fff;*zoom:1}
    .breadcrumb_detail>li>span{width: 130px;font-weight: bold;margin:0 20px;display:inline-block;*display:inline;text-shadow:0 1px 0 #fff;*zoom:1}
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
    $(function () {
        var orgUserId = '<?= $orgUser->id ?>';
        var xhr = $.get('/user/user/ump-org-account?id='+orgUserId, function (data) {
            if ('undefined' !== data.balance) {
                $('#ump label').html(data.balance);
            }
        });

        xhr.fail(function () {
            $('#ump label').html('无权限查看此项数据');
        });
    })
</script>
<?php $this->endBlock(); ?>