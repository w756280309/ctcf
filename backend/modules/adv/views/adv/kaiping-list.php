<?php
use yii\widgets\LinkPager;

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>运营管理模块【主要包含广告管理】</small>
                <a href="/adv/adv/kaiping-edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    新增首页开屏 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/kaiping-list">首页开屏</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">开屏图列表</a>
                </li>
            </ul>
        </div>
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
                        <td><?= $val['status'] ? '下线' : '上线' ?></td>
                        <td><input style="width:20px;height: 10px" type="text" name="show_order" readonly="true" value="<?= NULL === $val['show_order'] ? '---' : $val['show_order'] ?>"></td>
                        <td><?= $val['showOnPc'] ? (1 === $val['showOnPc'] ? 'PC端' : 'WAP端') : '移动端'; ?></td>
                        <td style="text-align: center">
                            <a href="/adv/adv/kaiping-edit?id=<?= $val->id ?>" class="btn mini green ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-edit"></i>编辑
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="height: 40px">
                <button class="btn green btn-block btn-block-line-on" h='/adv/adv/lineoff' style="width: 100px; float: left"><i class="icon-edit"></i>上线</button>
                <button class="btn green btn-block btn-block-line-on offline" h='/adv/adv/lineon' style="width: 100px; float: left; margin-top: 0px; margin-left: 10px"><i class="icon-edit"></i>下线</button>
            </div>
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
                if ($(this).hasClass("offline")) {
                    alert('请选择下线记录');
                } else {
                    alert('请选择上线记录');
                }
                return false;
            }
            var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
            if (confirm('确认此次操作吗？')) {
                $.post(url, {ids: ids.join(','), _csrf: csrftoken}, function(data) {
                    newalert(true, data.message, 1);
                });
            }
        })
    })
</script>
<?php $this->endBlock(); ?>

