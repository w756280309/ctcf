<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-11-22
 * Time: 下午5:25
 */
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
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/personalinvest/index">个人投资详情导出</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/index">查看</a>
                </li>
            </ul>
        </div>

        <!--search start-->

        <!--search end -->


        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th>客户姓名</th>
                    <th>产品名称</th>
                    <th>产品期限</th>
                    <th>开户行名称</th>
                    <th>银行卡号</th>
                    <th>认购日</th>
                    <th>起息日</th>
                    <th>认购金额</th>
                    <th>收益率</th>
                    <th>到期日</th>
                    <th>还款方式</th>
                    <th>线上/线下</th>
                    <center><th>付息情况</th></center>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data as $v): ?>
                    <tr>
                        <td><?= $v[0] ?></td>
                        <td><?= $v[1] ?></td>
                        <td><?= $v[2] ?></td>
                        <td><?= $v[3] ?></td>
                        <td><?= $v[4] ?></td>
                        <td><?= $v[5] ?></td>
                        <td><?= $v[6] ?></td>
                        <td><?= $v[7] ?></td>
                        <td><?= $v[8] ?></td>
                        <td><?= $v[11] ?></td>
                        <td><?= $v[12] ?></td>
                        <td><?= $v[13] ?></td>
                        <td><center>
                                <?php if($v[9]): ?>
                                <a href="javascript:openwin('/user/personalinvest/view-fx?time=<?= $v[9] ?>&money=<?= $v[10] ?>',500,400)"
                                       class="btn mini green"><i class="icon-edit"></i>查看</a>
                                <?php endif; ?>
                            </center></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $this->endBlock(); ?>

