<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = '0元夺宝';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理
                <small>0元夺宝</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">0元夺宝</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <?php if (\Yii::$app->session->hasFlash('info')) {?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="background-image: none!important;"><span aria-hidden="true">&times;</span></button>
                <strong><?= \Yii::$app->session->getFlash('info') ?></strong>
            </div>
        <?php }?>
        <form action="" method="post">
            <table class="table">
                <tr>
                    <td>
                        <div class="search-btn" align="right">
                            <input type ='hidden' name="_csrf" value="<?= Yii::$app->request->csrfToken?>">
                            <button type='submit' id="submit_btn" class="btn blue btn-block span3 submit_btn">增加1个
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
            <tr>
                <th style="text-align: center">ID</th>
                <th style="text-align: center">手机</th>
                <th style="text-align: center">时间</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($promoLottery as $plt) : ?>
                <tr>
                    <td style="text-align: center">
                        <?= $plt->id ?>
                    </td>
                    <td style="text-align: center">
                        <?= $plt->source != 'fake' ? $plt->user->getMobile() : '---'?>
                    </td>
                    <td style="text-align: center">
                        <?= date('Y-m-d H:i:s', $plt->created_at)?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(".form-actions").on( "click", "#submit_btn", function(e) {
        e.preventDefault();
        $(this).attr('disabled', true);
        $('#import_form').submit();
        setTimeout(function(){
            $("#submit_btn").attr('disabled', false);
        }, 2000);
    });
</script>
<?php $this->endBlock(); ?>
