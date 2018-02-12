<?php

$this->title = '预约';

$this->registerCssFile(ASSETS_BASE_URI.'css/booking/wengutou.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/booking/wengutou.js', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/JPlaceholder.js', ['depends' => 'frontend\assets\FrontAsset']);

?>

<form method="post" id="form_wgt" action="/order/booking/book?pid=<?= $product->id ?>">
<input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
<div class="arrange-box">
    <p class="arrange-box-head">温股投预约</p>
    <div class="arrange-box-content">
        <div class="combine-single">
            <div class="single-font">姓名</div>
            <div class="text-en">
                <input class="name-text" type="text" placeholder="请输入您的姓名" autocomplete="off" name="BookingLog[name]" >
            </div>
        </div>
        <p class="arrange-tip" id="name_err"></p>
        <div class="combine-single">
            <div class="single-font">手机号</div>
            <div class="text-en">
                <input class="phone-text" type="text" placeholder="请输入您的手机号" autocomplete="off" maxlength="11" name="BookingLog[mobile]">
            </div>
        </div>
        <p class="arrange-tip" id="phone_err"></p>
        <div class="combine-single combine-single-margin">
            <div class="single-font">预约金额</div>
            <div class="text-en">
                <input class="money-text" name="BookingLog[fund]" type="text" placeholder="请输入预约金额" autocomplete="off">
            </div>
            <span class="single-unit">万</span>
        </div>
        <p class="arrange-tip" id="money_err"></p>
        <p class="arrange-tip-real">提示：温股投起购金额<?= $product->min_fund ?>万元</p>
        <a class="confirm-arrange">提交</a>
    </div>
</div>
</form>