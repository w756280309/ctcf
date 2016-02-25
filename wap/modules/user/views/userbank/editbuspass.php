<?php
use yii\bootstrap\ActiveForm;
$this->title="修改第三方交易密码";

$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/base.css">
<link rel="stylesheet" href="/css/setting.css">

<?php $form = ActiveForm::begin(['id' => 'form', 'action' =>"/user/userbank/reset-trade-pass"]); ?>
    <div>
        <input id="editpassbtn" class="btn-common btn-normal" style="margin-top:40px;" type="button" value="确认重置">
    </div>
<?php ActiveForm::end(); ?>

<!-- 绑定提示 end  -->
<!-- 修改登录密码页 end  -->
<script type="text/javascript">
    var csrf;
    $(function(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if ('1' === err) {
            toasturl(tourl, mess);
        }

        $('#editpassbtn').bind('click', function() {
            var $form = $('#form');
            $(this).attr('disabled', true);
            var xhr = $.post(
                $form.attr('action'),
                $form.serialize()
            );

            xhr.done(function(data) {
                if (data.message !== undefined) {
                    toast(form, data.message, function() {
                        $('#editpassbtn').attr('disabled', false);
                    });
                }
            });

            xhr.fail(function(jqXHR) {
                var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

                toast(null, errMsg);
                $('#rechargebtn').attr('disabled', false);
            });
        });
    })
</script>

