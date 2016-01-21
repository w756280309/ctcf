<?php
use yii\web\View;
use yii\bootstrap\ActiveForm;

$this->title = '提现 - 温都金服';

$_js = <<<'JS'
$(function() {
    $("#editbank").hide();
    var pid = $('#province').find('option:selected').attr('data-id');
    if (pid) {
        sel_city(pid);
    }
    $('.editback_btn').bind('click', function() {
        var vals = $("#editbank_form").serialize();
        $(this).find('button[type=submit]').attr('disabled', true);
        $.post('/user/useraccount/editbank', vals, function(data){
            if (data['message']) {
                alert(data['message']);
            }
            $("#editbank").hide();
            $(this).find('button[type=submit]').attr('disabled', false);
        })
    })
    $('#province').change(function() {
        //console.log($(this).find('option:selected'));
        pid = $(this).find('option:selected').attr('data-id');
        sel_city(pid);
    })
    $('#city').bind('click', function() {
        var p_name = $("#province").val();
        if (p_name === undefined) {
            alert("请先选择省份");
        }
    })
})
JS;

$this->registerJs($_js, View::POS_END, 'body_close');

$_js2 = <<<'JS'
function sel_city(pid) {
    $.ajax({
        type: 'GET',
        url: "/user/useraccount/city?pid="+pid,
        dataType: 'json',
        success:function(data){
            if (data['name']) {
                var html='';
                var city = $("#city").attr('data-city');
                $.each(data.name, function (i, item) {
                    html += '<option value="'+item.name+'">'+item.name+'</option>';
                });
                $('#city').html(html);
                $("#city option[value='"+city+"']").attr("selected", "selected");
            } else {
                alert("没有找到对应的城市");
            }
        }
    });
}
function editbank() {
    $("#editbank").show();
}
JS;

$this->registerJs($_js2, View::POS_END, 'body_close2');

?>
<div class="container">
    <div class="main">
        <div class="page-heading">
            <h2>账户提现</h2>
        </div>

        <div class="section" style="padding-top: 40px">
            <ul class="wdjf-form">
                <li>
                    <div class="wdjf-label">持卡人</div>
                    <div class="wdjf-field"><?= $bank->account ?></div>
                </li>
                <li>
                    <div class="wdjf-label">银行卡</div>
                    <div class="wdjf-field" style="border: 1px solid #c0c0c0; width: 200px"><img src="/images/banks/<?= $bank->bank_id ?>.jpg"><?= substr_replace($bank->card_number, "**", 0, -4) ?></div>
                </li>
                <li>
                    <div class="wdjf-label">开户行信息</div>
                    <div class="wdjf-field"><a href="javascript:void(0)" onclick="editbank()" style="color: #c0c0c0"><img src="">信息变更</a></div>
                </li>
            </ul>

            <?php $form = ActiveForm::begin(['id' => 'editbank_form']); ?>
            <ul class="wdjf-form" id="editbank">
                <li>
                    <div class="wdjf-label">分支行名称</div>
                    <div class="wdjf-field"><?= $form->field($bank, 'sub_bank_name', ['template' => '{input}', 'inputOptions' => ['id' => 'sub_bank_name']])->textInput(); ?></div>
                </li>
                <li>
                    <div class="wdjf-label">分支行省份</div>
                    <div class="wdjf-field">
                        <select id="province" name="UserBanks[province]" class="form-control">
                            <option value="">---未选择---</option>
                            <?php foreach ($province as $val): ?>
                            <option value="<?= $val['name'] ?>" data-id="<?= $val['id'] ?>"
                            <?php
                            if ($bank->province === $val['name']) {
                                echo "selected='selected'";
                            }
                            ?>
                                    ><?= $val['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="wdjf-label">分支行城市</div>
                    <div class="wdjf-field">
                        <select id="city" name="UserBanks[city]" data-city="<?= $bank->city ?>" class="form-control">
                            <option value="">---未选择---</option>
                        </select>
                    </div>
                </li>
                <li class="wdjf-action">
                    <input class="btn btn-primary editback_btn" type="button" value="保存">
                </li>
            </ul>
            <?php ActiveForm::end(); ?>

            <h3>填写提现金额</h3>
            <div class="section">
                <?php $form = ActiveForm::begin(['id' => 'tixian_form', 'action' => "/user/useraccount/tixian",]); ?>
                <ul class="wdjf-form">
                    <li>
                        <div class="wdjf-label">可用余额</div>
                        <div class="wdjf-field"><span class="balance"><?= $user_account->available_balance ?></span> 元</div>
                    </li>
                    <li>
                        <div class="wdjf-label">提现金额</div>
                        <div class="wdjf-field"><?= $form->field($draw, 'money', ['template' => '{input}{error}'])->textInput(); ?></div><span style='margin-left: 5px;'>元</span>
                    </li>
                    <li>
                        <div class="wdjf-label">支付密码</div>
                        <div class="wdjf-field"><?= $form->field($model, 'password', ['template' => '{input}{error}', 'inputOptions' => ['placeholder'=>'请输入支付密码']])->passwordInput(); ?></div>
                    </li>
                    <li class="wdjf-action">
                        <input class="btn btn-primary" type="submit" value="提现申请">
                        <p>* 提现T+1个工作日到账，遇到法定节假日顺延</p>
                    </li>
                </ul>
                <?php ActiveForm::end(); ?>
            </div>

            <h3>温馨提示</h3>
            <div class="section">
                <ol>
                    <li>身份认证、提现银行卡绑定均设置成功后，才能进行提现；</li>
                    <li>提现款项将于申请提现后的下一个工作日到账（周六/周日/法定节假日均除外）。实际到账时间根据银行到账时间会有差异；</li>
                    <li>每笔提现款项收取1元手续费，提现后，账户余额大于1元，从账户余额中收取手续费；账户余额不足1元，从提现的资金中收取1元；</li>
                    <li>特殊声明：禁止洗钱、信用卡套现、虚假交易等行为，一经发现并确认，将终止该账户的使用；</li>
                    <li>如果提现出现问题，请联系客服，<?= Yii::$app->params['contact_tel'] ?>(工作日9:00-18:00)。</li>
                </ol>
            </div>
        </div>
    </div>
</div>
