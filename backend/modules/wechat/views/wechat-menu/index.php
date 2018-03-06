<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-5
 * Time: 下午1:51
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '编辑菜单';
$this->params['breadcrumbs'][] = $this->title;
$menus = \common\models\AuthSys::getMenus('A100000');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>公众号管理</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/wechat/reply/index">公众号管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">编辑菜单</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="portlet-body form">
        <form action="" class="form-horizontal form-bordered form-label-stripped" method="post">
            <div class="control-group">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <label class="control-label">菜单内容</label>
                <div class="controls">
                    <textarea name="content" class="m-wrap span4">
                    </textarea>
                </div>
            </div>
            <div class="form-actions">
                <input type="submit" class="btn blue" value="提交">
            </div>
        </form>
    </div>
</div>
<script>
    <?php if (!empty($errMessage)) : ?>
        var info = "<?= $errMessage ?>";
        alert(info);
    <?php endif; ?>
</script>
<?php $this->endBlock(); ?>
