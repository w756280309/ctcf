<?php

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\utils\StringUtils;
use common\utils\SecurityUtils;
use yii\grid\GridView;
use yii\helpers\Html;

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

        <?php
            $couponType = CouponType::findOne($coupon_id);
            if (null !== $couponType) {
        ?>
            <div class="portlet-body">
                <table class="table" style="margin-bottom: 0px;">
                    <?php
                        $attributes = [
                            'name',
                            'amount',
                            'minInvest',
                        ];
                    ?>
                    <tr>
                        <?php
                            $c = count($attributes);
                            for ($i = 0; $i < $c; $i++) {
                                $attributeName = $attributes[$i];
                        ?>
                                <td class="title">
                                    <?= $couponType->getAttributeLabel($attributeName) ?>：<?= $i < 1 ? $couponType->{$attributeName} : StringUtils::amountFormat2($couponType->{$attributeName}) . '元' ?>
                                </td>
                        <?php
                            }
                        ?>
                        <td></td>
                    </tr>
                    <?php
                        $statusArr = [
                            '总数量',
                            'valid' => '未使用',
                            'used' => '已使用',
                            'expired' => '已过期',
                        ];
                    ?>
                    <tr>
                        <?php
                            foreach ($statusArr as $stats => $label) {
                        ?>
                            <td class="title">
                                <?= $label ?>：<?= UserCoupon::findCouponByConfig($coupon_id, !empty($stats) ? $stats : null)->count() ?>
                            </td>
                        <?php
                            }
                        ?>
                    </tr>
                </table>
            </div>
        <?php
            }
        ?>

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
            <div class="portlet-body">
                <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                        'columns' => [
                            [
                                'header' => '用户手机号',
                                'value' => function ($coupon) {
                                    return isset($coupon->user->safeMobile) ? SecurityUtils::decrypt($coupon->user->safeMobile) : '---';
                                }
                            ],
                            [
                                'header' => '真实姓名',
                                'format' => 'html',
                                'value' => function ($coupon) {
                                    if (isset($coupon->user->real_name)) {
                                        return '<a href="/user/user/detail?id='.($coupon->user->id).'">'.$coupon->user->real_name.'</a>';
                                    }

                                    return '----';
                                }
                            ],
                            [
                                'header' => '注册时间',
                                'value' => function ($coupon) {
                                    return isset($coupon->user->created_at) ? date('Y-m-d H:i:s', $coupon->user->created_at) : '---';
                                },
                            ],
                            [
                                'header' => '领取时间',
                                'value' => function ($coupon) {
                                    return date("Y-m-d H:i:s", $coupon->created_at);
                                }
                            ],
                            [
                                'header' => '使用状态',
                                'value' => function ($coupon) {
                                    $label = '----';
                                    if ($coupon->isUsed) {
                                        $label = '已使用';
                                    } else {
                                        if ($coupon->expiryDate < date('Y-m-d')) {
                                            $label = '已过期';
                                        } else {
                                            $label = '未使用';
                                        }
                                    }

                                    return $label;
                                }
                            ],
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>