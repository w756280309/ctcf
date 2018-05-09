<?php

use yii\helpers\Html;
use common\view\UdeskWebIMHelper;
use common\models\user\User;

$this->title = '实名认证';
$this->showViewport = false;
//hotfix: 有体验问题，先注释掉
//UdeskWebIMHelper::init($this);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/tie-card/step.css?v=20170907">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png?v=1" alt="" onclick="history.go(-1)">
            <div style="color: #333;"><?= Html::encode($this->title) ?></div>
            <a class="rg" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><img src="<?= FE_BASE_URI ?>wap/tie-card/img/phone.png?v=1" alt=""></a>
        </div>
    <?php } ?>

    <div class="stepShow">
        <ul class="clearfix stepNum">
            <li class="lf f15 stepNow">1</li>
            <li class="lf line"></li>
            <li class="lf f15">2</li>
            <li class="lf line"></li>
            <li class="lf f15">3</li>
        </ul>
        <ul class="clearfix f12 stepIntro">
            <li class="lf">&nbsp;&nbsp;&nbsp;&nbsp;实名认证&nbsp;&nbsp;&nbsp;&nbsp;</li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;开通免密服务</li>
            <li class="rg">绑定银行卡</li>
        </ul>
    </div>

    <form method="post" class="cmxform" id="form" action="/user/identity/verify" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->idcard_status == User::IDCARD_STATUS_PASS) { ?>
            <div class="name clearfix">
                <label for="name" class="f15 lf">真实姓名</label>
                <input type="text" class="lf f14" id="real_name" name='User[real_name]' value="<?php Yii::$app->user->identity->real_name ?>" placeholder="请输入本人姓名">
            </div>
            <div class="creidtCard clearfix">
                <label for="creidtCard" class="f15 lf">身份证号</label>
                <input type="text" class="lf f14" id="idcard" name='User[idcard]' value="<?php Yii::$app->user->identity->safeIdCard ?>" placeholder="请输入本人身份证号码">
            </div>
        <?php } else { ?>
            <div class="name clearfix">
                <label for="name" class="f15 lf">真实姓名</label>
                <input type="text" class="lf f14" id="real_name" name='User[real_name]' placeholder="请输入本人姓名">
            </div>
            <div class="creidtCard clearfix">
                <label for="creidtCard" class="f15 lf">身份证号</label>
                <input type="text" class="lf f14" id="idcard" name='User[idcard]' placeholder="请输入本人身份证号码">
            </div>
        <?php } ?>
    </form>

    <a class="instant f18" id="idcardbtn" href="javascript:void(0)">立 即 认 证</a>

    <div class="f12">
        <p class="tipsCtn">*实名认证信息由公安部进行认证</p>
        <p class="tipsCtn">*平台为保障您的资金安全，出借前请先进行实名认证</p>
    </div>
</div>
<!--<p id="btn_udesk_im" style="position:absolute;bottom: 30px;"><img src="<?= FE_BASE_URI ?>wap/new-homepage/images/online-service-blue.png">在线客服</p>-->

<script type="text/javascript">
var csrf = $("meta[name=csrf-token]").attr('content');
var err = '<?= $code ?>';
var mess = '<?= $message ?>';
var tourl = '<?= $tourl ?>';
var t = 0;
var allowClick = true;
var idCardBtn = $('#idcardbtn');

if (err === '1') {
    toastCenter(mess, function () {
        if (tourl !== '') {
            location.href = tourl;
        }
    });
}

$(function () {
    $.get('/ctcf/user/get-name-and-card', function (data) {
       $('#real_name').val(data.real_name);
       $("#idcard").val(data.idCard)
    })
})

<?php
/**
 * @var \common\models\user\OpenAccount $lastRecord
 */
if(!is_null($lastRecord)) {
?>
t = setInterval(function () {
    getIdentityRes(<?= $lastRecord->id?>);
}, 1000);
allowClick = false;
$('#real_name').val('<?= $lastRecord->getName()?>');
$('#idcard').val('<?= $lastRecord->getIdCard()?>');
idCardBtn.html('认 证 中...');

<?php
}
?>

idCardBtn.on('click', function (e) {
    e.preventDefault();

    subForm();
});
function validateForm()
{
    if('' === $('#real_name').val()) {
        toastCenter('姓名不能为空');

        return false;
    }

    if('' === $('#idcard').val()) {
        toastCenter('身份证号不能为空');

        return false;
    }

    if(18 !== $('#idcard').val().length) {
        toastCenter('身份证暂只支持18位');

        return false;
    }

    return true;
}

function subForm() {
    if (!allowClick) {
        return false;
    }

    allowClick = false;
    idCardBtn.html('认 证 中...');

    if (!validateForm()) {
        allowClick = true;
        idCardBtn.html('立 即 认 证');

        return false;
    }

    var $form = $('#form');
    var xhr = $.post(
        $form.attr('action'),
        $form.serialize()
    );

    xhr.done(function (data) {
        if (0 !== data.code) {
            if (data.message) {
                toastCenter(data.message, function () {
                    if (typeof data.tourl !== 'undefined') {
                        location.href = data.tourl;
                    }
                });
            } else {
                if (typeof data.tourl !== 'undefined') {
                    location.href = data.tourl;
                }
            }

            allowClick = true;
            idCardBtn.html('立 即 认 证');
        } else {
            if (data.id) {
                t = setInterval(function () {
                    getIdentityRes(data.id);
                }, 1000);
            }
        }


    });

    xhr.fail(function (jqXHR) {
        var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
            ? jqXHR.responseJSON.message
            : '未知错误，请刷新重试或联系客服';

        toastCenter(errMsg);
        allowClick = true;
        idCardBtn.html('立 即 认 证');
    });
}

function getIdentityRes(id) {
    var xhr = $.get('/user/identity/res?id=' + id);
    xhr.done(function (data) {
        if (0 === data.code || 2 === data.code) {
            clearInterval(t);
            if (data.message) {
                toastCenter(data.message, function () {
                    if (typeof data.tourl !== 'undefined') {
                        location.href = data.tourl;
                    }
                });
            } else {
                if (typeof data.tourl !== 'undefined') {
                    location.href = data.tourl;
                }
            }

            if (2 === data.code) {
                allowClick = true;
                idCardBtn.html('立 即 认 证');
            }
        }
    })
}
</script>
