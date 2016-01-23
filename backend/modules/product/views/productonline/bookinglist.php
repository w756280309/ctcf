<?php
use yii\widgets\LinkPager;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
            </h3>

            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/product/productonline/list">贷款管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">股权投资</a>
                </li>
            </ul>
        </div>


        <!--search start-->
        <div class="portlet-body">
            <form action="/product/productonline/bookinglist" method="get" target="_self">
            <table class="table">
                <tbody>
                <tr>
                    <td>
                        <span class="title">姓名</span>
                    </td>
                    <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= $name ?>"  placeholder="请输入预约人姓名"/></td>
                    <td>
                        <div align="right" style="margin-right: 20px">
                        <button type='submit' class="btn blue btn-block" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div>
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
                        <th><input class="chooseall" type='checkbox'></th>
                        <th>序号</th>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>预约金额</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model as $val) : ?>
                    <tr>
                        <td>
                            <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                        </td>
                        <td><?= $val['id'] ?></td>
                        <td><?= $val['name'] ?></td>
                        <td><?= $val['mobile'] ?></td>
                        <td><?= $val['fund'] ?>万</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>

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
    });
</script>
<?php $this->endBlock(); ?>


