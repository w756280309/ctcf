<?php

$this->title = '我要理财';

$this->registerCssFile(ASSETS_BASE_URI . 'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI . 'css/credit/creditlist.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;
use common\widgets\Pager;

$action = Yii::$app->controller->action->getUniqueId();
$user = Yii::$app->user->getIdentity();
?>
<div class="projectContainer">
    <div class="alist-box">
        <a href='/licai/loan' class="list-span <?= 'licai/loan' === $action ? 'select-span' : '' ?>">网贷</a>
        <a href='/licai/' class="list-span <?= 'licai/index' === $action ? 'select-span' : '' ?>">定期</a>
        <a href='/licai/notes' class="list-span <?= 'licai/notes' === $action ? 'select-span' : '' ?>">转让</a>
<!--        --><?php //if (!empty($user) && $user->isShowNjq) { ?>
<!--            <a href='/njq/loan-list' class="list-span --><?//= 'njq/loan-list' === $action ? 'select-span' : '' ?><!--">南金中心</a>-->
<!--        --><?php //} ?>
    </div>
    <div class="clear"></div>

    <div class="projectList">
        <!--转让中-->
        <?php foreach ($notes as $note) : ?>
        <?php
            $loan = $note['loan'];
            $order = $note['order'];
            $endTime = new \DateTime($note['endTime']);
            $nowTime = new \DateTime();
            $tradedAmount = $note['tradedAmount'];
            $amount = $note['amount'];
            $realClosed = $note['isClosed'] || $nowTime >= $endTime;
            //转让中状态应对应为：isClosed=false
            $progress = $realClosed ? 100 : bcdiv(bcmul($tradedAmount, '100'), $amount, 0);
        ?>
        <a class="credit-single credit-single-border" href="/credit/note/detail?id=<?= $note['id'] ?>" target="_blank"> <!--类btn_ing_border为转让中的红边框-->
            <div class="single_left">
                <div class="single_title">
                    <p class="p_left"><span>【转让】</span><?= null === $loan ? '' : $loan->title ?></p>
                    <p class="p_right"><?= null === $loan ? '' :  Yii::$app->params['refund_method'][$loan->refund_method] ?></p>
                    <div class="clear"></div>
                </div>
                <div class="center-border"></div>
                <div class="single_content">
                    <ul class="single_ul_left">
                        <li class="li_1">
                            <i class="float-left">
                                <?= null === $order ? '' : StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2)) ?>
                            </i>
                            <span>%</span>
                        </li>
                        <li class="li_2">预期年化收益率</li>
                    </ul>
                    <ul class="single_ul_right">
                        <li class="li_1">
                            <?php
                                if (null !== $loan) {
                                    $remainingDuration = $loan->getRemainingDuration();
                                    if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                                        echo $remainingDuration['months'] . '<span>个月</span>';
                                    }
                                    if (isset($remainingDuration['days'])) {
                                        if (!isset($remainingDuration['months']) || $remainingDuration['days'] >0) {
                                            echo $remainingDuration['days'] . '<span>天</span>';
                                        }
                                    }
                                } else {
                                    echo '0<span>天</span>';
                                }

                            ?>
                        </li>
                        <li class="li_2">剩余期限</li>
                    </ul>
                    <ul class="single_ul_right-add">
                        <li class="li_1"><?= StringUtils::amountFormat1('{amount}<span>{unit}</span>', $note['amount'] / 100) ?></li>
                        <li class="li_2">转让金额</li>
                    </ul>
                </div>
            </div>
            <div class="single_right">
                <div class="single_right_tiao">
                    <div class="tiao_content">
                        <span class="tiao_content_length" style="width:<?= $progress ?>%"></span>
                    </div>
                    <span class="single_right_tiao_span"><?= $progress ?>%</span>
                    <div class="clear"></div>
                    <p class="remain-number">
                        距离结束：
                        <?php
                            if ($realClosed) {
                                echo '0天0时0分';
                            } else {
                                $dateDiff = $endTime->diff($nowTime);
                                echo $dateDiff->d . '天' . $dateDiff->h . '时' . $dateDiff->i . '分';
                            }
                        ?>
                    </p>
                </div>
                <!-- 转让中 -->
                    <?php
                        if ($realClosed) {
                    ?>
                        <span class="single_right_button_over">已转让</span>
                    <?php
                        } else {
                    ?>
                            <span class="single_right_button">转让中</span>
                    <?php
                        }
                    ?>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (!empty($notes)) { ?>
            <center><?= Pager::widget(['pagination' => $pages])?></center>
        <?php } else { ?>
            <p class="yet_show">暂无转让项目</p>
        <?php } ?>
    </div>
</div>
