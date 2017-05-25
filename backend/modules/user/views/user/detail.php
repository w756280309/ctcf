<?php

use common\utils\SecurityUtils;
use common\utils\StringUtils;
use yii\web\YiiAsset;

$this->title = '会员详情';

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

?>

<?php $this->beginBlock('blockmain'); ?>
<style type="text/css">
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
                    <a href="/user/user/<?= ($normalUser->isOrgUser()) ? 'listr' : 'listt' ?>">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listt">投资会员</a>
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
                    <td><strong>会员ID</strong></td>
                    <td><?= $normalUser['usercode'] ?></td>
                    <td><strong>手机号</strong></td>
                    <td><?= SecurityUtils::decrypt($normalUser['safeMobile']) ?></td>
                    <td><strong>真实姓名</strong></td>
                    <td><?= $normalUser['real_name'] ? $normalUser['real_name'] : '---' ?></td>
                </tr>
                <tr>
                    <td><strong>身份证号</strong></td>
                    <td><?= $normalUser['idcard'] ? StringUtils::obfsIdCardNo($normalUser['idcard']) : '---' ?></td>
                    <td><strong>生日</strong></td>
                    <td><?= $normalUser['idcard'] ? $normalUser->birthday : '---' ?></td>
                    <td><strong>性别</strong></td>
                    <td>
                        <?php
                        $gender = $normalUser->getGender();
                        if ($gender === 'male') {
                            echo '男性';
                        } elseif ($gender === 'female') {
                            echo '女性';
                        } else {
                            echo '---';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>分销商</strong></td>
                    <td><?= $userAff ? $userAff->affiliator->name : '官方' ?>&nbsp;&nbsp;&nbsp;<a href="javascript:openwin('/fenxiao/fenxiao/get-aff-info?uid=<?= $normalUser->id ?>' , 500, 300)">修改</a></td>
                    <td><strong>注册渠道</strong></td>
                    <td>
                        <?php
                        //todo 临时代码-应通过翻译项翻译
                        $campaignSource = trim($normalUser['campaign_source']);
                        if ('walmart1702' === $campaignSource) {
                            echo '沃尔玛';
                        } else if ('' === $campaignSource) {
                            echo '---';
                        } else {
                            echo $campaignSource;
                        }
                        ?>
                    </td>
                    <td><strong>注册时间</strong></td>
                    <td><?= date('Y-m-d H:i:s', $normalUser['created_at']) ?></td>
                </tr>
                <tr>
                    <td><strong>实名认证</strong></td>
                    <td>
                        <?php
                        if ($normalUser['idcard_status'] == '-1') {
                            echo '未通过';
                        } elseif ($normalUser['idcard_status'] == '1') {
                            echo '验证通过';
                        } else {
                            echo '未验证';
                        }
                        ?>
                    </td>
                    <td><strong>免密支付</strong></td>
                    <td><?= $normalUser['mianmiStatus'] ? '已开通' : '未开通' ?></td>
                    <td><strong>银行卡</strong></td>
                    <td><?= $normalUser->qpay ? substr_replace($normalUser->qpay->card_number, '**** **** **** ', 0, -4) : '未开通' ?></td>
                </tr>
                <tr>
                    <td><strong>充值时间</strong></td>
                    <td><?= empty($czTime) ? '---' : date('Y-m-d H:i:s', $czTime) ?></td>
                    <td><strong>最后登录时间</strong></td>
                    <td><?= empty($normalUser['last_login']) ? '---' : date('Y-m-d H:i:s', $normalUser['last_login']) ?></td>
                    <td><strong>标的最后投资时间</strong></td>
                    <td><?= empty($tzTime) ? '---' : date('Y-m-d H:i:s', $tzTime) ?></td>
                </tr>
                <tr>
                    <td><strong>转让最后投资时间</strong></td>
                    <td><?= $latestCreditOrderTime ? $latestCreditOrderTime : '---' ?></td>
                    <td><strong>本年度160天及以上项目累计投资额</strong></td>
                    <td><?= StringUtils::amountFormat2($leiji) ?>元</td>
                    <td><strong>会员积分</strong></td>
                    <td><?= StringUtils::amountFormat2($normalUser->points) ?></td>
                </tr>
                <tr>
                    <td><strong>会员等级</strong></td>
                    <td>VIP<?= $normalUser->getLevel() ?></td>
                    <td><strong>用户在兑吧ID</strong></td>
                    <td><?= $normalUser->thirdPartyConnect ? $normalUser->thirdPartyConnect->publicId : '---'?></td>
                    <td><strong>会员财富值</strong></td>
                    <td><?= $normalUser->getCoins() ?></td>
                </tr>
                <tr>
                    <td><strong>注册端</strong></td>
                    <td>
                        <?php
                        if ($normalUser['regFrom'] === 1) {
                            echo 'wap注册';
                        } elseif ($normalUser['regFrom'] === 2) {
                            echo '微信注册';
                        } elseif ($normalUser['regFrom'] === 3) {
                            echo 'app注册';
                        } elseif ($normalUser['regFrom'] === 4) {
                            echo 'pc注册';
                        } else {
                            echo '未知来源注册';
                        }
                        ?>
                    </td>
                    <td><strong>注册位置</strong></td>
                    <td><?= $normalUser['regContext'] ? $normalUser['regContext'] : '---' ?></td>
                    <td><strong>注册IP位置</strong></td>
                    <td>
                        <?= $normalUser['regLocation'] ? $normalUser['regLocation'] : '---' ?>&nbsp;&nbsp;&nbsp;
                        <a href="/user/user/update-reg-location?user_id=<?= $normalUser->id ?>">修改</a>
                    </td>
                </tr>
            </table>

            <div class="detail_font">会员资金详情</div>
            <table class="table table-condensed">
                <tr>
                    <td><span>资产总额（元）</span><?= StringUtils::amountFormat2($userAccount->getTotalFund()) ?></td>
                    <td><span>理财资产（元）</span><?= StringUtils::amountFormat2($userAccount->investment_balance) ?></td>
                    <td><span>冻结资金（元）</span><?= StringUtils::amountFormat2($userAccount->freeze_balance) ?></td>
                    <td><span>可用余额（元）</span><?= StringUtils::amountFormat2($userAccount->available_balance) ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><span>充值次数（次）</span><?= $czNum ?></td>
                    <td><span>提现次数（次）</span><?= $txNum ?></td>
                    <td><span>转让次数（次）</span><?= $transferCount ?></td>
                    <td><span>标的投资次数（次）</span><?= $tzNum ?></td>
                    <td><span>转让投资次数（次）</span><?= $creditSuccessCount ?></td>
                </tr>
                <tr>
                    <td><span>充值总计（元）</span><?= empty($czMoneyTotal) ? '0.00' : $czMoneyTotal ?></td>
                    <td><span>提现总计（元）</span><?= empty($txMoneyTotal) ? '0.00' : $txMoneyTotal ?></td>
                    <td><span>转让总计（元）</span><?= StringUtils::amountFormat2($transferSum) ?></td>
                    <td><span>标的投资总计（元）</span><?= empty($tzMoneyTotal) ? '0.00' : $tzMoneyTotal ?></td>
                    <td><span>转让投资总计（元）</span><?= empty($creditTotalAmount) ? '0.00' : $creditTotalAmount ?></td>
                </tr>
            </table>
        </div>

        <div>
            <div>
                <ul class="nav nav-tabs nav-pills" role="tablist" id="list_nav">
                    <li role="presentation" class="money_record_nav active"><a>资金流水</a></li>
                    <li role="presentation" class="recharge_nav"><a>充值流水</a></li>
                    <li role="presentation" class="draw_nav"><a>提现流水</a></li>
                    <li role="presentation" class="loan_order_nav"><a>标的投资</a></li>
                    <li role="presentation" class="credit_order_nav"><a>转让买入</a></li>
                    <li role="presentation" class="credit_note_nav"><a>转让卖出</a></li>
                    <li role="presentation" class="bind_nav"><a>绑卡</a></li>
                    <li role="presentation" class="coupon_nav"><a>代金券</a></li>
                    <li role="presentation" class="points"><a>积分</a></li>
                    <li role="presentation" class="coins"><a>财富值</a></li>
                    <li role="presentation" class="invite_nav"><a>关系详情</a></li>
                    <li role="presentation" class="offline_nav"><a>线下会员</a></li>
                </ul>
            </div>
            <div class="container-fluid"  id="list">
                <div class="list_detail" id="money_record_list"></div>
                <div class="list_detail" id="recharge_list"></div>
                <div class="list_detail" id="draw_list"></div>
                <div class="list_detail" id="loan_order_list"></div>
                <div class="list_detail" id="credit_order_list"></div>
                <div class="list_detail" id="credit_note_list"></div>
                <div class="list_detail" id="bind_card_list"></div>
                <div class="list_detail" id="coupon_list"></div>
                <div class="list_detail" id="point_list"></div>
                <div class="list_detail" id="coin_list"></div>
                <div class="list_detail" id="invite_list"></div>
                <div class="list_detail" id="offline_user"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function getMoneyRecord(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#money_record_list').html(data);
            }
        })
    }

    function getBindList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#bind_card_list').html(data);
            }
        })
    }

    function getCouponList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#coupon_list').html(data);
            }
        })
    }

    function getInviteList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#invite_list').html(data);
            }
        })
    }

    function getOfflineUser(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#offline_user').html(data);
                $('#offline_user').html(data);
            }
        })
    }

    function getRechargeList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#recharge_list').html(data);
            }
        })
    }

    function getDrawList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#draw_list').html(data);
            }
        })
    }

    function getLoanOrderList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#loan_order_list').html(data);
            }
        })
    }

    function getCreditOrderList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#credit_order_list').html(data);
            }
        })
    }

    function getCreditNoteList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#credit_note_list').html(data);
            }
        })
    }

    function getPointList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#point_list').html(data);
            }
        })
    }

    function getCoinList(href)
    {
        $.get(href, function(data) {
            if (data) {
                $('#coin_list').html(data);
            }
        })
    }

    getMoneyRecord('/user/user/detail?id=<?= $normalUser->id?>&key=money_record');

    $('#list_nav li').click(function () {
        var index = $("#list_nav li").index(this);
        if(!$(this).hasClass('active')) {
            $('#list_nav li').removeClass('active');
            $(this).addClass('active');
            $('#list .list_detail').hide();
            $('#list .list_detail').eq(index).show();
        }
    });

    $('.points').click(function () {
        if (!$('#point_list').html()) {
            getPointList('/user/user/point-list?userId=<?= $normalUser->id ?>');
        }
    });

    $('.coins').click(function () {
        if (!$('#coin_list').html()) {
            getCoinList('/user/user/coin-list?userId=<?= $normalUser->id ?>');
        }
    });

    $('.bind_nav').click(function () {
        if (!$('#bind_card_list').html()) {
            getBindList('/user/bank-card/list?uid=<?= $normalUser->id?>');
        }
    });

    $('.coupon_nav').click(function(){
        if (!$('#coupon_list').html()) {
            getCouponList('/coupon/coupon/list-for-user?uid=<?= $normalUser->id?>')
        }
    });

    $('.invite_nav').click(function(){
        if (!$('#invite_list').html()) {
            getInviteList('/user/user/detail?id=<?= $normalUser->id?>&key=invite_record')
        }
    });

    $('.offline_nav').click(function(){
        if (!$('#offline_user').html()) {
            getOfflineUser('/user/user/detail?id=<?= $normalUser->id?>&key=offline_user')
        }
    });

    $('.recharge_nav').click(function(){
        if (!$('#recharge_list').html()) {
            getRechargeList('/user/user/detail?id=<?= $normalUser->id?>&key=recharge_record')
        }
    });

    $('.draw_nav').click(function(){
        if (!$('#draw_list').html()) {
            getDrawList('/user/user/detail?id=<?= $normalUser->id?>&key=draw_record')
        }
    });

    $('.loan_order_nav').click(function(){
        if (!$('#loan_order_list').html()) {
            getLoanOrderList('/order/onlineorder/detailt?id=<?= $normalUser->id?>')
        }
    });

    $('.credit_order_nav').click(function(){
        if (!$('#credit_order_list').html()) {
            getCreditOrderList('/user/user/credit-records?id=<?= $normalUser->id?>')
        }
    });

    $('.credit_note_nav').click(function(){
        if (!$('#credit_note_list').html()) {
            getCreditNoteList('/user/user/detail?id=<?= $normalUser->id?>&key=credit_note')
        }
    });
</script>
<?php $this->endBlock(); ?>
