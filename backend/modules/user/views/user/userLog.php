<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-8
 * Time: 上午11:36
 */
echo 'hello world';
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>用户日志</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">用户日志</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <form action="/user/user/user-log" method="get" target="_self" id="loanFilter">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="title">用户账号</span></td>
                    <td><input  type="text" class="m-wrap span4 " style="margin-bottom: 0px;width:200px" name='mobile' value="<?= Yii::$app->request->get('mobile') ?>" /></td>
                    <td>
                        <div align="right" style="margin-right: 20px">
                            <input type="button"  class="btn" value="重置" style="width: 60px;" onclick="location='/user/user/user-log'"/>
                            <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover"  width="100%">
            <thead>
            <tr>
                <th>用户账号</th>
                <th>登录IP</th>
                <th>登录时间</th>
                <th>登录设备</th>
                <th>登录状态</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $model) : ?>
                <tr>
                    <td><?= \common\utils\StringUtils::obfsMobileNumber($model->user_name) ?></td>
                    <td><?= $model->ip ?></td>
                    <td><?= date('Y-m-d H:i:s', $model->created_at) ?></td>
                    <td><?= $model->type == 1 ? '移动端' : 'PC' ?></td>
                    <td><?= $model->status == 1 ? '成功' : '失败' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination" style="text-align:center"><?=  \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>
