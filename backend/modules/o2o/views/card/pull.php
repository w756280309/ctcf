<?php

$this->title = '补充领取人';
use yii\widgets\ActiveForm;
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    运营管理 <small>O2O商家管理</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/o2o/affiliator/list">商家列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a class="jump-list" href="/o2o/card/list?affId=<?= $affId ?>">兑换码列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="#">发放预留兑换码</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body form">
                <?php if ($pullStatus == 1) {?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        用户不存在！
                    </div>
                <?php }?>
                <?php if ($pullStatus == 3) {?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        兑换券发放失败！
                    </div>
                <?php }?>

                <form role="form" method="post" action="/o2o/card/update" class="form-horizontal form-bordered form-label-stripped">

                    <div class="control-group">
                        <label class="control-label">兑换码</label>
                        <div class="controls">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
                            <input type="text" name ="serial" autocomplete="off" class="m-wrap span4" value="<?= $serial ?>" readonly>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">手机号</label>
                        <div class="controls">
                            <input type="tel" autocomplete="off" name="mobile" class="m-wrap span4" maxlength="11">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit"   class="btn blue sub-btn"><i class="icon-ok"></i> 提交 </button>&nbsp;&nbsp;&nbsp;
                        <a href="/o2o/card/list?affId=<?= $affId ?>" class="btn">取消</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

<?php $this->endBlock(); ?>