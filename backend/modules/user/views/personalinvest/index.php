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
                会员管理 <small>个人投资详情导出</small>
                <a href="/adv/adv/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    新增广告 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/index">个人投资详情导出</a>
                    <i class="icon-angle-right"></i>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/personalinvest/index" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>用户类型 :
                            <select name="type" id="">
                                <option value="">-请选择-</option>
                                <option value="online" <?php if(Yii::$app->request->get('type') == 'online') echo 'selected'; ?>>正式用户</option>
                                <option value="offline" <?php if(Yii::$app->request->get('type') == 'offline') echo 'selected'; ?>>线下用户</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='number' value="<?= Yii::$app->request->get('number') ?>" placeholder="请输入手机号或身份证号" />
                            <span style="color: red">(注:正式用户请输入手机号,线下用户输入身份证号)</span>
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
                    <th>姓名</th>
                    <th>身份证/手机号</th>
                    <th><center>操作</center></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php
                        if (!is_null($model)) {
                            if (Yii::$app->request->get('type') == 'online') {
                                echo '<td>'.$model->real_name.'</td><td>'.\common\utils\SecurityUtils::decrypt($model->safeMobile).'</td><td><center><a href="/user/personalinvest/export?number='.\common\utils\SecurityUtils::decrypt($model->safeMobile) .'&type=online">导出</a></center></td>';
                            } else {
                                echo '<td>'.$model->realName.'</td><td>'.$model->idCard.'</td><td><center><a href="/user/personalinvest/export?number='.$model->idCard.'&type=offline">导出</a></center></td>';

                            }
                                                    } else {
                            echo '暂无数据';
                        }
                    ?>
                </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>


<script type="text/javascript">
//    $('.dc').on('click', function(){
//        var aid = $(this).attr('aid');
//        $.get('/user/personalinvest/export', {'aid':aid}, function(str){
//
//        })
//    })
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
            if (confirm('确认此次操作吗？')) {
                $.post(url, {ids: ids.join(','), _csrf: csrftoken}, function(data) {
                    newalert(true, data.message, 1);
                });
            }
        })
    })
</script>
<?php $this->endBlock(); ?>

