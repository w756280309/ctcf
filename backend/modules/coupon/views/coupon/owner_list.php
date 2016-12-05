<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$status = Html::encode($status);
$now_date = date('Y-m-d');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                代金券管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/coupon/coupon/list">代金券管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">代金券领取记录列表</a>
                    </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/coupon/coupon/owner-list" method="get" target="_self">
                <input type="hidden" name='id' value="<?= Html::encode($coupon_id) ?>"/>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">使用状态</span></td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <option value="a"
                                    <?php
                                        if ('a' === $status) {
                                            echo "selected='selected'";
                                        }
                                    ?>
                                            >未使用</option>
                                    <option value="b"
                                    <?php
                                        if ('b' === $status) {
                                            echo "selected='selected'";
                                        }
                                    ?>
                                            >已使用</option>
                                    <option value='c'
                                        <?php
                                        if ('c' === $status) {
                                            echo "selected='selected'";
                                        }
                                        ?>
                                    >已过期</option>
                                </select>
                            </td>
                            <td>
                                <div class="search-btn" align="right">
                                    <button type='submit' class="btn blue btn-block button-search">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
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
                        <th>序号</th>
                        <th>用户手机号</th>
                        <th>真实姓名</th>
                        <th>注册时间</th>
                        <th>领取时间</th>
                        <th>使用状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= ++$key ?></td>
                        <td><?= $val['mobile'] ?></td>
                        <td><a href="/user/user/detail?id=<?= $val['id']?>"><?= empty($val['real_name']) ? '---' : $val['real_name'] ?></a></td>
                        <td><?= date('Y-m-d H:i:s', $val['created_at']) ?></td>
                        <td><?= date('Y-m-d H:i:s', $val['collectDateTime']) ?></td>
                        <td>
                            <?php
                                if ($val['isUsed']) {
                                    echo '已使用';
                                } else if ($now_date > $val['expiryDate']) {
                                    echo '已过期';
                                } else {
                                    echo '未使用';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination text-align-ct"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>