<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\AuthSys;

$menus = AuthSys::getMenus('A1000000');
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                资讯管理
                <small>新闻资讯类模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/news/">资讯管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/news/index">资讯列表</a>
                    <i class="icon-angle-right"></i>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="/news/news/index" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>
                            <span class="title">标题</span>
                        </td>
                        <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px" id="title" name='title' value="<?= $selectQueryParams['title'] ?>" placeholder="请输入标题"/></td>
                        <!--<td><span class="title">新闻分类</span></td>
                    <td>
                        <select class="small m-wrap" style="margin-bottom: 0px">
                                <option value="">--全部--</option>
                                <?php /*foreach ($categories as $key => $val): */ ?>
                                <option value="<? /*= $key */ ?>" <?php
                        /*                                if ($selectQueryParams['category_id'] == $key) {
                                                                echo 'selected';
                                                        }
                                                        */ ?> ><? /*= $val */ ?></option>
                                <?php /*endforeach; */ ?>
                        </select>
                    </td>-->
                        <td><span class="title">状态</span></td>
                        <td>
                            <select class="small m-wrap" style="margin-bottom: 0px">
                                <option value="">--全部--</option>
                                <?php foreach ($status as $key => $val): ?>
                                    <option value='<?= $key ?>' <?php
                                    if ($selectQueryParams['status'] == $key && $selectQueryParams['status'] != "") {
                                        echo 'selected';
                                    }
                                    ?> ><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="right" style=" text-align: right">
                            <button class="btn blue btn-block" style="width: 100px;">
                                查询 <i class="m-icon-swapright m-icon-white"></i>
                            </button>
                        </td>
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
                    <th>ID</th>
                    <th>新闻标题</th>
                    <th>状态</th>
                    <th>发布时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $key => $val) : ?>
                    <tr>
                        <td>
                            <?= $val['id'] ?>
                        </td>
                        <td><?= $val['title'] ?></td>
                        <td><?php
                            foreach ($status as $k => $v) {
                                if ($k == $val['status']) {
                                    echo $v;
                                }
                            }
                            ?></td>
                        <td><?= date('Y-m-d H:i:m', $val['news_time']) ?></td>
                        <td>
                            <a href="/news/news/edit?id=<?= $val['id'] ?>" class="btn mini purple"><i
                                    class="icon-edit"></i> 编辑</a>
                            <a href="/news/news/delete?id=<?= $val['id'] ?>"
                               onclick="javascript:return confirm('确定要删除吗？');" class="btn mini black"><i
                                    class="icon-trash"></i> 删除</a>
                            <a href="#" class="btn mini green-stripe">查看</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {

    });
</script>
<?php $this->endBlock(); ?>

