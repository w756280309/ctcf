<?php
$this->title="系统设置";

$muri = ASSETS_BASE_URI;
?>
<link rel="stylesheet" href="<?=$muri ?>css/setting.css">

<div class="row">
    <div class="row bg-height border-bottom">
        <div class="col-xs-3 left-txt">用户头像</div>
        <div class="col-xs-7 ">
            ID:<?= $model->mobile ?>
        </div>
        <div class="col-xs-2 headpic">
            <img class="headpic-img-box" src="<?=$muri ?>images/headpic.png" width="40px" hieght="40px" alt="头像">
        </div>
    </div>
    <div class="row sm-height border-bottom">
        <div class="col-xs-3 left-txt">绑定手机</div>
        <div class="col-xs-3"></div>
        <div class="col-xs-5 left-txt"><?= substr_replace($model->mobile,'****',3,-4) ?></div>
        <div class="hidden-xs col-sm-1 "></div>
    </div>
    <a class="row sm-height border-bottom margin-top block" href="/system/system/safecenter">
        <div class="col-xs-3 col-sm-3 left-txt">安全中心</div>
        <div class="col-xs-7 col-sm-7"></div>
        <div class="col-xs-1 col-sm-1 arrow">
           <img src="<?=$muri ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1 col-sm-1 "></div>
    </a>
    <div class="clear"></div>
</div>
<!-- 系统设置 end  -->
