<?php

use yii\helpers\Html;

$this->title = '开通资金托管账户';
$this->showViewport = false;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/tie-card/css/step.css?v=20170327">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="history.go(-1)">
            <div><?= Html::encode($this->title) ?></div>
            <a class="rg" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><img src="<?= FE_BASE_URI ?>wap/tie-card/img/phone.png" alt=""></a>
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
            <li class="lf">开通资金托管账户</li>
            <li>开通免密服务</li>
            <li class="rg">绑定银行卡</li>
        </ul>
    </div>

    <form method="post" class="cmxform" id="form" action="/user/identity/verify" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="name clearfix">
            <label for="name" class="f15 lf">真实姓名</label>
            <input type="text" class="lf f14" id="real_name" name='User[real_name]' placeholder="请输入本人姓名">
        </div>
        <div class="creidtCard clearfix">
            <label for="creidtCard" class="f15 lf">身份证号</label>
            <input type="text" class="lf f14" id="idcard" name='User[idcard]' placeholder="请输入本人身份证号码">
        </div>
    </form>

    <a class="instant f18" id="idcardbtn" href="javascript:void(0)">立 即 开 通</a>

    <div class="f12">
        <p class="tipsCtn">*实名认证信息由公安部进行认证</p>
        <p class="tipsCtn">*开通资金托管账户是为了确保您的账户资金安全</p>
    </div>
</div>

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
idCardBtn.html('开 通 中...');

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
    idCardBtn.html('开 通 中...');

    if (!validateForm()) {
        allowClick = true;
        idCardBtn.html('立 即 开 通');

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
            idCardBtn.html('立 即 开 通');
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
        idCardBtn.html('立 即 开 通');
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
                idCardBtn.html('立 即 开 通');
            }
        }
    })
}
</script>