<?php

$this->title = '补充兑换码';
use yii\widgets\ActiveForm;

?>
<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    运营管理 <small>O2O商家管理</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/o2o/affiliator/list">商家列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a class="jump-list" href="/o2o/card/list?affId=<?= $affId ?>">兑换码列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/o2o/card/supplement?affId=<?= $affId ?>">补充兑换码</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body form">
                <?php $action = '/o2o/card/supplement?affId='. $affId; ?>
                <?php $form = ActiveForm::begin(['action'=>$action, 'options'=>['class'=>'form-horizontal form-bordered form-label-stripped', 'enctype'=>'multipart/form-data', 'id' => 'add-code']]); ?>
                    <input type="hidden" name="flag" value="1">
                    <div class="control-group">
                    <label class="control-label">奖品名称</label>
                    <div class="controls">
                        <select class = "m-wrap goods-name" style = "width:200px" name = 'gid' id="goodsName">
                            <?php foreach ($goods as $good): ?>
                                <option days="<?= $good->effectDays ?>" value="<?= $good->id ?>">
                                    <?= $good->name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">兑换码有效期</label>
                    <div class="controls">
                        <input type="number" autocomplete="off" style = "width:185px" name="effectDays" class="code-num" value="<?= $goods[0]->effectDays ?>" id="effectDays">
                        <p style="display: inline-block">天</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">上传Excel表格</label>
                    <div class="controls">
                        <?= $form->field($model, 'excel', ['template' => '{input}'])->fileInput() ?>
                        <?= $form->field($model, 'excel', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" id="confirm-btn" class="btn blue sub-btn"><i class="icon-ok"></i> 提交 </button>&nbsp;&nbsp;&nbsp;
                    <a href="/o2o/card/list?affId=<?= $affId ?>" class="btn">取消</a>
                </div>
                <?php $form->end(); ?>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#goodsName').bind('change', function () {
                $('#effectDays').val($(this).find('option:selected').attr('days'));
            });
            $('#confirm-btn').bind('click', function () {
                var form = $('#add-code');
                $(this).attr('disabled', 'disabled');
                form.submit();
            })
        })
    </script>
<?php $this->endBlock(); ?>