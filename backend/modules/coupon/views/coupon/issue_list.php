<?php

use common\utils\StringUtils;
use yii\helpers\Html;

$this->title = '发放代金券';

$user_id = Html::encode($uid);

?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <script src="/js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>
        <script type="text/javascript" src="/js/ajax.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#issue-coupon').on('change', function() {
                    var cid = $(this).val();
                    if ('' === cid) {
                        $('.coupon-info').html('');
                    } else {
                        $.get('/coupon/coupon/allow-issue-list?uid=<?= $user_id ?>&cid='+cid, function(data) {
                            if (!data.code) {
                                var expire = data.data[0]['useEndDate'] ? '有效截止日期为'+data.data[0]['useEndDate'] :
                                    '有效期为'+data.data[0]['expiresInDays']+'天';
                                if (data.data[0]['type'] === '0') {
                                    $('.coupon-info').html('该代金券面值为'+data.data[0]['amount']+'元，' +
                                        '最小投资金额为'+convertToMoney(data.data[0]['minInvest'])+'元，'+expire+'。');
                                } else {
                                    $('.coupon-info').html('该加息券利率为'+parseFloat(data.data[0]['bonusRate'])+'%，' +
                                        '加息天数为'+data.data[0]['bonusDays']+'天,'+'最小投资金额为'+
                                        convertToMoney(data.data[0]['minInvest'])+'元，'+expire+'。');
                                }
                            } else {
                                alert('获取代金券数据失败');
                            }
                        });
                    }
                });

                var allowClick = true;
                $('#issue').on('click', function() {
                    if (!allowClick) {
                        return;
                    }

                    var cid = $('#issue-coupon').val();
                    if ('' === cid) {
                        newalert(0, '您还没有选择代金券');
                        return;
                    }

                    if (confirm("确认发放该券给用户?")) {
                        var form = $('#form');
                        allowClick = false;
                        var xhr = $.get(form.attr('action'), form.serialize(), function (data) {
                            newalert(!data.code, data.message);
                            parent.location.href="/user/user/detail?id=<?= $uid ?>&type=1&tabClass=<?= $tabClass ?>"
                            allowClick = true;
                        });

                        xhr.fail(function () {
                            allowClick = true;
                        });
                    }
                });
                //添加优惠券类型控制
                $("#coupon-type").on('change', function () {
                    var couponType = $("#coupon-type").val();
                    $.get('/coupon/coupon/allow-issue-list?uid=<?= $user_id ?>&couponType='+couponType, function(data) {
                        if (!data.code) {
                           var issueCoupon = $("#issue-coupon");
                           issueCoupon.html('')
                           var couponData = data.data;
                           var html = '<option>--请选择--</option>';
                           for(var i =0; i<couponData.length; i++) {
                               if(couponData[i]['type'] == 0) {
                                   html+= '<option value="'+couponData[i]['id']+'">'+couponData[i]['name']+'-'+
                                       parseFloat(couponData[i]['amount'])+'元-'+convertToMoney(couponData[i]['minInvest'])+'元起投</option>'
                               }else if(couponData[i]['type'] == 1) {
                                   html+= '<option value="'+couponData[i]['id']+'">'+couponData[i]['name']+'-'+
                                       parseFloat(couponData[i]['bonusRate'])+'%-'+couponData[i]['bonusDays']+'天-'+
                                       convertToMoney(couponData[i]['minInvest'])+'元起投</option>'
                               }
                           }
                           issueCoupon.html(html)
                        }
                    });
                })
            })
            //将数字格式化为金额格式
            function convertToMoney(num) {
                num = parseFloat(num)
                num = num.toLocaleString();
                return num;
            }
        </script>
    </head>

    <body class="page-header-fixed page-full-width">
        <div class="page-container row-fluid">
            <div class="page-content">
                <div>&nbsp;</div>
                <div class="form-horizontal form-view">
                    <form action="/coupon/coupon/issue-for-user" method="get" id="form">
                    <div class="control-group">
                        <label class="control-label">可发优惠券</label>
                        <div class="controls">
                            <input type="hidden" name="uid" value="<?= $user_id ?>">
                            <select name="cid" id="issue-coupon">
                                <option value="">--请选择--</option>
                                <?php foreach($model as $val) : ?>
                                    <?php if ($val['type'] == '0') { ?>
                                        <option value="<?= $val->id ?>">
                                            <?= $val->name ?>-<?= StringUtils::amountFormat2($val->amount) ?>元-
                                            <?= StringUtils::amountFormat2($val->minInvest) ?>元起投
                                        </option>
                                    <?php } else { ?>
                                        <option value="<?= $val->id ?>">
                                            <?= $val->name ?>-<?= StringUtils::amountFormat2($val->bonusRate) ?>%-
                                            <?= $val->bonusDays ?>天-
                                            <?= StringUtils::amountFormat2($val->minInvest) ?>元起投
                                        </option>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </select>
                            <label>&nbsp;&nbsp;优惠券类型</label>
                            <select name="type" id="coupon-type" class="m-wap span2">
                                <option value="">--全部--</option>
                                <option value="0">代金券</option>
                                <option value="1">加息券</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="controls">
                                <span class="text notice coupon-info"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn green" id="issue">发放</button>
                        <button type="button" class="btn green" onclick="closewin()">关闭窗口</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>