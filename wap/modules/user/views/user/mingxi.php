<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="交易明细";
?>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/jiaoyimingxi.css"/>
    <div class="container" style="background: #fff;padding-top: 10px;padding-bottom:10px;">
    <div class="row jiaoyi">
        <div class="col-xs-3">时间</div>
        <div class="col-xs-3">用途</div>
        <div class="col-xs-3">金额(元)</div>
        <div class="col-xs-3">账户余额(元)</div>
    </div>
    <?php if($model){ foreach($model as $key => $val): ?>
    <div class="clear"></div>
    <div class="row md-height border-bottom">
        <div class="col-xs-3 data">
            <span class="data1"><?= $val['created_at']?date('Y-m-d',$val['created_at']):"" ?></span>
            <span class="data2"><?= $val['created_at']?date('H:i:s',$val['created_at']):"" ?></span>
        </div>
        <div class="col-xs-3 revenue"><?= Yii::$app->params['mingxi']['type'][$val['type']] ?><i style="font-style: normal"><?= $desc[$key]?"(".$desc[$key].")":"" ?></i></div>
        <?php if($val['type'] == 0 || $val['type'] == 4 || ($val['type'] == 2 && $val['status'] == 3)) { ?>
        <div class="col-xs-3 money"><?= "+".$val['in_money'] ?></div>
        <?php }else if($val['type'] == 1 || $val['type'] == 2){ ?>
        <div class="col-xs-3 money"><?= "-".$val['out_money'] ?></div>
        <?php } ?>
        <div class="col-xs-3 revenue"><?= $val['balance'] ?></div>
    </div>
    <?php endforeach; ?>
    <?php }else{ ?>
    <div class="nodata" style="display: block">暂无数据</div>
    <?php } ?>
    </div>