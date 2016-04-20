<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                资讯管理
                <small>运营模块</small>
                <a href="/news/news/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加资讯 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/news/index">资讯管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">资讯列表</a>
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
                        <td><input type="text" class="m-wrap" style="margin-bottom: 0px" id="title" name='title'
                                   value="<?= $selectQueryParams['title'] ?>" placeholder="请输入标题"/></td>
                        <td><span class="title">新闻分类</span></td>
                        <td>
                            <select class="small m-wrap" style="margin-bottom: 0px" name="category">
                                <option value="">--全部--</option>
                                <?php foreach ($categories as $key => $val): ?>
                                    <option value="<?= $key ?>" <?php
                                    if ($selectQueryParams['category_id'] == $key) {
                                        echo 'selected';
                                    }
                                    ?> ><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><span class="title">状态</span></td>
                        <td>
                            <select class="small m-wrap" style="margin-bottom: 0px" name="status">
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
                        <td>
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
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">新闻标题</th>
                    <th style="text-align: center">分类</th>
                    <th style="text-align: center">状态</th>
                    <th style="text-align: center">发布时间</th>
                    <th style="text-align: center">显示顺序</th>
                    <th style="text-align: center">创建时间</th>
                    <th style="text-align: center">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $key => $val) : ?>
                    <tr>
                        <td style="text-align: center">
                            <?= $val['id'] ?>
                        </td>
                        <td style="text-align: center">
                            <a href="/news/news/edit?id=<?= $val['id'] ?>"><?= $val['title'] ?></a>
                        </td>
                        <td style="text-align: center">
                            <?= $val->getCategoryName() ?>
                        </td>
                        <td style="text-align: center"><?php
                            foreach ($status as $k => $v) {
                                if ($k == $val['status']) {
                                    echo $v;
                                }
                            }
                            ?></td>
                        <td style="text-align: center"><?= date('Y-m-d H:i:s', $val['news_time']) ?></td>
                        <td style="text-align: center">
                            <?= $val['sort'] ?>
                        </td>
                        <td style="text-align: center"><?= date('Y-m-d H:i:s', $val['created_at']) ?></td>
                        <td style="text-align: center">
                            <a href="/news/news/edit?id=<?= $val['id'] ?>" class="btn mini purple"><i
                                    class="icon-edit"></i> 编辑</a>
                            <a href="/news/news/delete?id=<?= $val['id'] ?>"
                               onclick="javascript:return confirm('确定要删除吗？');" class="btn mini black"><i
                                    class="icon-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php $this->endBlock(); ?>

