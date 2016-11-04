<?php

use common\models\user\User;
use common\models\user\DrawRecord;
use yii\web\YiiAsset;
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员管理模块【主要包含投资会员投资申请】</small>
                <?php if ($category == User::USER_TYPE_ORG) { ?>
                    <a href="/user/user/edit?type=2" id="sample_editable_1_new" class="btn green" style="float: right;">
                        添加新融资客户 <i class="icon-plus"></i>
                    </a>
                <?php } ?>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/<?= $category == 1 ? "listt" : "listr" ?>">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listt">投资会员</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">提现申请</a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/drawrecord/apply?type=<?= $category ?>" method="get" target="_self">

                <table class="table">
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">真实姓名</span>
                            </td>
                            <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= $request['name'] ?>"  placeholder="真实姓名"/></td>
                            <td><span class="title">手机号</span></td>
                            <td>
                                <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='mobile' value="<?= $request['mobile'] ?>"  placeholder="手机号"/>
                            </td>
                            <td colspan="6"></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="title">提现申请时间</span>
                            </td>
                            <td colspan="3">
                                <input type="text" placeholder="开始时间" value="<?=$request['starttime']?>" name="starttime" class="m-wrap span4" style="width: 300px;" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d")?>"});'/>
                                ---
                                <input type="text" placeholder="结束时间" value="<?=$request['endtime']?>" name="endtime" class="m-wrap span4" style="width: 300px;" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date("Y-m-d")?>"});'/>
                            </td>
                            <td colspan="6" align="right" style=" text-align: right">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
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
                        <th>会员ID</th>
                        <th>手机号</th>
                        <th>真实姓名</th>
                        <th>提现金额（元）</th>
                        <th>申请时间</th>
                        <th>状态</th>
                        <th>联动状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model as $key => $val) : ?>
                        <tr>
                            <td><?= $res[$val['uid']]['usercode'] ?></td>
                            <td><?= $res[$val['uid']]['mobile'] ?></td>
                            <td><a href="#"><?= $res[$val['uid']]['real_name'] ?></a></td>
                            <td><?= $val['money'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $val['created_at']) ?></td>
                            <td><?= Yii::$app->params['draw_status'][$val['status']] ?></td>
                            <td><button class="btn btn-primary get_order_status" drawid="<?= $val['id'] ?>">查询流水在联动状态</button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        //点击获取流水在联动的状态
        $('.get_order_status').on('click', function () {
            var _this = $(this);
            var id = _this.attr('drawid');
            if (id) {
                var xhr = $.get('/user/drawrecord/ump-status?id='+id, function (data) {
                    _this.parent().html(data.message);
                });

                xhr.fail(function() {
                    newalert(0, '联动接口请求失败');
                });
            }
        });
    })
</script>
<?php $this->endBlock(); ?>