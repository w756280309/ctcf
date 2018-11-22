<?php

use yii\web\YiiAsset;
use yii\widgets\ActiveForm;

$this->title = '资金转移';
$this->registerJsFile('/js/My97DatePicker/bootstrap-select.js', ['depends' => YiiAsset::class]);
$this->registerCssFile('/css/bootstrap-select.min.css', ['position' => 1]);
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                资金转移 <small>（针对各种问题将进行紧急、临时处理）</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/toolbox/transfer/index">工具箱</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/toolbox/transfer/index">资金转移</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <h4><strong>商户间转账</strong></h4>
        <hr>
        <?php $form = ActiveForm::begin(['id' => 'CTOC', 'action' => '/toolbox/transfer/first', 'options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="span12">
            <div class="span3">
                <div class="control-group">
                    <label class="control-label">付款方</label>
                    <div class="controls">
                        <select class="chosen-with-diselect selectpicker" data-live-search="true" data-size="10" name="payerId" id="payerId-select">
                            <?php foreach ($selectedUsers as $userId => $name) { ?>
                                <option value="<?= $userId ?>"><?= $name.'--'.$userId ?></option>
                            <?php } ?>
                        </select>
                        <span class="balance"></span>
                    </div>
                </div>
            </div>
            <div class="span3">
                <div class="control-group">
                    <label class="control-label">收款方</label>
                    <div class="controls">
                        <select class="chosen-with-diselect selectpicker" data-live-search="true" data-size="10" name="receiverId" id="receiverId-select">
                            <?php foreach ($selectedUsers as $userId => $name) { ?>
                                <option value="<?= $userId ?>"><?= $name.'--'.$userId ?></option>
                            <?php } ?>
                        </select>
                        <span class="balance"></span>
                    </div>
                </div>
            </div>
            <div class="span3">
                <label class="control-label">转账金额（元）</label>
                <div class="input-append controls" style="display: block;width: 65%;">
                    <span class="add-on">￥</span>
                    <input type="text" class="m-wrap span12" name="money" autocomplete="off" placeholder="请输入转账金额" value="">
                    <span class="add-on">元</span>
                </div>
            </div>
            <div class="span3">
                <div class="control-group">
                    <label class="control-label">转账金额是否提现</label>
                    <div class="controls">
                        <select class="chosen-with-diselect selectpicker" data-live-search="true" data-size="10" name="withdrawalId" id="withdrawalId-select">
                            <?php foreach ($withdrawal as $selectId => $name) { ?>
                                <option value="<?= $selectId ?>"><?= $name ?></option>
                            <?php } ?>
                        </select>
                        <span class="balance"></span>
                    </div>
                </div>
            </div>
            <div class="span3">
                <label class="control-label">操作</label>
                <div>
                    <button type="button" class="btn blue" id="x-tranfer"><i class="icon-ok"></i>确认转账</button>
                </div>
            </div>
        </div>
        <?php $form->end() ?>
    </div>
</div>
<script>
    $(function () {
        $('#payerId-select, #receiverId-select').on('change', function () {
            var _this = $(this);
            var xhr = $.get('/toolbox/transfer/get-balance?userId='+ _this.val(), function (data) {
                _this.parent().next().html('<br>平台账户余额：' + data.plat + '元，<br>联动账户余额：' + data.ump + '元。');
            });

            xhr.fail(function () {
                alert('系统异常，请稍后重试');
            });
        });

        var allowClick = true;
        $('#x-tranfer').on('click', function() {
            if (!allowClick) {
                return;
            }

            var payerId = parseInt($('#payerId-select option:selected').val());
            var receiverId = parseInt($('#receiverId-select option:selected').val());
            var receiverText = $('#receiverId-select option:selected').text();
            var withdrawalId = parseInt($('#withdrawalId-select option:selected').val());
            if (0 === payerId || 0 === receiverId) {
                alert('请选择正确的付款方或者收款方');
                return false;
            }
            if (payerId === receiverId) {
                alert('付款方与收款方不能相同');
                return false;
            }

            var amount = parseFloat($("input[name='money']").val());
            if (amount <= 0 || isNaN(amount)) {
                alert('请输入正确的金额');
                return false;
            }
            if(withdrawalId === 0){
            if (confirm('确认向【'+receiverText+'】转账【'+amount+'】元并且不执行提现操作么?')) {
                var form = $('#CTOC');
                allowClick = false;
                openLoading();
                $.post(form.attr('action'), form.serialize(), function (result) {
                    allowClick = true;
                    if ('success' === result.code) {
                        allowClick = true;
                        if ('borrower' === result.data.toType) {
                            layer.confirm('已成功向'+receiverText+'向转账'+amount+'元，前往融资会员详情查看余额？', {title: '转账', btn: ['立即前往', '取消']}, function () {
                                cloaseLoading();
                                location.href = '/user/user/detail?id='+result.data.toId;
                            }, function () {
                                location.reload();
                                layer.closeAll();
                            });
                        } else {
                            layer.confirm('已成功向'+receiverText+'转账'+amount+'元', {title: '转账', btn: ['确定', '取消']}, function () {
                                cloaseLoading();
                                layer.closeAll();
                                location.reload();
                            }, function () {
                                layer.closeAll();
                                location.reload();
                            });
                        }
                    } else {
                        layer.closeAll();
                        alert(result.message);
                    }
                });
            }
            }else{
                if (confirm('确认向【'+receiverText+'】转账【'+amount+'】元并且向银行账户提现【'+amount+'】元么?')) {
                    var form = $('#CTOC');
                    allowClick = false;
                    openLoading();
                    $.post(form.attr('action'), form.serialize(), function (result) {});
                    $.post('/toolbox/transfer/withdrawal', form.serialize(), function (result) {
                        allowClick = true;
                        if ('success' === result.code) {
                            allowClick = true;
                                layer.confirm('已成功向'+receiverText+'转账后提现'+amount+'元，前往融资会员详情查看余额？', {title: '转账', btn: ['立即前往', '取消']}, function () {
                                    cloaseLoading();
                                    location.href = '/user/user/detail?id='+result.data.toId;
                                }, function () {
                                    location.reload();
                                    layer.closeAll();
                                });
                        } else {
                            layer.closeAll();
                            alert(result.message);
                        }
                    });
                }
            }

            return false;
        });
    })
</script>
<?php $this->endBlock(); ?>
