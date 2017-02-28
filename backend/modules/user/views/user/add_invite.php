<?php

use yii\widgets\ActiveForm;

$this->title = '补充邀请关系';

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>关系详情</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/detail?id=<?= $user->id ?>&type=1">关系详情</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="">补充邀请关系</a>
                </li>
            </ul>
        </div>

        <?php
            $form = ActiveForm::begin([
                'action' => '/user/user/add-invite?userId='.$user->id,
                'method' => 'get',
                'options' => [
                    'id' => 'form',
                    'class' => 'form-horizontal form-bordered form-label-stripped',
                ]
            ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">邀请人手机号码</label>
                <div class="controls">
                    <?= $user->mobile ?>
                </div>
            </div>

            <?php if ($user->real_name) { ?>
                <div class="control-group">
                    <label class="control-label">邀请人姓名</label>
                    <div class="controls">
                        <?= $user->real_name ?>
                    </div>
                </div>
            <?php } ?>

            <div class="control-group">
                <label class="control-label">被邀请人手机号码</label>
                <div class="controls">
                    <input name="mobile" id="mobile" autocomplete="off" placeholder="请输入被邀请人手机号码" maxlength="11" type="text" value="<?= $invitee ? $invitee->mobile : '' ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="button" class="btn blue">关联</button>&nbsp;&nbsp;&nbsp;
                <a href="/user/user/detail?id=<?= $user->id ?>&type=1" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
    <script type="text/javascript">
        $(function () {
            var $form = $('#form');
            var userMobile = '<?= $user->mobile ?>';
            var userName = '<?= $user->real_name ?>';

            $('#button').on('click', function () {
                var mobile = $('#mobile').val();

                if ('' === mobile) {
                    newalert(0, '被邀请人手机号不能为空');
                    return;
                }

                $.get($form.attr('action')+'?'+$form.serialize(), function (data) {
                    if (data.code) {
                        newalert(0, data.message);
                        return;
                    }

                    var note = '当前您正试图将手机号为'+data.mobile+'的用户';

                    if (data.realName) {
                        note += '('+data.realName+')';
                    }

                    note += '绑定邀请关系到手机号为'+userMobile+'的用户';

                    if (userName) {
                        note += '('+userName+')';
                    }

                    note += '上';

                    if (confirm(note)) {
                        $form.submit();
                    }
                });
            });
        })
    </script>
<?php $this->endBlock(); ?>