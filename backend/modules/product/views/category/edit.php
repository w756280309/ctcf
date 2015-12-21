<?php

/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-18
 * Time: 下午7:46
 */
use yii\widgets\ActiveForm;

/**
  // * @var $model common\models\news\Category
 * @var yii\web\View $this
 * @var $categories

  $this->registerJsFile('/admin/js/validate.js', ['depends' => 'yii\web\YiiAsset']);
  <?php $form = ActiveForm::begin(['id'=>'product_category_form', 'action' => "/product/category/add", 'enableClientValidation' => FALSE, 'enableAjaxValidation' => true, 'options'=>['onafterValidate'=>'js:function() {console.log("Hello world!")}']]); ?>
 *  */
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
    <div class="info">
        <h3>编辑分类</h3>
    </div>
</div>
<div class="tab" id="tab">
    <a class="selected" href="/product/category/index">返回分类列表</a>
</div>
<div class="page_form">
<?php $form = ActiveForm::begin(['id' => 'product_category_form', 'action' => "/product/category/add?id=".$model['id']]); ?>
    <div class="page_table form_table">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'parent_id', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'parent_id', ['template' => '{input}'])->dropDownList([0 => '=====顶级分类====='] + $categories);
                    ?>
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'parent_id', ['template' => '{error}']);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'name', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'name', ['template' => '{input}'])->textInput();
                    ?>
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'name', ['template' => '{error}']);
                    ?>
                </td>
            </tr>
          
         <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'code', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'code', ['template' => '{input}'])->textInput();
                    ?>
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'code', ['template' => '{error}']);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'description', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'description', ['template' => '{input}'])->textarea(['cols'=>60,'rows'=>6]);
                    ?>
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'description', ['template' => '{error}']);
                    ?>
                </td>
            </tr>
            
           <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'status', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'status', ['template' => '{input}'])->radioList([1 => '显示', 0 => '隐藏']);
                    ?>控制调用的显示与隐藏
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'status', ['template' => '{error}']);
                    ?>
                </td>
            </tr> 
            
            <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'home_status', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'home_status', ['template' => '{input}'])->radioList([1 => '显示', 0 => '隐藏']);
                    ?>控制调用的显示与隐藏
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'home_status', ['template' => '{error}']);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td width="100" align="right">
                    <?=
                    $form->field($model, 'sort', ['template' => '{label}']);
                    ?>
                </td>
                <td width="300" class="text_left">
                   <?=
                    $form->field($model, 'sort', ['template' => '{input}'])->textInput();
                    ?>控制调用的显示与隐藏
                </td>
                <td class="text_left">
                    <?=
                    $form->field($model, 'sort', ['template' => '{error}']);
                    ?>数字越大越在前面
                </td>
            </tr>
            
        </table>
<?= $form->field($model, 'id', ['template' => '{input}'])->hiddenInput() ?>
    </div>
    <!--普通提交-->
    <div class="form_submit">
        <button type="submit" class="button">保存</button>
    </div>
<?php $form->end(); ?>
</div>
<?php $this->endBlock(); ?>