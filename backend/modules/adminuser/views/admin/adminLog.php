<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-8
 * Time: 上午10:24
 */
?>
<?php $this->beginBlock('blockmain'); ?>

<!-- BEGIN PAGE HEADER-->
<div class="container-fluid">
    <div class="span12">
        <h3 class="page-title">
            系统管理 <small>管理员日志</small>
        </h3>
        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/adminuser/admin/list">系统管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="javascript:void(0);">管理员日志</a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <form action="/adminuser/admin/admin-log" method="get" target="_self" id="loanFilter">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="title">管理员姓名</span></td>
                    <td><input  type="text" class="m-wrap span4 " style="margin-bottom: 0px;width:200px" name='name' value="<?= Yii::$app->request->get('name') ?>" /></td>
                    <td><span class="title">类型</span></td>
                    <td><input  type="text" class="m-wrap span4 " style="margin-bottom: 0px;width:200px" name='type' value="<?= Yii::$app->request->get('type') ?>" /></td></td>
                    <td>
                        <div align="right" style="margin-right: 20px">
                            <input type="button"  class="btn" value="重置" style="width: 60px;" onclick="location='/adminuser/admin/admin-log'"/>
                            <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
            <tr>
                <th>管理员</th>
                <th>类型</th>
                <th>操作内容</th>
                <th>处理结果</th>
                <th>操作时间</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $model) : ?>
                <tr>
                    <td><?= $model->adminName ?></td>
                    <td><?= $model->tableName ?></td>
                    <td><?= '编辑ID为['. $model->primaryKey . ']的数据'?></td>
                    <td style="max-width:1000px;word-break:break-all; overflow:hidden;"><?= $model->changeSet != '[]' ? $model->changeSet : $model->allAttributes ?></td>
                    <td><?= date('Y-m-d H:i:s', $model->created_at) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination" style="text-align:center"><?=  \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>

