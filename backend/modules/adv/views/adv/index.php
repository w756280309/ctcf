<?php
use yii\widgets\LinkPager;
use common\models\AuthSys;

$menus = AuthSys::getMenus('A100000');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>运营管理模块【主要包含广告管理】</small>
                <a href="/adv/adv/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    新增广告 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/index">广告管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">首页轮播</a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/adv/adv/index" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">标题</span></td>
                            <td>
                                <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='title' value="<?= Yii::$app->request->get('title') ?>" />
                            </td>


                            <td>
                                <span class="title" >状态</span>
                            </td>
                            <td>
                                <select name="status" style="width:300px">
                                    <option value="">---未选择---</option>
                                    <option value="0"
                                    <?php
                                    if (Yii::$app->request->get('status') == '0') {
                                        echo "selected='selected'";
                                    }
                                    ?>
                                            >上线</option>
                                    <option value="1"
                                    <?php
                                    if (Yii::$app->request->get('status') == '1') {
                                        echo "selected='selected'";
                                    }
                                    ?>
                                            >下线</option>

                                </select>
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
                        <th>状态</th>
                        <th>显示顺序</th>
                        <th>显示设备</th>
                        <th style="text-align: center">操作</th>
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
                            <td><?php
                                if ($val['status'] == 0) {
                                    echo "上线";
                                } else {
                                    echo "下线";
                                }
                                ?></td>
                            <td><input style="width:20px;height: 10px" type="text" name="show_order" readonly="true" value="<?= $val['show_order'] ?>"></td>
                            <td><?= !empty($val['showOnPc']) ? 'PC端' : '移动端' ?></td>
                            <td style="text-align: center">
                                <a href="/adv/adv/edit?id=<?= $val->id ?>" class="btn mini green ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-edit"></i>编辑
                                </a>
                                |
                                <a href="/adv/adv/delete?id=<?= $val->id ?>" class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>删除
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="height: 40px">
            <button class="btn green btn-block btn-block-line-on" h='/adv/adv/lineoff' style="width: 100px; float: left"><i class="icon-edit"></i>上线</button>
            <button class="btn green btn-block btn-block-line-on" h='/adv/adv/lineon' style="width: 100px; float: left; margin-top: 0px; margin-left: 10px"><i class="icon-edit"></i>下线</button>
            </div>
            <div class="pagination" style="text-align:center;"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        $(".chooseall").click(function() {
            //var isChecked = $(this).prop("checked");
            var isChecked = $(this).parent().hasClass('checked');
            if (!isChecked) {
                $("input[name='choose[]']").parent().addClass('checked');
            } else {
                $("input[name='choose[]']").parent().removeClass('checked');
            }
        });

        $('.btn-block-line-on').click(function() {
            var objs = $("input[name='choose[]']").parent();
            var ids = new Array();
            var ckidkey = 0;
            var url = $(this).attr('h');
            for (var i = 0; i < objs.length; i++) {
                if ($(objs[i]).hasClass('checked')) {
                    ids[ckidkey] = $($("input[name='choose[]']").get(i)).val();
                    ckidkey++;
                }
            }
            if (ids.length == 0) {
                alert('请选择上线记录');
                return false;
            }
            var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
            if (confirm('确认此次操作吗？')) {
                $.post(url, {ids: ids.join(','), _csrf: csrftoken}, function(data)
                {
//                        alert(data.message);
                    newalert(true, data.message, 1);
                });
            }
        })


    })
</script>
<?php $this->endBlock(); ?>

