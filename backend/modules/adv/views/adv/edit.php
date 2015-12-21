<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->registerJs("var t=0;",1);//在头部加载  0:adv 1:product
$this->registerJsFile('/js/swfupload/swfupload.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/swfupload/handlers.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        运营管理 <small>运营管理模块【主要包含广告管理】</small>
                </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/adv/adv/index">广告管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/adv/adv/index">首页轮播</a>
                        <i class="icon-angle-right"></i>                    
                    </li>
                    <li>
                        <a href="javascript:void(0);">添加</a>                    
                    </li>
            </ul>
        </div>
        

<div class="portlet-body form">
        <?php $form = ActiveForm::begin(['id' => 'adv_form', 'action' => "/adv/adv/edit?id=" . $model->id,'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
    
     

    <?php if($model->id){ ?>
    <div class="control-group">
        <label class="control-label">序号</label>
        <div class="controls"><?= $model->sn ?></div>
    </div>
    <?php } ?>
    
    
    <div class="control-group">
        <label class="control-label">标题</label>
        <div class="controls">                           
                <?= $form->field($model, 'title', ['template' => '{input}','inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'标题']])->textInput() ?>
                <?= $form->field($model, 'title', ['template' => '{error}']) ?>
             </div>
    </div>
    
    
    
    <div class="control-group">
        <label class="control-label">描述</label>
        <div class="controls">                           
                <?= $form->field($model, 'description', ['template' => '{input}','inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'描述']])->textarea(['rows'=>3]) ?>
                <?= $form->field($model, 'description', ['template' => '{error}']) ?>
             </div>
    </div>
    
    
    
<div class="control-group">
    <label class="control-label">上传图片</label>
    <div class="controls">   
        <?= $form->field($model, 'image', ['template' => '{input}'])->hiddenInput(['id'=>"hidimage"]) ?>
            <div style="width: 180px; height: 18px; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;">
                <span id="spanButtonPlaceholder_baoli"></span>
            </div>
            <div id="divFileProgressContainer_baoli" style="height: 10px; display:none"></div>
            <div id='thumbnails_baoli'>
                <?php if($model->id) {?>
                <img src="/upload/adv/<?=$model->image ?>" style="margin: 5px; vertical-align: middle; width: 100px; height: 100px; opacity: 1;">
                <?php }?>
            </div>
    </div>
    <div class="controls"> 
        <span style="color:red">图片大小不超过2M，只限于jpg格式图片,并且大小限定为：高350px,宽750px
            <?= $form->field($model, 'image', ['template' => '{error}']) ?>
        </span>
    </div>
</div>
    
    <div class="control-group">
        <label class="control-label">链接</label>
        <div class="controls">                           
                <?= $form->field($model, 'link', ['template' => '{input}','inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'链接']])->textarea(['rows'=>3]) ?>
                <?= $form->field($model, 'link', ['template' => '{error}']) ?>
             </div>
    </div>
    
    
    <div class="control-group">
        <label class="control-label">显示顺序</label>
        <div class="controls">                           
            <?= $form->field($model, 'show_order', ['template' => '{input}','inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'显示顺序']])->textarea(['rows'=>3]) ?>
            <?= $form->field($model, 'show_order', ['template' => '{error}']) ?>
        </div>
    </div>
                              
        <!--普通提交-->
        <div class="form-actions">

                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>

                    <a href="/adv/adv/index" class="btn">取消</a> 

        </div>
        <?php $form->end(); ?> 
    </div>
                                    
</div>

<!--<style type="text/css">
    .h{
        display:none;
    }
    .text_left{
        margin-left: 20px;
    }
</style>-->
<script type="text/javascript">
//    showPosInfo(1);
//    $('#adv-pos_id').change(function(){
//        showPosInfo($(this).val())
//    });
//    
//    function showPosInfo(i){
//        $('.h').hide();
//        $('#l_'+i).show();
//    }
    
	var swfu;
	var swfu_baoli;
	var swfu_gongguan;
	window.onload = function () 
	{
		swfu_baoli = new SWFUpload({
			// Backend Settings
			upload_url: "/js/swfupload/upload_baoli.php?type=adv",
			post_params: {"PHPSESSID": "<?= rand(time(),  time()+  time()) ?>"},
			file_size_limit : "2 MB",
			file_types : "*.jpg;*.gif;*.png",
			file_types_description : "JPG Images; PNG Image",
			file_upload_limit : 1,
			swfupload_preload_handler : preLoad,
			swfupload_load_failed_handler : loadFailed,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			button_image_url : "/js/swfupload/SmallSpyGlassWithTransperancy_17x18.png",
			button_placeholder_id : "spanButtonPlaceholder_baoli",
			button_width: 180,
			button_height: 18,
			button_text : '<span class="button">选择图片 <span class="buttonSmall">(2 MB Max)</span></span>',
			button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
			button_text_top_padding: 0,
			button_text_left_padding: 18,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,
			flash_url : "/js/swfupload/swfupload.swf",
			flash9_url : "/js/swfupload/swfupload_fp9.swf",

			custom_settings : 
			{
                            upload_target : "divFileProgressContainer_baoli",
			},
			debug: false
		});
		
		
			

		
	};
	
	function delimg(id,img,obj)
	{
		var status=confirm("是否确定删除！");
		if(status)
		{
			$.get("/adv/adv/imgdel",{img:img,id:id},function(data)
			{
				if(data)
				{
                                    $(obj).parent().find('img').detach();
                                     var stats = swfu_baoli.getStats();
                        stats.successful_uploads--;
                        swfu_baoli.setStats(stats);
				    alert("成功删除");
				}
			});
		}
	}
	
$(function(){ 
    $('.ajax_button').click(function(){
       vals = $("#adv_form").serialize();
        $.post($("#adv_form").attr("action"), vals, function (data) {  
            
           res(data,"/adv/adv/index");
            
        });  
    })

});	
</script>

<?php $this->endBlock(); ?>