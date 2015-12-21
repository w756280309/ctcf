<?php
use yii\widgets\ActiveForm;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
        <div class="info">
                <h3>广告位列表</h3>
        </div>
</div>
<div class="tab" id="tab">
        <a class="selected" href="/adv/pos/index">返回分类列表</a>
</div>

<div class="page_form">
        <?php $form = ActiveForm::begin(['id' => 'pos_form', 'action' => "/adv/pos/edit?id=" . $model['id']]); ?>
                <div class="page_table form_table">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              
                                <tr>
                                        <td width="100" align="right">位置名称</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'title', ['template' => '{input}'])->textInput() ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'title', ['template' => '{error}']) ?>
                                        </td>
                                </tr>

                                <?php if($model->id>0) {?>
                                <tr>
                                        <td width="100" align="right">编号</td>
                                        <td width="300" class="text_left">
                                            <?= $model->code ?>
                                        </td>
                                        <td class="text_left">
                                            
                                        </td>
                                </tr>
                                <?php }else{?>
                                <tr>
                                        <td width="100" align="right">编号</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'code', ['template' => '{input}'])->textInput() ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'code', ['template' => '{error}']) ?>
                                        </td>
                                </tr>
                                <?php }?>
                                <tr>
                                        <td width="100" align="right">图片宽度</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'width', ['template' => '{input}'])->textInput() ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'width', ['template' => '{error}']) ?>
                                        </td>
                                </tr>
                                
                                <tr>
                                        <td width="100" align="right">图片高度</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'height', ['template' => '{input}'])->textInput() ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'height', ['template' => '{error}']) ?>
                                        </td>
                                </tr>

                                <tr>
                                        <td width="100" align="right">图片数量</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'number', ['template' => '{input}'])->textInput() ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'number', ['template' => '{error}']) ?>
                                        </td>
                                </tr>
                                
                                <tr>
                                        <td width="100" align="right">显示隐藏</td>
                                        <td width="300" class="text_left">
                                            <?= $form->field($model, 'status', ['template' => '{input}'])->radioList([0=>"正常",1=>"隐藏"]) ?>
                                        </td>
                                        <td class="text_left">
                                            <?= $form->field($model, 'status', ['template' => '{error}']) ?>
                                        </td>
                                </tr>
                        </table>
                </div>
                <!--普通提交-->
                <div class="form_submit">
                        <button type="submit" class="button">保存</button>
                </div>
        <?php $form->end(); ?>
</div>


<?php $this->endBlock(); ?>