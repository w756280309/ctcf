<?php

use common\models\mall\PointRecord;
use common\utils\StringUtils;
use common\widgets\Pager;
use frontend\assets\FrontAsset;

$this->title = '我的积分';

$this->registerCssFile(FE_BASE_URI.'pc/common/css/base.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(FE_BASE_URI.'pc/my_point/css/index.css?v=1.0', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => FrontAsset::class]);

?>

<div class="my_point">
    <ul class="my_point_top">
        <li class="top_left">
            <span class="point_now">当前积分</span>
            <p class="point_number"><?= StringUtils::amountFormat2($user->points) ?></p>
            <a href="/mall/point/rules" class="point_rules">
                <span class="i_pot">i</span>
                积分规则
            </a>
            <i class="border"></i>
        </li>
        <li class="top_right">
            <img src="<?= FE_BASE_URI ?>pc/my_point/images/wechat_img2.png" alt="">
            <ul>
                <li class="rule_content">PC端暂不支持兑换商品</li>
                <li class="rule_content">如需兑换请用手机扫描左侧二维码</li>
                <li class="rule_content">进入移动端"我的积分"进行兑换</li>
            </ul>
        </li>
    </ul>
    <div class="my_point_body">
        <div class="detail_title">
            <img src="<?= FE_BASE_URI ?>pc/my_point/images/detail_title.png" alt="">积分明细
        </div>
        <div class="detail_head">
            <div class="head1 lf">类型</div>
            <div class="head2 lf">时间</div>
            <div class="head3 lf">投资积分</div>
            <div class="head4 lf">当前积分</div>
            <div class="head5 lf">详情</div>
        </div>

        <ul class="detail_content">
            <?php foreach ($points as $point) { ?>
                <?php
                    switch ($point->ref_type) {
                        case PointRecord::TYPE_LOAN_ORDER:
                            $message = '投资获得';
                            $isIn = true;
                            break;
                        case PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1:
                            $message = '首投奖励';
                            $isIn = true;
                            break;
                        case PointRecord::TYPE_POINT_FA_FANG:
                        case PointRecord::TYPE_MALL_INCREASE:
                            $message = $point->remark;
                            $isIn = true;
                            break;
                        case PointRecord::TYPE_BACKEND_BATCH:
                            $message = empty($point->remark) ? '投资奖励' : $point->remark;
                            $isIn = true;
                            break;
                        default:
                            $message = '兑换商品';
                            $isIn = false;
                    }
                ?>
                <li class="items">
                    <div class="head1 lf"><?= $message ?></div>
                    <div class="head2 lf"><?= $point->recordTime ?></div>
                    <div class="head3 lf <?= $isIn ? 'point_a' : 'point_b' ?>"><?= $isIn ? ('+'.StringUtils::amountFormat2($point->incr_points)) : ('-'.StringUtils::amountFormat2($point->decr_points)) ?></div>
                    <div class="head4 lf"><?= StringUtils::amountFormat2($point->final_points) ?></div>
                    <div class="head5 lf">
                        <?php $order = $point->fetchOrder(); ?>
                        <?php if ($order) { ?>
                            <a href="/deal/deal/detail?sn=<?= $order->loan->sn ?>"><?= $order->loan->title ?></a>
                        <?php } else { ?>
                            流水号: <?= $point->sn ?>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
        <center><?= Pager::widget(['pagination' => $pages]); ?></center>
    </div>
</div>

<script>
    window.onload = function () {
        var ul = document.getElementsByClassName('detail_content')[0];
        var chilArr = ul.childNodes;
        var arr = [];
        for(var i=0;i<chilArr.length;i++){
            if(chilArr[i].nodeType == 1){
                arr.push(chilArr[i]);
            }
        }
        for(var i=0;i<arr.length;i++){
            if(i%2 != 0){
                arr[i].style.background = '#efeff3';
            }
        }

        var li_length = $('.detail_content').children().length;
        if(li_length == 0){
            $('.detail_content').css('height','295px').append('<p>暂无积分明细</p>')
        }
    }
</script>