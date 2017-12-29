<?php

use yii\widgets\LinkPager;
use common\models\adv\Splash;
use common\models\AuthSys;

$menus = AuthSys::getMenus('A100000');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>闪屏图</small>
                <a href="/adv/splash/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    新增闪屏图<i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/splash/index">闪屏图管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">闪屏图列表</a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/adv/splash/index" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">标题</span></td>
                            <td>
                                <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='title' value="<?= Yii::$app->request->get('title') ?>" />
                            </td>


                            <td><div align="right" style="margin-right: 20px">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                                </div></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->


        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th><input class="chooseall" type='checkbox'></th>
                        <th>ID</th>
                        <th>标题</th>
                        <th>闪屏图创建时间</th>
                        <th>发布时间</th>
                        <th>是否发布</th>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($model as $key => $val) : ?>
                        <tr>
                            <td>
                                <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                            </td>
                            <td>
                                <?= $val['sn'] ?>
                            </td>
                            <td><?= $val['title'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $val['createTime']) ?></td>
                            <td><?= $val['publishTime'] ?></td>
                            <td>
                                <?php
                                    if ($val['isPublished'] == Splash::UNPUBLISHED) {
                                        echo '否';
                                    } else {
                                        echo '是';
                                    }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <a href="/adv/splash/edit?id=<?= $val->id ?>" class="btn mini green ajax_op" op="status"  index="<?= $val['id'] ?>"><i class="icon-edit"></i>查看
                                </a>
                                <?php
                                    if ($val['auto_publish'] == Splash::AUTO_PUBLISH_ON) {
                                        $color = 'green';
                                        $onclick = "javascript:return confirm('确认取消自动发布？');";
                                        $statusName = '自动发布on';
                                        $icon = "icon-plus-sign";
                                    } else {
                                        $color = 'red';
                                        $onclick = "javascript:return confirm('确认自动发布？');";
                                        $statusName = '自动发布off';
                                        $icon = "icon-minus-sign";
                                    }
                                ?>
                                <a href="/adv/splash/publish?id=<?= $val->id ?>" class="btn mini <?= $color ?> ajax_op" op="status"  index="<?= $val['id'] ?>" onclick="<?= $onclick ?>"><i class="<?= $icon ?>"></i><?= $statusName ?>
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination" style="text-align:center;"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        $(".chooseall").click(function() {
            var isChecked = $(this).parent().hasClass('checked');
            if (!isChecked) {
                $("input[name='choose[]']").parent().addClass('checked');
            } else {
                $("input[name='choose[]']").parent().removeClass('checked');
            }
        });
    })
</script>
<?php $this->endBlock(); ?>

