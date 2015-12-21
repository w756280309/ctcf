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
$this->registerCssFile('/kindeditor/themes/default/default.css',['depends' => 'yii\web\YiiAsset'] );
$this->registerJsFile('/kindeditor/kindeditor-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/kindeditor/lang/zh_CN.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile('/js/product.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
        <div class="info">
                <h3>添加特殊产品</h3>
        </div>
</div>
<div class="tab" id="tab">
        <a class="selected" href="/product/product/special">返回列表</a>
</div>

<div class="page_form">
    <div><span style="color:red;font-weight:bold;">*</span>为必填项</div>
        <?php $form = ActiveForm::begin(['id'=>'product_special_form', 'action' => "/product/product/specialedit?id=".$model['id'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
                  <?=
                    $form->field($model, 'home_status', ['template' => '{input}'])->hiddenInput(['value'=>1]);
                ?>
                <div class="page_table form_table">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="right">
                                        <span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'title', ['template' => '{label}']) ?>
                                    </td>
                                    <td class="text_left">
                                        <?=
                                            $form->field($model, 'title', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                                        ?>
                                    </td>
                                    
                                    
                                    <td align="right">
                                        <span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'sn', ['template' => '{label}']) ?>
                                    </td>
                                    <td class="text_left">
                                        <?=
                                            $form->field($model, 'sn', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                                        ?>
                                    </td>
                                    
                             <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'category_id', ['template' => '{label}']) ?></td>
                             <td class="text_left">
                            <?=
                                $form->field($model, 'category_id', ['template' => '{input}{error}'])->dropDownList($cat_data);
                            ?>
                             </td>
                            
                                        
                                </tr>
                                <tr>
                              
                                    <td align="right"><span style="color:red;font-weight:bold;">*</span>挂牌底价</td>
                                    <td class="text_left">
                                        
                            <?=
                                $form->field($model, 'money', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                        
                                    </td>      
                                    
                                  <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'end_time', ['template' => '{label}']) ?></td>
                                  <td class="text_left">
                            <?=
                                $form->field($model, 'end_time', ['template' => '{input}{error}', 'inputOptions'=>['class'=>'text_value Wdate', 'style'=>'width:150px;', 'onclick'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss"});']])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                  </td>  
                                    
                           <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'special_type_title', ['template' => '{label}']) ?></td>
                                  <td class="text_left">
                                      <?=
                                $form->field($model, 'special_type_title', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                  </td> 
                                  
                            
                                </tr>
                                <tr>
                                    <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'contact', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'contact', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                    </td>
                            
                                    <td align="right">
                                  <span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'contact_mobile', ['template' => '{label}']) ?>
                                  </td><td class="text_left">
                                  <?=
                                $form->field($model, 'contact_mobile', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                  </td>  
                                    
                            <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'product_status', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'product_status', ['template' => '{input}{error}'])->dropDownList($product_status);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                  <td align="right"><span style="color:red;font-weight:bold;">*</span>项目描述</td>  
                                  <td class="text_left" colspan="5">
                                      <?=
                                            $form->field($model, 'description', ['template' => '{input}{error}'])->textarea(['id'=>'company','style'=>"width:688px; height:350px;"]) 
                                        ?>
                                      <input type="button" value="生成模板" onclick="createTemp();" />
                                  </td>
                            
                                </tr>
                        </table>
                </div>
    <input value="<?= $model['id']?>" name="is_edit" type="hidden" />
                <?= $form->field($model, 'updated_at', ['template' => '{input}'])->hiddenInput() ?>
                <?= $form->field($model, 'created_at', ['template' => '{input}'])->hiddenInput() ?>
                <?= $form->field($model, 'id', ['template' => '{input}'])->hiddenInput() ?>
                <?= $form->field($model, 'del_status', ['template' => '{input}'])->hiddenInput() ?>
                <?= $form->field($model, 'creator_id', ['template' => '{input}'])->hiddenInput(['value'=>Yii::$app->user->id]) ?>
                <!--普通提交-->
                <div class="form_submit">
                        <input type="submit" class="button" value="保存"/>
                </div>
        <?php $form->end(); ?>
</div>

<script type="text/javascript">
var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
var i=0;//初始化数组下标	
$(function() {
    $('#file_upload').uploadify({
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/js/uploadify/uploadify.swf',
        'uploader' : '/js/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
	'buttonText' : '选择文件(须有附件)',//设置按钮文本
        //'buttonImg' : 'http://m.fae.comimages/button.png',
        'multi'    : true,//允许同时上传多张图片
        'uploadLimit' : 10,//一次最多只允许上传10张图片
        'fileTypeDesc' : 'Image Files Or Pdf Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg; *.png; *.pdf',//限制允许上传的图片后缀
        'fileSizeLimit' : '20000KB',//限制上传的图片不得超过200KB 
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
                var html = '<tr class="copyfile">'
                           +'     <td></td>'
                           +'     <td colspan="5" >'
                           +'         <div align="left"><input type="text"  class="text_value"  name="name[]" /><a href="/upload/product'+data+'" target="_blank" >'+data+'</a>'
                           +'<input type="hidden" readonly name="content[]" value="'+data+'" /><input type="hidden" name="field_type[]" value="2" />'
                           +'&emsp;<input type="button" value="删除" onclick="del(this,\'.copyfile\');" /></div>'               
                           +'    </td>'
                           +'</tr>';
                   if($('.copyfile').length){
                       $('.copyfile').last().after(html)
                   }else{
                        $('#file').append(html)
                   }
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
           // if(img_id_upload.length>0)
           //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
           //console.log(queueData);
        }  
        // Put your options here
    });
});	

        function del(obj,cla){
            $(obj).parents(cla).detach();
            alert("成功删除");
        }
	function delimg(id,img,obj,cla)
	{
            if($(cla).length==1){
                    alert('请至少保留一项');return false;
                }
		var status=confirm("是否确定删除！");
                //$(obj).parents(cla).detach();return;
		if(status)
		{
			$.get("/product/product/imgdel",{img:img,id:id},function(data)
			{
				if(data)
				{
                                    $(obj).parents(cla).detach();
				    alert("成功删除");
				}
			});
		}
	}
	
	
</script>
<?php $this->endBlock(); ?>