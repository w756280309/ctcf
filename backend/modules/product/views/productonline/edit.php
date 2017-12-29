<?php

$this->title = '标的'.($model->id ? '编辑' : '添加');

use PayGate\Cfca\CfcaUtils;
use yii\helpers\ArrayHelper;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
$this->registerCssFile('/vendor/kindeditor/4.1.11/themes/default/default.css');
$this->registerCssFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.css');
$this->registerJsFile('/vendor/kindeditor/4.1.11/kindeditor-all-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/lang/zh-CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.js', ['depends' => 'yii\web\YiiAsset']);

$readonly = $model->online_status ? ['readonly' => 'readonly'] : [];
$disabled = $model->online_status ? ['disabled' => 'disabled'] : [];

$desc = '*项目上线后此内容不可修改';

$tpl = <<<TPL
<div class="row-fluid">
    <div class="span12 ">
        <div class="control-group">
            <label class="control-label">{{ contentName }}</label>
            <div class="controls">
                <input type="text" id="contracttemplate-name" class="m-wrap span12" name="name[]" autocomplete="off" placeholder="合同标题" value='{{ name }}'>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid ctemp">
    <div class="span12 ">
        <div class="control-group">
            <label class="control-label">{{ contentDetail }}</label>
            <div class="controls">
                <textarea class="m-wrap span12 new_template" name="content[]">{{ content }}</textarea>
            </div>
        </div>
    </div>
</div>
TPL;

$tpl2 = <<<TPL
<div id='con_{{ key }}'>
    <div class="row-fluid">
        <div class="span12 ">
            <div class="control-group">
                <label class="control-label">合同标题</label>
                <div class="controls">
                    <input type="text" id="contracttemplate-name" class="m-wrap span12" name="name[]" autocomplete="off" placeholder="合同标题" value="{{ name }}">
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid ctemp">
        <div class="span12 ">
            <div class="control-group">
                <label class="control-label">合同内容</label>
                <div class="controls">
                    <textarea class="m-wrap span12 new_template" name="content[]">{{ content }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 ">
            <div class="control-group">
                <label class="control-label" style="text-align: right;"><a href="javascript:void(0);" onclick="delcontract(this);" style='color:red' data-con='con_{{ key }}'>删除该条合同信息</a></label>
            </div>
        </div>
    </div>
</div>
TPL;

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
        <?php $form = ActiveForm::begin(['id' => 'product_product_form', 'action' => '/product/productonline/'.($model->id ? 'edit?id='.$model->id : 'add'), 'options' => ['enctype' => 'multipart/form-data']]); ?>
        <h3 class="form-section">项目基本信息</h3>
        <?php if ($model->id) {  ?>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目编号</label>
                    <div class="controls">
                        <?= $model->sn ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目名称</label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'title', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '项目名称', 'maxLength' => 32, 'class' => 'm-wrap span12']])->textInput()
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目副标题</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'internalTitle', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '项目副标题（60字以内）', 'class' => 'm-wrap span12']])->textInput()
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">项目类型</label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'cid', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'class' => 'chosen-with-diselect span6']])->dropDownList(['' => '--选择--'] + Yii::$app->params['pc_cat'])
                        ?>
                    </div>
                </div>
            </div>
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">还款方式</label>
                    <div class="controls">
                    <?php
                        $refund_input_option = array_merge([
                            'autocomplete' => 'off',
                            'class' => 'chosen-with-diselect span6 refund_method',
                            'onchange' => 'changeRefmet(this)',
                        ], $disabled);
                    ?>
                    <?=
                        $form->field($model, 'refund_method', [
                            'template' => '{input}{error}',
                            'inputOptions' => $refund_input_option,
                            ])->dropDownList(['' => '--选择--'] + Yii::$app->params['refund_method'])
                    ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">项目利率</label>
                    <div class="controls">
                    <?=
                        $form->field($model, 'yield_rate', [
                            'template' => '<div class="input-append">{input}<span class="add-on">%</span> </div>{error}',
                            'inputOptions' => [
                                'autocomplete' => 'off',
                                'placeholder' => '项目利率',
                                'class' => 'm-wrap span12',
                            ]
                        ])->textInput($readonly)
                    ?>
                    </div>
                </div>
            </div>
            <!--/span-->
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">项目期限</label>
                    <div class="controls">
                    <?=
                        $form->field($model, 'expires', [
                            'template' => '<div class="input-append">{input}<span class="add-on">(天)</span></div>{error}',
                            'inputOptions' => [
                                'autocomplete' => 'off',
                                'placeholder' => '项目期限',
                                'class' => 'm-wrap span12 expires',
                            ]
                        ])->textInput($readonly)
                    ?>
                    </div>
                </div>
            </div>
            <!--/span-->
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">加息</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'jiaxi', ['template' => '<div class="input-append">{input}<span class="add-on">%</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '加息', 'class' => 'm-wrap span12']])->textInput($readonly)
                        ?>
                    </div>
                </div>
            </div>
            <!--/span-->
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">募集金额<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                    <?=
                        $form->field($model, 'money', [
                            'template' => '<div class="input-prepend input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}',
                            'inputOptions' => [
                                'autocomplete' => 'off',
                                'placeholder' => '募集金额',
                                'class' => 'm-wrap span12',
                            ]
                        ])->textInput($disabled)
                    ?>
                    </div>
                </div>
            </div>
            <!--/span-->
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">募集开始时间<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                    <?php
                        $start_date_input_option = array_merge([
                            'autocomplete' => 'off',
                            'placeholder' => '募集开始时间',
                        ], $disabled);
                    ?>
                    <?=
                        $form->field($model, 'start_date', [
                            'template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="WdatePicker({el:\'onlineproduct-start_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}',
                            'inputOptions' => $start_date_input_option
                            ])->textInput([
                                'class' => 'm-wrap span12',
                                'value' => $model->start_date ? date('Y-m-d H:i', $model->start_date) : '',
                                'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.date('Y-m-d').'\'});'
                            ])
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
                        $form->field($model, 'end_date', [
                            'template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="WdatePicker({el:\'onlineproduct-end_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}',
                            'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '募集结束时间']
                            ])->textInput([
                                'class' => 'm-wrap span12',
                                'value' =>  $model->end_date ? date('Y-m-d H:i', $model->end_date) : '',
                                'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.date('Y-m-d').'\'});'
                                ])
                        ?>
                    </div>
                </div>
            </div>
            <!--/span-->
        </div>
        <div class="row-fluid sourceRfmet">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">产品到期日<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?= $form->field($model, 'finish_date', ['template'=>'{input}'])->hiddenInput(['value' => 0, 'id' => 'finish_date_hide'])->label(false)?>
                        <?php
                            $fd_input_option = ['autocomplete' => 'off', 'placeholder' => '产品到期日'];
                            if (!$model->is_fdate || !empty($readonly)) {
                                $fd_input_option = array_merge($fd_input_option, ['readonly' => 'readonly']);
                            }
                        ?>
                        <?=
                            $form->field($model, 'finish_date', [
                                'template' => '<div class="input-append date form_datetime">{input}<span class="add-on" onclick="if ($(\'#onlineproduct-finish_date\').attr(\'readonly\')) {return false;}WdatePicker({el:\'onlineproduct-finish_date\',dateFmt:\'yyyy-MM-dd HH:mm\',minDate:\''.date('Y-m-d').'\'});"><i class="icon-calendar"></i></span></div>{error}',
                                'inputOptions' => $fd_input_option,
                            ])->textInput([
                                'class' => 'm-wrap span12',
                                'value' =>  $model->finish_date ? date('Y-m-d H:i', $model->finish_date) : '',
                                'onclick' => 'if ($(this).attr("readonly")) {return false;}WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.date('Y-m-d').'\'});',
                            ])
                        ?>
                        <?php
                            $fd_input_option = [];
                            if (!empty($disabled)) {
                                $fd_input_option = array_merge($disabled, ['uncheck' => $model->is_fdate]);
                            }
                        ?>
                        <?=
                            $form->field($model, 'is_fdate', [
                                'template' => '{input}',
                                'inputOptions' => [
                                    'autocomplete' => 'on',
                                ],
                            ])->checkbox($fd_input_option)
                        ?>
                    </div>
                </div>
            </div>

            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">项目宽限期</label>
                    <div class="controls">
                    <?php
                        $fd_input_option = ['autocomplete' => 'off', 'placeholder' => '默认0天'];
                        if (!$model->is_fdate || !empty($readonly)) {
                            $fd_input_option = array_merge($fd_input_option, ['readonly' => 'readonly']);
                        }
                    ?>
                    <?=
                        $form->field($model, 'kuanxianqi', ['template' => '{input}'])->hiddenInput(['value' => 0, 'id' => 'kuanxianqi_hide'])->label(false)
                    ?>
                    <?=
                        $form->field($model, 'kuanxianqi', ['template' => '<div class="input-append">{input}<span class="add-on">(天)</span></div>{error}', 'inputOptions' => $fd_input_option])->textInput(['class' => 'm-wrap span6'])
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">融资用户<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?php
                            $borrow_uid_input_option = array_merge([
                                'autocomplete' => 'off',
                                'class' => 'chosen-with-diselect span6',
                            ], $disabled);
                        ?>
                        <?=
                            $form->field($model, 'borrow_uid', [
                                'template' => '{input}{error}',
                                'inputOptions' => $borrow_uid_input_option,
                            ])->dropDownList($rongziInfo, [0 => '--请选择--'])
                        ?>
                    </div>
                </div>
            </div>

            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">定向标用户手机号</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'allowedUids', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '定向标用户手机号']])->textInput(['class' => 'm-wrap span12'])
                        ?>
                        <?php $disable = empty($model->id) ? [] : ['disabled' => 'true', 'uncheck' => $model->isPrivate] ?>
                        <?=
                        $form->field($model, 'isPrivate', ['template' => '<div class="input-append">{input}</div>{error}'])->checkbox($disable)
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">发行方</label>
                    <div class="controls">
                        <?php
                        $change_input_option = array_merge([
                            'autocomplete' => 'off',
                            'class' => 'chosen-with-diselect span6',
                            'onchange' => 'changeIssueName(this)',
                        ], $disabled);
                        ?>
                        <?=
                            $form->field($model, 'issuer', [
                                'template' => '{input}{error}',
                                'inputOptions' => $change_input_option])->dropDownList(ArrayHelper::merge(['' => '--请选择--'], ArrayHelper::map($issuer, 'id', 'name')))
                        ?>
                    </div>
                </div>
            </div>

            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">发行方项目编号</label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'issuerSn', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发行方项目编号']])->textInput(['class' => 'm-wrap span12'])
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid originalBorrower" style="display: none">
            <div class="span6">
                <div class="control-group">
                    <label>底层融资方</label>
                    <div class="controls">
                        <?= $form->field($model, 'originalBorrower', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '底层融资方']])->textInput(['class' => 'm-wrap span6']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">备案金额</label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'filingAmount', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '备案金额']])->textInput(['class' => 'm-wrap span12'])
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">起投金额</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'start_money', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '起投金额,必须是1的整数倍', 'value' => $model->isTest ? $model->start_money : intval($model->start_money)]])->textInput(['class' => 'm-wrap span12'])
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
                        $form->field($model, 'dizeng_money', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '递增金额,必须是1的整数倍', 'value' => $model->isTest ? $model->dizeng_money : intval($model->dizeng_money)]])->textInput(['class' => 'm-wrap span12'])
                        ?>
                    </div>
                </div>
            </div>
        <!--/span-->
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">浮动利率</label>
                    <div class="controls">
                    <?=
                        $form->field($model, 'rateSteps', [
                            'template' => '<div class="input-append">{input}</div>',
                            'inputOptions' => [
                                'placeholder' => '浮动利率',
                                'maxlength' => true,
                            ]
                        ])->textarea(array_merge(['rows'=>'5','cols'=>'250'], $readonly)) ?>
                    <?= $form->field($model, 'rateSteps', ['template' => '{error}']) ?>
                    <?php
                        $rateStepsInput = ['autocomplete' => 'on'];
                        if (!empty($disabled)) {
                            $rateStepsInput = array_merge($rateStepsInput, $disabled, ['uncheck' => $model->isFlexRate]);
                        }
                    ?>
                    <?= $form->field($model, 'isFlexRate')->checkbox($rateStepsInput) ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">浮动利率示例</label>
                    <div class="controls">
                        1000,8<br/>
                        2000,9
                    </div>
                    <p>浮动最低利率应大于项目利率</p>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">赎回申请开放时段</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'redemptionPeriods', [
                            'template' => '<div class="input-append">{input}</div>',
                            'inputOptions' => [
                                'placeholder' => '赎回申请开放时段',
                                'maxlength' => true,
                            ]
                        ])->textarea(array_merge(['rows'=>'5','cols'=>'250'])) ?>
                        <?php
                            $redeemInput = ['autocomplete' => 'on'];
                        ?>
                        <?= $form->field($model, 'redemptionPeriods', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">赎回付款日</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'redemptionPaymentDates', [
                            'template' => '{input}',
                            'inputOptions' => [
                                'placeholder' => '赎回付款日',
                                'maxlength' => true,
                            ]
                        ])->textInput(['class' => 'm-wrap span10']) ?>
                        <?php
                        $redeemInput = ['autocomplete' => 'on'];
                        ?>
                        <?= $form->field($model, 'redemptionPaymentDates', ['template' => '{error}']) ?>
                    </div>
                </div>
                <?= $form->field($model, 'isRedeemable')->checkbox($redeemInput) ?>
            </div>
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">赎回申请开放时段示例</label>
                    <div class="controls">
                        20171210,20171220<br/>
                        20181210,20181220
                    </div>
                    <p>应以英文逗号隔开，若存在多个时段，每个时段需另起一行</p>
                </div>
                <div class="control-group" style="padding-top: 70px;">
                    <label class="control-label">赎回付款日示例</label>
                    <div class="controls">
                        20171210,20171220
                    </div>
                    <p>若存在多个赎回付款日，应以英文逗号隔开</p>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 gudinghk">
                <div class="control-group">
                    <label class="control-label">固定还款日</label>
                    <div class="controls">
                        <?php
                            $paymentDayInputOptions = [];
                            if ($model->is_jixi) {
                                $paymentDayInputOptions = array_merge($paymentDayInputOptions, ['disabled' => 'disabled']);
                            }
                        ?>
                        <?=
                            $form->field($model, 'paymentDay', ['template' => '<div class="input-append">{input}<span class="add-on">(日)</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '固定还款日', 'class' => 'm-wrap span12 gdhk', 'value' => empty($model->paymentDay) ? 20 : $model->paymentDay]])->textInput($paymentDayInputOptions)
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">允许使用代金券<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?= $form->field($model, 'allowUseCoupon')->checkbox(array_merge(['autocomplete' => 'on'], $model->online_status ? ['disabled' => 'disabled', 'uncheck' => $model->allowUseCoupon] : []))->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">测试标的</label>
                    <div class="controls">
                        <?=  $form->field($model, 'isTest')->checkbox(['autocomplete' => 'on'])->label(false)?>
                    </div>
                </div>
            </div>
            <div class="span6 fix-offset">
                <div class="control-group">
                    <label class="control-label">允许使用加息券<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?= $form->field($model, 'allowRateCoupon')->checkbox(array_merge(['autocomplete' => 'on'], $model->online_status ? ['disabled' => 'disabled', 'uncheck' => $model->allowRateCoupon] : []))->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="span6">
                <div class="control-group">
                    <label class="control-label">新手专享标<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?=  $form->field($model, 'is_xs')->checkbox(array_merge(['autocomplete' => 'on'], $model->online_status ? ['disabled' => 'disabled', 'uncheck' => $model->is_xs] : []))->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="span6 fix-offset">
                <div class="control-group">
                    <label class="control-label">项目标签<span style="color:grey">(每个标签不超过四个字，且用中文逗号隔开)</span></label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'tags', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '标签列表']])->textInput(['class' => 'm-wrap span12'])
                        ?>
                    </div>
                </div>
            </div>

            <div class="span6">
                <div class="control-group">
                    <label class="control-label">理财计划<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?=  $form->field($model, 'isLicai')->checkbox(array_merge(['autocomplete' => 'on'], $model->online_status ? ['disabled' => 'disabled', 'uncheck' => $model->isLicai] : []))->label(false) ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">积分倍数<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?=  $form->field($model, 'pointsMultiple')->textInput(array_merge(['autocomplete' => 'off', 'value' => $model->pointsMultiple, 'class' => 'm-wrap span2'], $model->online_status ? ['disabled' => 'disabled'] : []))->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="span6 fix-offset">
                <div class="control-group">
                    <label class="control-label">允许转让</label>
                    <div class="controls">
                        <?=  $form->field($model, 'allowTransfer')->checkbox($model->online_status ? ['disabled' => true, 'uncheck' => $model->allowTransfer] : [])->label(false)?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">是否是自定义还款</label>
                    <div class="controls">
                        <?=  $form->field($model, 'isCustomRepayment')->checkbox($model->online_status ? ['disabled' => true, 'uncheck' => $model->isCustomRepayment] : [])->label(false)?>
                    </div>
                </div>
            </div>
            <div class="span6  fix-offset">
                <div class="control-group">
                    <label class="control-label">指定累计资金额用户以上可见<span style="color:grey">（累计资金额=累计投资额+账户余额）</span></label>
                    <?=
                    $form->field($model, 'balance_limit', ['template' => '<div class="input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入累计资金额', 'value' => $model->balance_limit > 0 ? $model->balance_limit : '']])->textInput(['class' => 'm-wrap span12']) ?>
                </div>
            </div>
            <div class="span6">
                <div class="control-group">
                    <label class="control-label">资产包编号<span style="color:grey">(<?= $desc ?>)</span></label>
                    <div class="controls">
                        <?=  $form->field($model, 'pkg_sn')->textInput(array_merge(['autocomplete' => 'off', 'value' => $model->pkg_sn, 'class' => 'm-wrap span12'], $model->online_status ? ['disabled' => 'disabled'] : []))->label(false) ?>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="form-section">项目合同信息</h3>
        <?=
        $form->field($model, 'contract_type', ['template' => '{error}']);
        ?>
        <?php
            if ($ctmodel && !$model->hasErrors()) {
                echo CfcaUtils::renderXml($tpl, [
                    'contentName' => '认购协议标题',
                    'contentDetail' => '认购协议内容',
                    'name' => $ctmodel[0]['name'],
                    'content' => $ctmodel[0]['content'],
                ]);
                echo CfcaUtils::renderXml($tpl, [
                    'contentName' => '合同标题',
                    'contentDetail' => '合同内容',
                    'name' => $ctmodel[1]['name'],
                    'content' => $ctmodel[1]['content'],
                ]);
                foreach ($ctmodel as $key => $val) {
                   if (in_array($key, [0, 1])) {
                       continue;
                   }
                   echo CfcaUtils::renderXml($tpl2, [
                       'key' => $key,
                       'name' => $val['name'],
                       'content' => $val['content'],
                   ]);
                }
            } else {
                echo CfcaUtils::renderXml($tpl, [
                    'contentName' => '认购协议标题',
                    'contentDetail' => '认购协议内容',
                    'name' => $con_name_arr[0],
                    'content' => $con_content_arr[0],
                ]);
                echo CfcaUtils::renderXml($tpl, [
                    'contentName' => '合同标题',
                    'contentDetail' => '合同内容',
                    'name' => $con_name_arr[1],
                    'content' => $con_content_arr[1],
                ]);
                if (!empty($con_name_arr)) {
                    foreach ($con_name_arr as $key => $val) {
                        if (in_array($key, [0, 1])) {
                            continue;
                        }
                        echo CfcaUtils::renderXml($tpl2, [
                            'key' => $key,
                            'name' => $val,
                            'content' => $con_content_arr[$key],
                        ]);
                    }
                }
            }
        ?>
        <div class="row-fluid" id="insert_con">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label" style="text-align: right;"><a href="javascript:void(0);" onclick="inscontract();">新增其他合同</a></label>
                </div>
            </div>
        </div>

        <h3 class="form-section">项目描述信息</h3>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目描述</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'description', ['template' => '{input}{error}'])->textarea(['id' => 'company', 'class' => 'm-wrap span12'])
                        ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <?= $form->field($model, 'status', ['template' => '{error}'])->textInput(); ?>
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
        var $productForm = $('#product_product_form');
        var $submit = $productForm.find('button[type=submit]');
        var originalBorrower = $("#onlineproduct-issuer option:selected").text();
        originalBorrower = originalBorrower.replace(/(^\s*)|(\s*$)/g,"");
        if(parseInt($("#onlineproduct-refund_method").val())===1){
            $('#onlineproduct-kuanxianqi').removeAttr('readonly');
        }
        KindEditor.ready(function(K) {
            K.create('#company', {
                cssPath: '/vendor/kindeditor/4.1.11/plugins/code/prettify.css',
                fileManagerJson: '',
                uploadJson: '/kindeditor/editor.php?baseurl='+'<?= urlencode(UPLOAD_BASE_URI) ?>',
                allowFileManager: true,
                filterMode: false,
                afterUpload: function(url, data) {
                    $('#file_id').val($('#file_id').val() + data.id + ',');
                },
                afterCreate : function() {
                    this.sync();
                },
                afterBlur:function(){
                    this.sync();
                }
            });

            kindEdit();
        });

        $productForm.submit(function() {
            $submit.attr('disabled', true);
        }).on('afterValidate', function() {
            if (!$(this).data('yiiActiveForm').validated) {
                $submit.attr('disabled', false);
            }
        });

        if ($('.refund_method').val() >= '6') {
            $('.gudinghk').show();
        } else {
            $('.gudinghk').hide();
        }

        if(originalBorrower == '深圳立合旺通商业保理有限公司') {
            $(".originalBorrower").show()
        } else {
            $(".originalBorrower").hide()
        }

        //是否使用截止日期关系着产品到期日和宽限期天数的设置。如果勾选，可以填写截止日和宽限期，否则不可以填写
        $('#onlineproduct-is_fdate').bind('click', function() {
            if (true === $(this).parent().hasClass('checked')) {
                disabledIsFdate(1);
            } else {
                disabledIsFdate(0);
            }
        });

        <?php if ((int)$model->refund_method > 1 && !$model->isDailyAccrual) { ?>
            $('#onlineproduct-expires').next().html('(个月)');//当编辑项目的还款方式是：除了到期本息之外的任意的还款方式。单位都默认是个月
        <?php } ?>

        //是否使用浮动利率设置。如果勾选，浮动利率可以填写。
        var isFlexRate = $('#onlineproduct-isflexrate');
        var rateSteps = $('#onlineproduct-ratesteps');
        var isFlex = <?= intval($model->isFlexRate)?>;
        if(!isFlex){
            rateSteps.val('');
            rateSteps.attr('disabled', 'disabled');
        }
        isFlexRate.bind('click', function () {
            if (true === $(this).parent().hasClass('checked')) {
                rateSteps.val('');
                rateSteps.attr('disabled', 'disabled');
            } else {
                rateSteps.removeAttr('disabled');
            }
        });

        //是否使用主动赎回设置
        var isRedeemableBox = $('#onlineproduct-isredeemable');
        var redemptionPeriods = $('#onlineproduct-redemptionperiods');
        var redemptionPaymentDates = $('#onlineproduct-redemptionpaymentdates');
        var isRedeemable = <?= intval($model->isRedeemable)?>;
        if (!isRedeemable) {
            redemptionPeriods.val('');
            redemptionPaymentDates.val('');
            redemptionPeriods.attr('disabled', 'disabled');
            redemptionPaymentDates.attr('disabled', 'disabled');
        }
        isRedeemableBox.on('click', function () {
            if (true === $(this).parent().hasClass('checked')) {
                redemptionPeriods.val('');
                redemptionPeriods.attr('disabled', 'disabled');
                redemptionPaymentDates.attr('disabled', 'disabled');
            } else {
                redemptionPeriods.removeAttr('disabled');
                redemptionPaymentDates.removeAttr('disabled');
            }
        });
    });

    //选择还款方式是到期本息的可以设置产品到期日以及宽限期。否则不可以设置
    function changeRefmet(obj)
    {
        $('.expires').val('');
        if (1 === parseInt($(obj).val())) {
            $('#onlineproduct-expires').next().html('(天)');
        } else {
            $('#onlineproduct-expires').next().html('(个月)');
        }

        $('.gdhk').val(20);
        if ($(obj).val() >= '6') {
            $('.gudinghk').show();
        } else {
            $('.gudinghk').hide();
        }

        if ($(obj).val() === '1') {
            $('#onlineproduct-kuanxianqi').removeAttr('readonly');
        } else {
            $('#onlineproduct-kuanxianqi').attr('readonly', 'readonly');
        }
    }
    //当发行方选择为‘深圳立合旺通商业保理有限公司’时，底层融资方显示，否则隐藏
    function changeIssueName(obj) {
        var issueName = $(obj).find("option:selected").text();
        issueName = issueName.replace(/(^\s*)|(\s*$)/g,"");    //去掉字符串两边的空格
        if (issueName == '深圳立合旺通商业保理有限公司') {
            $(".originalBorrower").show()
        } else {
            $(".originalBorrower").hide();
        }
    }

    function kindEdit()
    {
        KindEditor.create('.new_template', {
            cssPath: '/vendor/kindeditor/4.1.11/plugins/code/prettify.css',
            uploadJson: '/kindeditor/editor.php',
            allowFileManager: true,
            filterMode: false,
            items: ["source", "|", "preview", "print", "cut", "copy", "paste", "plainpaste", "wordpaste",
                    "|", "justifyleft", "justifycenter", "justifyright", "justifyfull", "insertorderedlist",
                    "insertunorderedlist", "indent", "outdent", "clearhtml", "quickformat", "selectall", "|",
                    "fullscreen", "/", "formatblock", "fontname", "fontsize", "|", "forecolor", "hilitecolor",
                    "bold", "italic", "underline", "strikethrough", "lineheight", "removeformat", "|", "table"],
        });
    }

    var flag = 0;
    function inscontract()
    {
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

    function disabledIsFdate(flag)
    {
        if (flag) {
            $('#onlineproduct-finish_date').val('');
            $('#onlineproduct-finish_date').attr('readonly', 'readonly');
            $('#onlineproduct-finish_date').replaceWith($('#onlineproduct-finish_date').clone());
        } else {
            $('#onlineproduct-finish_date').removeAttr('readonly');
        }
    }
</script>
<?php $this->endBlock(); ?>
