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

 *  */
$this->registerCssFile('/kindeditor/themes/default/default.css', ['depends' => 'yii\web\YiiAsset','position'=>1]);
$this->registerJsFile('/kindeditor/kindeditor-min.js', ['depends' => 'yii\web\YiiAsset','position'=>1]);
$this->registerJsFile('/kindeditor/lang/zh_CN.js', ['depends' => 'yii\web\YiiAsset','position'=>1]);
$this->registerJs("var t=1;", 1); //在头部加载

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/product.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
                <h3 class="page-title">
                        贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/product/productonline/list">贷款管理</a>
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/product/productonline/list">项目列表</a>
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="javascript:void(0)">添加项目</a>
                        </li>
                </ul>
        </div>
    </div>

    <!--项目编辑区域 start-->
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <?php $form = ActiveForm::begin(['id' => 'product_product_form', 'action' => "/product/productonline/edit?id=" . $model['id'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?=
                $form->field($model, 'refund_method', ['template' => '{input}', 'inputOptions'=>['value'=>"1"]])->hiddenInput()
            ?>
            <h3 class="form-section">项目基本信息</h3>
                <div class="row-fluid">
                        <div class="span12 ">
                                <div class="control-group">
                                        <label class="control-label">项目名称</label>
                                        <div class="controls">
                                                <?=
                                                $form->field($model, 'title', ['template' => '{input}{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'项目名称']])->textInput(['class' => "m-wrap span12"])
                                                ?>
                                        </div>
                                </div>
                        </div>
                </div>
            <?php if($model->id){ ?>
                <div class="row-fluid">
                        <div class="span12 ">
                                <div class="control-group">
                                        <label class="control-label">项目编号</label>
                                        <div class="controls">
                                                <?=$model->sn?>
                                        </div>
                                </div>
                        </div>
                </div>
            <?php } ?>
                <div class="row-fluid">
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">项目利率</label>
                                        <div class="controls">

                                                    <?=
                                                    $form->field($model, 'yield_rate', ['template' => '<div class="input-append">{input}<span class="add-on">%</span> </div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'项目利率']])->textInput(['class' => "m-wrap span12"])
                                                    ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">项目天数</label>
                                        <div class="controls">

                                                    <?=
                                                    $form->field($model, 'expires', ['template' => '<div class="input-append">{input}<span class="add-on">(天)</span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'项目天数']])->textInput(['class' => "m-wrap span12"])
                                                    ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                </div>
                <div class="row-fluid">
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">项目截止日</label>
                                        <div class="controls">

                                                        <?=
                                                        //$form->field($model, 'finish_date', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'项目截止日']])->textInput(['readonly' => 'readonly', 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.  date("Y-m-d").'\'});','class' => "m-wrap span12"])
                $form->field($model, 'finish_date', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="WdatePicker({el:\'onlineproduct-finish_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'项目截止日']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.  date("Y-m-d").'\'});'])
                                                        ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">募集金额</label>
                                        <div class="controls">

                                                    <?=
                                                    $form->field($model, 'money', ['template' => '<div class="input-prepend input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'募集金额']])->textInput(['class' => "m-wrap span12"])
                                                    ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                </div>

                 <div class="row-fluid">
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">募集开始时间</label>
                                        <div class="controls">

                                                        <?=
                                                        $form->field($model, 'start_date', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="WdatePicker({el:\'onlineproduct-start_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'募集开始时间']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.  date("Y-m-d").'\'});'])
                                                        ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">募集结束时间</label>
                                        <div class="controls">

                                                        <?=
                                                        $form->field($model, 'end_date', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="WdatePicker({el:\'onlineproduct-end_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'募集结束时间']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.  date("Y-m-d").'\'});'])
                                                        ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                </div>
                <?php if(!$model->is_jixi) { ?>
                <div class="row-fluid">
                      <div class="span6 ">
                            <div class="control-group">
                                    <label class="control-label">计息开始日</label>
                                    <div class="controls">

                                        <?=
$form->field($model, 'jixi_time', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'计息开始日']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd",minDate:"#F{$dp.$D(\'onlineproduct-start_date\')}",maxDate:"#F{$dp.$D(\'onlineproduct-finish_date\')}"});'])
                                        ?>

                                    </div>
                            </div>
                       </div>
                </div>
                <?php } ?>

                <div class="row-fluid">
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">融资用户</label>
                                        <div class="controls">
                                                <?=
$form->field($model, 'borrow_uid', ['template' => '{input}{error}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'chosen-with-diselect span6']])->dropDownList($rongziInfo,[0=>'--请选择--'])
                                                ?>
                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">分销渠道</label>
                                        <div class="controls">
                                                <?=
                                                $form->field($model, 'channel', ['template' => '{input}{error}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'chosen-with-diselect span6']])->dropDownList(Yii::$app->params['deal']['channel'])
                                                ?>
                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                </div>
                <!--/row-->
                <div class="row-fluid">
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">起投金额</label>
                                        <div class="controls">

                                            <?=
                                            $form->field($model, 'start_money', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'起投金额,必须是1的整数倍','value'=>  intval($model->start_money)]])->textInput(['class' => "m-wrap span12"])
                                            ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                        <div class="span6 ">
                                <div class="control-group">
                                        <label class="control-label">递增金额</label>
                                        <div class="controls">

                                                    <?=
                                                    $form->field($model, 'dizeng_money', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'递增金额,必须是1的整数倍','value'=>  intval($model->dizeng_money)]])->textInput(['class' => "m-wrap span12"])
                                                    ?>

                                        </div>
                                </div>
                        </div>
                        <!--/span-->
                </div>


                <h3 class="form-section">项目合同信息</h3>
                <?=
                $form->field($model, 'contract_type', ['template' => '{error}']);
                ?>
                <?php if(empty($pid)) { ?>
                <div class="row-fluid">
                        <div class="span12 ">
                                <div class="control-group">
                                        <label class="control-label">合同标题</label>
                                        <div class="controls">
                                            <input type="text" id="contracttemplate-name" class="m-wrap span12" name="name[]" autocomplete="off" placeholder="合同标题">
                                        </div>
                                </div>
                        </div>
                </div>

                <div class="row-fluid ctemp">
                        <div class="span12 ">
                                <div class="control-group">
                                        <label class="control-label">合同内容</label>
                                        <div class="controls">
                                            <textarea class="m-wrap span12 new_template" name="content[]"></textarea>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php }else{ foreach($ctmodel as $key => $val){ ?>
                <div id='con_<?= $key ?>'>
                    <div class="row-fluid">
                            <div class="span12 ">
                                    <div class="control-group">
                                            <label class="control-label">合同标题</label>
                                            <div class="controls">
                                                <input type="text" id="contracttemplate-name" class="m-wrap span12" name="name[]" autocomplete="off" placeholder="合同标题" value="<?= $val['name'] ?>">
                                            </div>
                                    </div>
                            </div>
                    </div>

                    <div class="row-fluid ctemp">
                            <div class="span12 ">
                                    <div class="control-group">
                                            <label class="control-label">合同内容</label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 new_template" name="content[]"><?= $val['content'] ?></textarea>
                                            </div>
                                    </div>
                            </div>
                    </div>

                    <div class="row-fluid">
                            <div class="span12 ">
                                    <div class="control-group">
                                        <label class="control-label" style="text-align: right;"><a href="javascript:void(0);" onclick="delcontract(this);" style='color:red' data-con='con_<?= $key ?>'>删除该条合同信息</a></label>
                                    </div>
                            </div>
                    </div>
                </div>
                <?php } } ?>
                <div class="row-fluid" id="insert_con">
                        <div class="span12 ">
                                <div class="control-group">
                                    <label class="control-label" style="text-align: right;"><a href="javascript:void(0);" onclick="inscontract();">新增其他合同</a></label>
                                </div>
                        </div>
                </div>

                <!--/row-->

                <h3 class="form-section">项目描述信息</h3>
                <div class="row-fluid">
                        <div class="span12 ">
                                <div class="control-group">
                                        <label class="control-label">项目描述</label>
                                        <div class="controls">
                                            <?=
                                            $form->field($model, 'description', ['template' => '{input}{error}'])->textarea(['id' => 'company', 'class' => "m-wrap span12"])
                                            ?>

                                        </div>
                                </div>
                        </div>
                </div>


                <div class="form-actions">
                    <?=
                        $form->field($model, 'status', ['template' => '{error}'])->textInput();
                        ?>
                        <button id="product-submit" type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                        <a href="/product/productonline/list" class="btn">取消</a>

                </div>

        <?php $form->end(); ?>

        <!-- END FORM-->

</div>
    <!--end-->
</div>
<script type="text/javascript">
    $(function() {
        $('#product_product_form').submit(function() {
            $(this).find('button[type=submit]').attr('disabled', true);
        });
         kindEdit();
    });

     function kindEdit() {
        var editor = KindEditor.create('.new_template', {
                cssPath: '/kindeditor/plugins/code/prettify.css',
                fileManagerJson: '',
                uploadJson: '/kindeditor/editor.php',
                allowFileManager: true,
                filterMode: false,
                items: ["source", "|", "preview", "print", "cut", "copy", "paste", "plainpaste", "wordpaste",
                        "|", "justifyleft", "justifycenter", "justifyright", "justifyfull", "insertorderedlist",
                        "insertunorderedlist", "indent", "outdent", "clearhtml", "quickformat", "selectall", "|",
                        "fullscreen", "/", "formatblock", "fontname", "fontsize", "|", "forecolor", "hilitecolor",
                        "bold", "italic", "underline", "strikethrough", "lineheight", "removeformat", "|", "table"],
                afterUpload: function(url, data) {
                    $('#file_id').val($('#file_id').val() + data.id + ',');
                }
            });
    }

    var flag = 0;
    function inscontract() {
        var html = "";
        flag++;
        html =  '<div id=\'ins_con'+flag+'\'>' +
                '<div class="row-fluid">' +
                    '<div class="span12 ">' +
                            '<div class="control-group">' +
                                    '<label class="control-label">合同标题</label>' +
                                    '<div class="controls">' +
                                         '<input type="text" id="contracttemplate-name" class="m-wrap span12" name="name[]" autocomplete="off" placeholder="合同标题">' +
                                    '</div>' +
                            '</div>' +
                    '</div>' +
            '</div>' +
            '<div class="row-fluid ctemp">' +
                    '<div class="span12 ">' +
                            '<div class="control-group">' +
                                    '<label class="control-label">合同内容</label>' +
                                    '<div class="controls">' +
                                        '<textarea class="m-wrap span12 new_template" name="content[]"></textarea>' +
                                    '</div>' +
                            '</div>' +
                    '</div>' +
            '</div>' +
            '<div class="row-fluid">' +
                    '<div class="span12 ">' +
                            '<div class="control-group">' +
                                '<label class="control-label" style="text-align: right;"><a href="javascript:void(0);" onclick="delcontract(this)" data-con=\'ins_con'+flag+'\' style="color:red">删除该条合同信息</a></label>' +
                            '</div>' +
                    '</div>' +
            '</div>' +
            '</div>';
        $('#insert_con').before(html);
        kindEdit();
    }

    function delcontract(obj)
    {
        var id = $(obj).attr("data-con");
        layer.confirm('确定此操作吗？', function (index) {
            $("#"+id).detach();
            layer.close(index);
        });
    }
</script>
<?php $this->endBlock(); ?>
