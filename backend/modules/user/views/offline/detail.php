<?php

use common\utils\StringUtils;

$this->title = '线下会员详情';

?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .detail_font {
        width: 200px;
        margin-bottom: 15px;
        font-family: 微软雅黑;
        font-weight: bold;
        font-size: 15px;
        color: blue;
    }
    #list_nav li {
        cursor: pointer;
    }
</style>

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
                    <a href="/user/offline/list">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/offline/list">线下会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)">会员详情</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <div class="detail_font">会员账户详情</div>
            <table class="table table-condensed">
                <tr>
                    <td><strong>手机号</strong></td>
                    <td><?= $user->mobile ?></td>
                    <td><strong>真实姓名</strong></td>
                    <td><?= $user->realName ?></td>
                    <td><strong>身份证号</strong></td>
                    <td><?= StringUtils::obfsIdCardNo($user->idCard) ?></td>
                </tr>
                <tr>
                    <td><strong>积分</strong></td>
                    <td><?= StringUtils::amountFormat2($user->points) ?></td>
                    <td><strong>会员等级</strong></td>
                    <td>VIP<?= $user->getLevel() ?></td>
                    <td><strong>会员财富值</strong></td>
                    <td><?= $user->getCoins() ?></td>
                </tr>
            </table>
        </div>
        <div class="detail_font">会员资金详情</div>
        <table class="table table-condensed">
            <tr>
                <td><span>理财资产（万元）</span>  <?= StringUtils::amountFormat2($user->investment_balance) ?></td>
                <td></td>
            </tr>

        </table>
        <div>
            <div>
                <ul class="nav nav-tabs nav-pills" role="tablist" id="list_nav">
                    <li role="presentation" class="point_record_nav active"><a href="javascript:getLoanOrderList('/user/offline/orders?id=<?= $user->id?>')">标的投资明细</a></li>
                    <li role="presentation" class="point_record_nav"><a href="javascript:getPointList('/user/offline/points?id=<?= $user->id?>')">积分明细</a></li>
                    <li role="presentation" class="point_record_nav"><a href="javascript:getCoinList('/user/user/coin-list?userId=<?= $user->id ?>&isOffline=1')">财富值明细</a></li>
                    <li role="presentation" class="point_record_nav">
                        <a href="javascript:getOnlineList('/user/offline/online-user?id=<?= $user->id ?>')">线上会员</a>
                    </li>
                </ul>
            </div>
            <div class="container-fluid"  id="list">
                <div class="list_detail" id="list_detail"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function getLoanOrderList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#list_detail').html(data);
            }
        })
    }

    function getPointList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#list_detail').html(data);
            }
        })
    }

    function getCoinList(href)
    {
        $.get(href, function (data) {
            if (data) {
                $('#list_detail').html(data);
            }
        })
    }

    function getOnlineList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#list_detail').html(data);
            }
        })
    }

    $('#list_nav li').click(function () {
        var index = $("#list_nav li").index(this);
        if(!$(this).hasClass('active')) {
            $('#list_nav li').removeClass('active');
            $(this).addClass('active');
        }
    });
    //默认第一个显示
    getLoanOrderList('/user/offline/orders?id=<?= $user->id?>');
</script>
<?php $this->endBlock(); ?>