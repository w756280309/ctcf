<?php
use common\models\user\User;
use common\utils\StringUtils;
$isPersonal = $category === User::USER_TYPE_PERSONAL;
$accounts = Yii::$app->params['borrowerSubtype'];
?>
<div class="portlet-body">
    <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
            <tr>
        <?php if ($isPersonal) { ?>
                <th>手机号</th>
                <th>真实姓名</th>
        <?php } else { ?>
                <th><input class="chooseall" type='checkbox'></th>
                <th>企业名称</th>
                <th>账户类型</th>
        <?php } ?>
                <th>注册时间</th>
                <th class="money">可用余额（元）</th>
        <?php if ($isPersonal) { ?>
                <th class="money">资产总额</th>
                <th>未投资时长（天）</th>
                <th class="money">最后一次购买金额</th>
                <th>用户等级</th>
                <th>所属分销商</th>
                <th>注册位置</th>
                <th>联动状态</th>
                <th>账户状态</th>
        <?php } else {?>
                <th>联动商户号</th>
        <?php }?>
                <th><center>操作</center></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($model as $key => $val) : ?>
            <tr>
        <?php if ($isPersonal) { ?>
                <td><a href="/user/user/detail?id=<?= $val['id'] ?>" ><?= $val['mobile'] ?></a></td>
                <td><?= $val['real_name'] ? '<a href="/user/user/detail?id='.$val['id'].'">'.$val['real_name'].'</a>' : '---' ?></td>
        <?php } else { ?>
                <td>
                    <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                </td>
                <td><a href="/user/user/detail?id=<?= $val['id'] ?>" ><?= $val['org_name'] ?></a></td>
                <td><?php echo $accounts[$val->borrowerInfo['type']]?></td>
        <?php }?>
                <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                <td class="money"><?= StringUtils::amountFormat3($isPersonal ? $val->lendAccount['available_balance'] : $val->borrowAccount['available_balance']) ?></td>
                <?php if ($isPersonal) { ?>
                    <td class="money"><?= number_format($val->lendAccount->totalFund, 2)?></td>
                    <td>
                        <?php
                            $info = $val->info;
                            if ($info){
                                $days = (new \DateTime)->diff(new \DateTime($info->lastInvestDate))->days;
                            } else {
                                $days = 0;
                            }
                            echo $days ;
                        ?>
                    </td>
                    <td class="money">
                        <?= $val->info ? StringUtils::amountFormat3($val->info->lastInvestAmount) : 0 ?>
                    </td>
                    <td>VIP<?= $val->level ?></td>
                    <td><?= isset($affiliators[$val->id]) ? $affiliators[$val->id]->affiliator->name : '---' ?></td>
                    <td><?= $val->regContext ? $val->regContext : '---' ?></td>
                    <td>
                        <button class="btn btn-primary get_order_status" uid="<?= $val['id'] ?>">查询联动状态</button>
                    </td>
                    <td>
                        <?php echo $val->status == 1 ? '正常': '禁用';?>
                    </td>
                <?php } else {?>
                    <td><?php echo $val->epayUser->epayUserId;?></td>
                <?php }?>
                <td>
                <center>
                     <?php if($isPersonal) { ?>
                        <a href="/user/user/detail?id=<?= $val['id'] ?>" ><span class="label label-success"><i class="icon-edit"></i> 详情</span></a>
                     <?php } else { ?>
                        <a href="/user/user/edit?id=<?= $val['id'] ?>&type=<?= $category ?>" ><span class="label label-success"><i class="icon-edit"></i> 编辑</span></a>
                        <a href="/user/user/detail?id=<?= $val['id'] ?>" ><span class="label label-success"><i class="icon-edit"></i> 查看用户详情</span></a>
                         <a href="/user/user/soft-delete-org-user?id=<?= $val['id'] ?>" class="soft_delete_org_user"><span class="label label-danger"><i class="icon-minus-sign"></i>删除</span></a>
                    <?php } ?>
                    | <a href="/user/point/add?userId=<?= $val['id'] ?>&backUrl=<?= '/user/user/listt' ?>"><span class="label label-success">给积分</span></a>
                    | <?php if ($isPersonal) :?>
                        <a href="/user/user/user-access?id=<?= $val['id'] ?>"><span class="label label-success">权限控制</span></a>
                      <?php endif;?>
                </center>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($model)) { ?>
    <div class="note">暂无数据</div>
<?php } ?>

<script>
    $('.get_order_status').bind('click', function () {
        var csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
        var _this = $(this);
        if (_this.hasClass("isclicked")) {
            return false;
        }
        _this.addClass("isclicked");
        var uid = $(this).attr("uid");
        var xhr = $.ajax({
            type: 'POST',
            url: '/user/user/umpuserinfo?uid='+uid,
            data: {'_csrf': csrf},
            dataType: 'json'
        });

        xhr.done(function(data) {
            _this.removeClass("isclicked");
            if (parseInt(data.code) >= 0) {
                _this.parent().html(data.message);
            } else if (-1 === parseInt(data.code)) {
                _this.html("查询失败，点击重试");
            }
        });

        xhr.fail(function() {
            _this.removeClass("isclicked");
        });
    });
   $('.soft_delete_org_user').on('click', function (e) {
       e.preventDefault();
       if (confirm('确定删除')) {
           var url = $(this).attr('href');
           $.get(url, function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                } else {
                    alert(data.msg)
                }
           })
       }

   });
    $(".chooseall").click(function() {
        var isChecked = $(this).parent().hasClass('checked');
        if (!isChecked) {
            $("input[name='choose[]']").parent().addClass('checked');
        } else {
            $("input[name='choose[]']").parent().removeClass('checked');
        }
    });
</script>

