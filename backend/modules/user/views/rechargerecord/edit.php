<?php

    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    use yii\helpers\Html;
    use yii\web\YiiAsset;
    use common\models\user\User;
    
    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员充值管理模块【主要包含融资会员的充值数据录入管理】</small>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?=Yii::$app->request->get('type')==2?'listr':'listt'?>">会员管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                     <?php if(Yii::$app->request->get('type')==User::USER_TYPE_PERSONAL){?>
                    <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }else{?>
                        <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }?>
                    <li>
                        <a href="/user/user/<?=Yii::$app->request->get('type')==2?'listr':'listt'?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?=$_GET['id']?>&type=<?=Yii::$app->request->get('type');?>">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">充值流水明细录入</a>
                    </li>
            </ul>            
        </div>
 <div class="portlet-body form">
         
      <?php $form = ActiveForm::begin(['id' => 'adv_form', 'action' => "/user/rechargerecord/edit?id=$id&type=$type",'options' => ['class'=>'form-horizontal form-bordered form-label-stripped']]); ?>             
     <div class="control-group">
        <label class="control-label">流水号</label>
        <div class="controls">
            <?= $form->field($model, 'sn', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'流水号']])->textInput() ?>
            <?= $form->field($model, 'sn', ['template' => '{error}']); ?>
        </div>
    </div>
     <div class="control-group">
        <label class="control-label">充值银行</label>
        <div class="controls">
            <?=
            $form->field($model, 'bank_id', ['template' => '{input}{error}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'chosen-with-diselect span6','placeholder'=>'充值银行']])->dropDownList($banks)
            ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">充值金额</label>
        <div class="controls">
            <?= $form->field($model, 'fund', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'充值金额']])->textInput() ?>
            <?= $form->field($model, 'fund', ['template' => '{error}']); ?>
        </div>
    </div>
             
    <div class="control-group">
        <label class="control-label">充值时间</label>
        <div class="controls">
            <input type="text" value="" name = "created_at" onclick='WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",maxDate:"<?=  date('Y-m-d')?>"});'/>
        </div>
    </div>
             
    <div class="form-actions">

                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>

                    <a href="/user/user/detail?id=<?= $id ?>&type=<?=$type;?>" class="btn">取消</a> 

    </div>
                
    <?php $form->end(); ?> 
                
 </div>

<?php $this->endBlock(); ?>

        <style type="text/css">
            .page_table table tr{
                background-color: lightblue;
            }
        </style>
