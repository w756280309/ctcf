<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '添加/编辑新闻分类';
?>
<?php $this->beginBlock('blockmain'); ?>
    <div class="page_function">
        <div class="info">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <div class="tab" id="tab"><a class="selected" href="/news/category/">返回分类列表</a>
    </div>
    <div class="page_form">
    <?php $form = ActiveForm::begin(['id' => 'category_form', 'action' => "/news/category/edit?id=".$model['id']]);  ?>
        <div class="page_table form_table">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100" align="right">上级分类</td>
                    <td width="300" class="text_left">
                        <?=$form->field($model, 'parent')->dropDownList([0=>'=====顶级分类====='] + $categories); ?>
                    </td>
                    <td class="text_left"></td>
                </tr>
                <tr>
                    <td width="100" align="right">分类名称</td>
                    <td width="300" class="text_left">
                        <?= $form->field($model, 'name', ['template' => '{input}', 'inputOptions'=>['class'=>'text_value']])->textInput() ?>   
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'name', ['template' => '{error}']); ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right">分类描述</td>
                    <td width="300" class="text_left">                                                                                
                        <?= $form->field($model, 'description', ['template' => '{input}', 'inputOptions'=>['class'=>'text_value']])->textarea(); ?> 
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'description', ['template' => '{error}']); ?>
                    </td>
                </tr>                        
                <tr>
                    <td width="100" align="right">分类显示</td>
                    <td width="300" class="text_left">
                        <?= $form->field($model, 'status', ['template' => '{input}'])->radioList([1 => '显示', 0 => '隐藏']); ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right">分类顺序</td>
                    <td width="300" class="text_left">                                        
                        <?= $form->field($model, 'sort', ['template' => '{input}', 'inputOptions'=>['class'=>'text_value']])->textInput(); ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'sort', ['template' => '{error}']); ?>
                    </td>
                </tr>                       
            </table>
        </div>
        <!--普通提交-->
        <div class="form_submit">
            <?= Html::submitButton('保存', ['class' => 'button', 'id' => 'btnSave', 'name' => 'btnSave']) ?>
        </div>
    <?php $form->end(); ?>
    </div>

<?php $this->endBlock(); ?>


   


