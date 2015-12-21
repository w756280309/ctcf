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
 *  */
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="page_function">
    <div class="info">
        <h3>添加投资</h3>
    </div>
</div>
<div class="tab" id="tab">
    <a class="selected" href="/order/order/index?user_id=<?=$user['id']?>">返回列表</a>
</div>
<div class="page_form">
    <?php $form = ActiveForm::begin(['id' => 'order_form', 'action' => "/order/order/edit?id=" . $model['id']."&uid=".$user['id']]); ?>

    <div class="page_table form_table">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                    <td width="100" align="right">用户名</td>
                    <td width="300" class="text_left">
                         <?=$user['username'] ?>
                        <?= $form->field($model, 'user_id', ['template' => '{input}'])->hiddenInput(['value'=>$user['id']]) ?>
                        <?= $form->field($model, 'username', ['template' => '{input}'])->hiddenInput(['value'=>$user['username']]) ?>
                    </td>
                    <td class="text_left"></td>
            </tr>
            
            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'product_sn', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'product_sn', ['template' => '{input}<input type="button" value="搜索产品" onclick="searchPro();" />'])->textInput() ?>
                        
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'product_sn', ['template' => '{error}'])?> 
                    </td>
            </tr>

             <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'product_title', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'product_title', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <div><?= $form->field($model, 'product_title', ['template' => '{error}'])?></div>
                    </td>
            </tr>
            
<!--            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'real_name', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'real_name', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'real_name', ['template' => '{error}'])?>
                    </td>
            </tr>

             <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'idcard', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'idcard', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'idcard', ['template' => '{error}'])?>
                    </td>
            </tr>-->
<!--            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'org_name', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'org_name', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'org_name', ['template' => '{error}'])?>
                    </td>
            </tr>

            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'org_code', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'org_code', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'org_code', ['template' => '{error}'])?>
                    </td>
            </tr>-->

            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'order_money', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'order_money', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'order_money', ['template' => '{error}'])?>
                    </td>
            </tr>
             <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'yield_rate', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'yield_rate', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'yield_rate', ['template' => '{error}'])?>
                    </td>
            </tr>
 <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'product_duration', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'product_duration', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'product_duration', ['template' => '{error}'])?>
                    </td>
            </tr>
<tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'order_time', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'order_time', ['template' => '{input}'])->textInput(['onClick'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss"});','readonly'=>"true"]) ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'order_time', ['template' => '{error}'])?>
                    </td>
            </tr>
<tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'pay_time', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'pay_time', ['template' => '{input}'])->textInput(['onClick'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss"});','readonly'=>"true"]) ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'pay_time', ['template' => '{error}'])?>
                    </td>
            </tr>
<tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'contract_sn', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'contract_sn', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'contract_sn', ['template' => '{error}'])?>
                    </td>
            </tr>
     <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'status', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'status', ['template' => '{input}'])->dropDownList($status_arr) ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'status', ['template' => '{error}'])?>
                    </td>
            </tr>
            
            <tr>
                    <td width="100" align="right">
                    <?= $form->field($model, 'toubiao_type', ['template' => '{label}']) ?></td>
                    <td width="300" class="text_left">
                         <?= $form->field($model, 'toubiao_type', ['template' => '{input}'])->dropDownList($toubiao_arr) ?>
                    </td>
                    <td class="text_left">
                        <?= $form->field($model, 'toubiao_type', ['template' => '{error}'])?>
                    </td>
            </tr>


        </table>
    </div>

<!--    <input type="submit" class="button" value="保存"/>-->
    <button type="button" class="button ajax_button">保存</button>
    <script type="text/javascript">
        uid="<?=$user['id']?>";
    function searchPro(){
        $('.readpro').detach();
        var psn = $('#offlineorder-product_sn').val();
        $('#offlineorder-product_sn').after("<span style='color:red' class='readpro'>请稍等。数据读取中……</span>");
        $.get('/product/product/productinfo', {sn:$('#offlineorder-product_sn').val()}, function (data) {  
            var da = eval(("("+data+")"));
            $("#offlineorder-product_title").val("");
            $("#offlineorder-yield_rate").val("");
            $("#offlineorder-product_duration").val("");
            $('.readpro').detach();
            if(da['title']==""||da['title']==null){
               $('#offlineorder-product_sn').after("<span style='color:red' class='readpro'>没有找到！</span>") 
            }else{
                $("#offlineorder-product_title").val(da['title']);
                $("#offlineorder-yield_rate").val(da['yield_rate']);
                $("#offlineorder-product_duration").val(da['product_duration']);
            }
            
        });
    }
    
    $(function(){ 
    $('.ajax_button').click(function(){
       vals = $("#order_form").serialize();

        $.post($("#order_form").attr("action"), vals, function (data) {  
            
           res(data,"/order/order/index?user_id="+uid);
            
        });  
    })

});
    </script>

    <?php $form->end(); ?>
</div>

<?php $this->endBlock(); ?>