<?php
    use yii\helpers\Html;
    use Xii\Crm\Model\Engagement;

    $this->title = '登记客服记录';
    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<div class="row">
    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'phone_call_form']])?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">客服详情</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'number')->textInput(['id' => 'call_number'])?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'callerName')->textInput(['id' => 'call_name']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'gender')->radioList([Engagement::GENDER_MALE => '男性', Engagement::GENDER_FEMALE => '女性'])?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'callTime')->textInput(["onclick" => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"])?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'duration')->textInput()?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'direction')->radioList(Engagement::getDirectionLabels())?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-8">
                        <?= $form->field($engagement, 'content')->textarea()?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($engagement, 'summary')->textarea()?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <?= Html::submitButton('添加', ['class' => 'btn btn-primary  btn-lg btn-block', 'id' => 'phone_call_submit'])?>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
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
