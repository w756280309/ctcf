<?php
    use yii\helpers\Html;
    use Xii\Crm\Model\PhoneCall;

    $this->title = '添加客服记录';
    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<div class="row">
    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'phone_call_form']])?>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">基本信息</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($phoneCall, 'number')->textInput(['id' => 'call_number'])?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($phoneCall, 'callerName')->textInput(['id' => 'call_name']) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($phoneCall, 'gender')->radioList([PhoneCall::GENDER_MALE => '男性', PhoneCall::GENDER_FEMALE => '女性'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">记录详情</div>
            <div class="panel-body">
                <div class="col-md-4">
                    <?= $form->field($phoneCall, 'direction')->radioList(\Xii\Crm\Model\PhoneCall::getDirectionLabels())?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($phoneCall, 'duration')->textInput()?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($phoneCall, 'callTime')->textInput(["onclick" => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"])?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($phoneCall, 'content')->textarea()?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <?= Html::submitButton('添加', ['class' => 'btn btn-primary  btn-lg btn-block', 'id' => 'phone_call_submit'])?>
    </div>
    <?php $form->end()?>

</div>

<script>
    $('#call_number').on('change', function() {
        var _this = $(this);
        var number = _this.val();
        if (number) {
            $.ajax({
                url:'/crm/activity/info?number='+number,
                method:'GET'
            }).done(function(data){
                if (data && data.name) {
                    $('#call_name').val(data.name);
                }
            })
        }
    });

    $('#phone_call_form').on('beforeSubmit', function(e){
        $('#phone_call_submit').attr('disabled', true);
    });
</script>
