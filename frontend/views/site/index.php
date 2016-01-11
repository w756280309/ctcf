<?php

use yii\web\View;

$this->title = '@';

$js = <<<'JS'
$(function() {
    var $modal = $('#reg-first-modal').modal();
    $modal.modal('show');
})
JS;
$this->registerJs($js, View::POS_END, 'body_close');

?>
<div style="height: 300px; background-color: #eee"></div>

<div class="container">
    <div class="intro row">
        <div class="col-sm-3">
            <div class="intro-step-no">1</div>
            <div class="intro-step-title">3分钟完成注册</div>
            <div class="intro-step-desc">WAP端点击注册 填写个人信息<br>开通第三方托管 成功注册</div>
        </div>
        <div class="col-sm-3">
            <div class="intro-step-no">2</div>
            <div class="intro-step-title">1分钟开通快捷支付</div>
            <div class="intro-step-desc">分分钟充值提现成功</div>
        </div>
        <div class="col-sm-3">
            <div class="intro-step-no">3</div>
            <div class="intro-step-title">闪电快速充值</div>
            <div class="intro-step-desc">WAP端点击注册 填写个人信息<br>开通第三方托管 成功注册</div>
        </div>
        <div class="col-sm-3">
            <div class="intro-step-no">4</div>
            <div class="intro-step-title">投资安享收益</div>
            <div class="intro-step-desc">项目高收益 期限灵活<br>什么投资 安享好收益</div>
        </div>
    </div>
</div>

<div class="modal fade" id="reg-first-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>您当前的账户未绑定银行卡<br>请先访问移动端网站完成绑卡操作</p>

                <div style="margin: 20px auto; width: 160px; height: 160px; background-color: #eee;"></div>

                <p><button type="button" class="btn btn-primary" data-dismiss="modal">我知道了</button></p>
            </div>
        </div>
    </div>
</div>
