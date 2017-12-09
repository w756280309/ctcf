<?php

use yii\widgets\ActiveForm;
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$this->title = '兑换商品';
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员管理模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/offline/list">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/offline/list">线下会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/offline/detail?id=<?= $user->id ?>" class="detail-list">会员详情</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">兑换商品</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'id' => 'exchange-goods',
            'action' => '/user/offline/do-exchange',
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="hidden" name="user_id" value="<?= $user->id ?>">
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">姓名</label>
                <div class="controls">
                    <?= $user->realName ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">手机号</label>
                <div class="controls">
                    <?= $user->mobile ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">商品名称</label>
                <div class="controls">
                    <input type="text" name="offGoodsName" autocomplete="off" placeholder="请输入商品名称" class="offGoodsName">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">积分数量</label>
                <div class="controls currentPoint">
                    <?= $user->points ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">商品兑换积分</label>
                <div class="controls">
                    <input type="points" name="points" autocomplete="off" class="points" placeholder="请填写积分">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">兑换时间</label>
                <div class="controls">
                    <input type="text" name="orderTime" autocomplete="off" class="orderTime" placeholder="请选择兑换时间" readonly="readonly" onclick = "WdatePicker({dateFmt:'yyyy-MM-dd  HH:mm:ss'})">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn blue subm-btn"><i class="icon-ok"></i> 确定 </button>&nbsp;&nbsp;&nbsp;
                <a href="/user/offline/detail?id=<?= $user->id . '&tabClass=' . Yii::$app->request->get('tabClass') ?>" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $(function() {
        $('.subm-btn').bind('click', function () {
            var _this = $(this);
            var points = $('.points').val();
            var curPoints = parseFloat($('.currentPoint').html());
            if ('' === $('.offGoodsName').val()) {
                layer.msg('请填写商品名称', {icon: 2});
                return false;
            }
            if (isNaN(points) || points < 0) {
                layer.msg('请填写正确格式的积分，积分应大于0', {icon: 2});
                return false;
            }
            if (points > curPoints) {
                layer.msg('扣减的积分已超出了当前积分', {icon: 2});
                return false;
            }
            if ('' === $('.orderTime').val()) {
                layer.msg('请选择兑换时间', {icon: 2});
                return false;
            }
            var form = $('#exchange-goods');
            vals = form.serialize();
            _this.attr('disabled', 'disabled');
            $.post(form.attr("action"), vals, function (data) {
                _this.removeAttr('disabled');
                if (0 === data.code) {
                    location.href = "/user/offline/detail?id=<?= $user->id ?>&tabClass=<?= Yii::$app->request->get('tabClass') ?>";
                } else {
                    layer.msg(data.message, {icon: 2});
                    return false;
                }
            });
        });
    });
</script>
<?php $this->endBlock(); ?>