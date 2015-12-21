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
$this->registerJs("var t=1;",1);//在头部加载
//$this->registerJsFile('/js/swfupload/swfupload.js', ['depends' => 'yii\web\YiiAsset']);
//$this->registerJsFile('/js/swfupload/handlers.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile('/js/uploadify/jquery-1.7.2.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/uploadify/jquery.uploadify-3.1.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile('/js/uploadify/uploadify.css',['depends' => 'yii\web\YiiAsset'] );

$this->registerJsFile('/js/product.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
        <div class="info">
                <h3>添加产品</h3>
        </div>
</div>
<div class="tab" id="tab">
        <a class="selected" href="/product/product/index">返回列表</a>
</div>

<div class="page_form">
    <div><span style="color:red;font-weight:bold;">*</span>为必填项</div>
        <?php $form = ActiveForm::begin(['id'=>'product_product_form', 'action' => "/product/product/edit?id=".$model['id'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
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
                              
                                    <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'money', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                                        
                            <?=
                                $form->field($model, 'money', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                        
                                    </td>      
                                    
                                  <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'yield_rate', ['template' => '{label}']) ?></td>
                                  <td class="text_left">
                            <?=
                                $form->field($model, 'yield_rate', ['template' => '{input}%{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                  </td>  
                                    
                           <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'product_duration', ['template' => '{label}']) ?></td>
                                  <td class="text_left">
                            <?=
                                $form->field($model, 'product_duration', ['template' => '{input}{error}'])->textInput(['style'=>'width:100px;','class'=>'text_value']) 
                            ?>
                            <?=
                                $form->field($model, 'product_duration_type', ['template' => '{input}'])->dropDownList($duration_type) 
                            ?>
                                  </td> 
                                  
                            
                                </tr>
                                <tr>
                                    <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'start_money', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'start_money', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                    </td>
                            
                                    
                                    
                            <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'product_status', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'product_status', ['template' => '{input}{error}'])->dropDownList($product_status);
                            ?>
                                    </td>
                                  <td align="right">
                                  <?= $form->field($model, 'home_status', ['template' => '{label}']) ?>
                                  </td><td class="text_left">
                                  <?=
                                $form->field($model, 'home_status', ['template' => '{input}{error}'])->radioList([0=>"不展示",1=>'展示']);
                            ?>
                                  </td>  
                           
                            
                                </tr>
                                
                                <tr>
                                    <td colspan="6" align="left" style="text-align: left; font-size: 16px">
                                        资金监管账户设置<font color="red" style="font-weight: bold">以下三项如有任何一项没有填写，资金监管账户在前台将不会显示</font>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td align="right"><span style="color:red;font-weight:bold;">*</span><?= $form->field($model, 'account_name', ['template' => '{label}']) ?></td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'account_name', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) 
                            ?>
                                    </td>
                            
                                    
                                    
                            <td align="right"><span style="color:red;font-weight:bold;">*</span>银行账号</td>
                                    <td class="text_left">
                            <?=
                                $form->field($model, 'account', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) ;
                            ?>
                                    </td>
                                  <td align="right">
                                  <?= $form->field($model, 'bank', ['template' => '{label}']) ?>
                                  </td><td class="text_left">
                                  <?=
                                $form->field($model, 'bank', ['template' => '{input}{error}'])->textInput(['style'=>'width:200px;','class'=>'text_value']) ;
                            ?>
                                  </td>  
                           
                            
                                </tr>
                                
                                
                                <tr>
                                    <td colspan="6" align="left" style="text-align: left; font-size: 16px">
                                        备查文件设置
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center' colspan="3" style="text-align: center; font-size: 16px">
                                       <input type="file" name="file_upload" id="file_upload" />
                                    </td>
                                    <td align='center' colspan="3" style="text-align: center; font-size: 16px">
                                       <input type="button" class="button" style="margin: 0px;" value="增加中心留档备查文件(无附件)" onclick="copyFileRow(this);"/>
                                    </td>
                                </tr>                                
                                <tr>
                                    <td colspan="3" style="border-right:1px solid black">
                                        <table id="file">
                                            <?php
                                                     foreach($pro_field_model as $key=>$val)
                                                     {
                                                         if($val['field_type']==2&&!empty($val['content'])){
                                                 ?>
                                                     <tr class="copyfile">
                                                             <td></td>

                                                             <td colspan="5" class="text_left">

                                                                 <input type="text" name="name[]" class="text_value" style="width: 100px;" value="<?=$val['name'] ?>"  />
                                                                 <a href="/upload/product/<?=$val['content'] ?>" target="_blank"><?=$val['content'] ?></a>
                                                                 <input name="content[]" type="hidden" style="width:200px;" value="<?=$val['content'] ?>" />
                                                                     <input name="field_type[]" type="hidden" value="2" />
                                                                     &emsp;<input type="button" value="删除" onclick="delimg(<?=$val['id'] ?>,'<?=$val['content'] ?>',this,'.copyfile')"/>
                                                             </td>
                                                     </tr>
                                                  <?php
                                                         }
                                                     }
                                                  ?> 
                                                          
                                        </table>
                                    </td>
                                    <td colspan="3">
                                        
                                        <table>
                                            <?php
                                                $fcount = 0;
                                                     foreach($pro_field_model as $key=>$val)
                                                     {
                                                         if($val['field_type']==2&&empty($val['content'])){
                                                 ?>
                                                     <tr class="copyfile_no">
                                                             <td></td>

                                                             <td colspan="5" class="text_left">

                                                                 <input type="text" name="name[]" class="text_value" style="width: 100px;" value="<?=$val['name'] ?>"  />
                                                                 <a href="/upload/product/<?=$val['content'] ?>" target="_blank"><?=$val['content'] ?></a>
                                                                 <input name="content[]" type="hidden" style="width:200px;" value="<?=$val['content'] ?>" />
                                                                     <input name="field_type[]" type="hidden" value="2" />
                                                                     &emsp;<input type="button" value="删除" onclick="delimg(<?=$val['id'] ?>,'<?=$val['content'] ?>',this,'.copyfile_no')"/>
                                                             </td>
                                                     </tr>
                                                  <?php
                                                  $fcount++;
                                                         }
                                                     }
                                                  ?> 

                                                     <?php if($fcount==0){ ?>
                                                     <tr class="copyfile_no">
                                                         <td></td>
                                                         <td colspan="5" >
                                                             <div align="left">
                                                                 <input  class="text_value"  type="text" name="name[]" />
                                                                 <input  class="text_value"  type="hidden" name="content[]" />
                                                                 <input type="hidden" name="field_type[]" value="2" />

                                                             </div>
                                                         </td>

                                                     </tr>    
                                                     <?php }?>
                                        </table>
                                        
                                    </td>
                                </tr>
                           
                                
                               
                                <tr>
                                        <td align="right">
                                            可变信息录入
                                        </td>
                                        <td colspan="5" style="text-align:left;"><input type="button" style="margin: 0px;" class="button" value="增加一个新字段" onclick="copyRow(this);"/></td>
                                </tr>

                            <?php
                                foreach($pro_field_model as $key=>$val)
                                {
                                    if($val['field_type']==1){
                            ?>
                                <tr class="copy">
                                        <td></td>
                            
                                        <td colspan="5" class="text_left">
                                  
                                            <input type="text" name="name[]" class="text_value" style="width: 100px;" value="<?=$val['name'] ?>"  />：
                                                <input name="content[]" type="text" class="text_value" style="width:200px;" value="<?=$val['content'] ?>" />
                                                <input name="field_type[]" type="hidden" value="1" />
                                                &emsp;<input type="button" value="删除" onclick="delimg(<?=$val['id'] ?>,'<?=$val['content'] ?>',this,'.copy')"/>
                                        </td>
                                </tr>
                             <?php
                                    }
                                }
                             ?> 
                                <?php
                                if(($is_show_row==1)){
                                ?>
                                <tr class="copy">
                                        <td></td>
                            
                                        <td colspan="5" class="text_left">
                                  
                                            <input type="text" name="name[]" class="text_value" style="width: 100px;" value=""  />：
                                                <input name="content[]" type="text" class="text_value" style="width:200px;" value="" />
                                                <input name="field_type[]" type="hidden" value="1" />
                                        </td>
                                </tr>
                                <?php
                                    }
                                 ?>
                                <tr>
                            <?=
                                $form->field($model, 'description', ['template' => '<td align="right"><span style="color:red;font-weight:bold;">*</span>{label}</td><td class="text_left" colspan="5">{input}{error}</td>'])->textarea(['id'=>'company','style'=>"width:688px; height:350px;"]) 
                            ?>
                                    
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