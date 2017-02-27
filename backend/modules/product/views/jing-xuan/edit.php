<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$isEdit = !empty($model->id);
$this->title = ($isEdit ? '编辑' : '添加') . ' 精选项目介绍页';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                发行方管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/issuer/list">发行方管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/jing-xuan/list">精选项目介绍页列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)"><?= $isEdit ? '编辑' : '添加' ?>精选项目介绍页</a>
                </li>
            </ul>
        </div>

        <?php
            $form = ActiveForm::begin([
                'action' => '/product/jing-xuan/'.($isEdit ? 'edit?id='.$model->id : 'add'),
                'options' => [
                    'id' => 'editForm',
                    'class' => 'form-horizontal form-bordered form-label-stripped',
                    'enctype' => 'multipart/form-data',
                ]
            ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">发行方</label>
                <div class="controls">
                    <?php if (!$isEdit) { ?>
                        <?= $form->field($model, 'issuerId', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '请选择发行方名称']])->dropDownList(ArrayHelper::map($issuers, 'id', 'name'),['prompt'=>'不选择']) ?>
                        <?= $form->field($model, 'issuerId', ['template' => '{error}']) ?>
                    <?php } else { ?>
                            <?= $model->issuer->name ?>
                    <?php } ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">页面标题</label>
                <div class="controls">
                    <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入页面标题', 'class' => 'span4']])->textInput() ?>
                    <?= $form->field($model, 'title', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group portlet-body">
                <label class="control-label">页面内容</label>
                <style>
                    table {
                        width: 100%;
                    }
                    td.firstTd {
                        width: 15%;
                        text-align: center;
                    }
                    #sy_table .step {
                        display: none;
                    }
                </style>
                <div class="controls">
                    <table class="table-striped table-bordered table-advance table-hover">
                        <tr>
                            <td>上传顶部图片</td>
                            <td>
                                <input type="file" name="JxPage[pic]" />
                                <?php if (isset($content['pic'])) { ?>
                                    <br>
                                    <img src="/<?= $content['pic'] ?>" alt="顶部图片"/>
                                <?php } ?>
                                <span class="notice">*图片上传格式必须为PNG或JPG，宽度限定为：750px</span>
                                <?= $form->field($model, 'content', ['template' => '{error}']) ?>
                            </td>
                        </tr>
                        <?php
                            $zdy = ['发行人', '担保方', ''];
                            foreach ($zdy as $zk => $zv) {
                        ?>
                            <tr>
                                <td>自定义模块</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td class="firstTd">主标题</td>
                                            <td>
                                                <input type="text" class="span12" name="JxPage[content][zdy][<?= $zk ?>][mt]" value="<?= isset($content['zdy'][$zk]['mt']) ? trim($content['zdy'][$zk]['mt']) : $zv ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="firstTd">副标题</td>
                                            <td>
                                                <input type="text" class="span12" name="JxPage[content][zdy][<?= $zk ?>][ft]" value="<?= isset($content['zdy'][$zk]['ft']) ? trim($content['zdy'][$zk]['ft']) : '' ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="firstTd">内容</td>
                                            <td>
                                                <textarea class="span12" name="JxPage[content][zdy][<?= $zk ?>][content]" rows="10" placeholder="请输入内容"><?= isset($content['zdy'][$zk]['content']) ? trim($content['zdy'][$zk]['content']) : '' ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td>还款来源</td>
                            <td>
                                <table>
                                    <tr>
                                        <td class="firstTd">
                                            第一还款来源
                                        </td>
                                        <td class="td_ft">
                                            <input type="text" class="span12" name="JxPage[content][hkly][0][content]" value="<?= isset($content['hkly'][0]['content']) ? trim($content['hkly'][0]['content']) : '' ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="firstTd">
                                            第二还款来源
                                        </td>
                                        <td class="td_ft">
                                            <input type="text" class="span12" name="JxPage[content][hkly][1][content]" value="<?= isset($content['hkly'][1]['content']) ? trim($content['hkly'][1]['content']) : '' ?>">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                            $csBlock = [
                                'zxcs' => '增信措施',
                                'bzcs' => '保障措施',
                            ];
                            foreach ($csBlock as $csKey => $cs) {
                        ?>
                            <tr>
                                <td><?= $cs ?></td>
                                <td>
                                    <table>
                                        <tr>
                                            <td class="firstTd">副标题</td>
                                            <td class="td_ft">
                                                <input type="text" class="span12" name="JxPage[content][<?= $csKey ?>][ft]" value="<?= isset($content[$csKey]['ft']) ? trim($content[$csKey]['ft']) : '' ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="firstTd">内容</td>
                                            <td class="td_ct">
                                                <textarea class="span12" name="JxPage[content][<?= $csKey ?>][content]" rows="10" placeholder="请输入内容"><?= isset($content[$csKey]['content']) ? trim($content[$csKey]['content']) : '' ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td>产品要素信息</td>
                            <td>
                                <table>
                                    <?php
                                        $cpys = ['发行人', '备案登记机构', '产品金额', '产品期限', '认购起点', '预期年化收益率', '起息日', '收益分配'];
                                        foreach ($cpys as $k => $yaosu) {
                                    ?>
                                            <?php
                                            if ('预期年化收益率' === $yaosu) {
                                            ?>
                                                <tr>
                                                    <td class="firstTd">预期年化收益率</td>
                                                    <td class="td_ft">
                                                        <select class="span12" name="JxPage[content][syl]" id="syl">
                                                            <option value="0" <?php if (isset($content['syl']) && '0' === $content['syl']) { ?> seleted = 'seleted' <?php } ?>>普通利率</option>
                                                            <option value="1" <?php if (isset($content['syl']) && '1' === $content['syl']) { ?> selected = 'seleted' <?php } ?>>阶梯利率</option>
                                                        </select>
                                                        <table id="sy_table" class="span12" style="margin:0;">
                                                            <input class="span12" type="hidden" name="JxPage[content][cpys][5][ft]" value="预期年化收益率">
                                                            <tr class="normal">
                                                                <td><p style="margin: 0 20px 0 0;">预期年化收益率</p></td>
                                                                <td>
                                                                    <input class="span12" type="text" name="JxPage[content][cpys][5][content]" value="<?= isset($content['cpys'][5]['content']) ? trim($content['cpys'][5]['content']) : '' ?>">
                                                                </td>
                                                            </tr>
                                                            <tr class="step">
                                                                <td>金额</td>
                                                                <td>预期利率/年</td>
                                                            </tr>
                                                            <tr class="step">
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][0][title]" value="<?= isset($content['jieti'][0]['title']) ? trim($content['jieti'][0]['title']) : '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][0][content]" value="<?= isset($content['jieti'][0]['content']) ? trim($content['jieti'][0]['content']) : '' ?>">
                                                                </td>
                                                            </tr>
                                                            <tr class="step">
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][1][title]" value="<?= isset($content['jieti'][1]['title']) ? trim($content['jieti'][1]['title']) : '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][1][content]" value="<?= isset($content['jieti'][1]['content']) ? trim($content['jieti'][1]['content']) : '' ?>">
                                                                </td>
                                                            </tr>
                                                            <tr class="step">
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][2][title]" value="<?= isset($content['jieti'][2]['title']) ? trim($content['jieti'][2]['title']) : '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][2][content]" value="<?= isset($content['jieti'][2]['content']) ? trim($content['jieti'][2]['content']) : '' ?>">
                                                                </td>
                                                            </tr>
                                                            <tr class="step">
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][3][title]" value="<?= isset($content['jieti'][3]['title']) ? trim($content['jieti']['3']['title']) : '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="JxPage[content][jieti][3][content]" value="<?= isset($content['jieti'][3]['content']) ? trim($content['jieti']['3']['content']) : '' ?>">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php
                                            } else {
                                            ?>
                                                <tr>
                                                    <td class="firstTd"><?= $yaosu ?></td>
                                                    <td class="td_ft">
                                                        <input class="span12" type="hidden" name="JxPage[content][cpys][<?= $k ?>][ft]" value="<?= $yaosu ?>">
                                                        <input class="span12" type="text" name="JxPage[content][cpys][<?= $k ?>][content]" value="<?= isset($content['cpys'][$k]['content']) ? trim($content['cpys'][$k]['content']) : '' ?>">
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                    <?php
                                        }
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" id="confirmBtn" class="btn blue"><i class="icon-ok"></i>确认</button>
                &nbsp;&nbsp;&nbsp;
                <a href="list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $(function(){
        $('#syl').on('change', function() {
            if ('0' === $(this).val()) {
                $('#sy_table .step input').val('');
                $('#sy_table .step').hide();
                $('#sy_table .normal').show();
            } else if ('1' === $(this).val()) {
                $('#sy_table .normal input').val('');
                $('#sy_table .normal').hide();
                $('#sy_table .step').show();
            }
        })
        var isJieTi = '<?= isset($content['syl']) && '1' === $content['syl'] ?>';
        if (isJieTi) {
            $('#sy_table .normal').hide();
            $('#sy_table .step').show();
        } else {
            $('#sy_table .step').hide();
            $('#sy_table .normal').show();
        }
        $('#confirmBtn').on('click', function() {
            var _this = $(this);
            if (true === _this.attr('disabled')) {
                return false;
            }
            _this.attr('disabled', true);
            $("#editForm").submit();
            _this.removeAttr('disabled');
        });
    })
</script>
<?php $this->endBlock(); ?>
