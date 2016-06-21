<?php
$this->title = '我的代金券';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/mycoupon.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;
use common\widgets\Pager;
?>

<div class="myCoupon-box">
    <div class="myCoupon-header">
        <div class="myCoupon-header-icon"></div>
        <span class="myCoupon-header-font">我的代金券</span>
    </div>
    <div class="myCoupon-content">
        <div class="display_number">
            <p class="p_left">可用代金券：<span><?= empty($data['totalAmount']) ? 0 : StringUtils::amountFormat2($data['totalAmount']) ?></span>元</p>
            <p class="p_right">共：<span><?= $data['count'] ?></span>个</p>
            <a href="/licai/">立即投资</a>
        </div>
        <table>
            <tr>
                <th width="148" class="table-text-ct">面值（元）</th>
                <th width="320" class="text-align-lf">使用规则</th>
                <th width="210" class="text-align-lf">使用期限</th>
                <th width="80" class="text-align-lf">状态</th>
            </tr>
            <?php foreach ($model as $key => $val) : ?>
                <?php if (!$val->isUsed && date('Y-m-d') <= $val->expiryDate) { ?>
                    <tr class="<?= 0 === $key%2 ? '' : 'td-back-color' ?>">
                        <td class="table-text-number color-red"><?= StringUtils::amountFormat2($val->couponType->amount) ?></td>
                <?php } else { ?>
                    <tr class="already-use <?= 0 === $key%2 ? '' : 'td-back-color' ?>">
                        <td class="table-text-number"><?= StringUtils::amountFormat2($val->couponType->amount) ?></td>
                <?php } ?>
                    <td>
                        单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $val->couponType->minInvest) ?>可用；
                        <?php
                            if (empty($val->couponType->loanCategories)) {
                                echo '所有';
                            } else {
                                $arr = array_filter(explode(',', $val->couponType->loanCategories));

                                foreach ($arr as $k => $v) {
                                    $arr[$k] = \Yii::$app->params['pc_cat'][$v];
                                }

                                echo implode('、', $arr);
                            }
                         ?>项目可用；
                    </td>
                    <td><?= $val->couponType->useStartDate ?>至<?= $val->couponType->useEndDate ?></td>
                    <td>
                        <?php
                            if ($val->isUsed) {
                                echo '已使用';
                            } elseif (date('Y-m-d') > $val->expiryDate) {
                                echo '已过期';
                            } else {
                                echo '未使用';
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <center><?= Pager::widget(['pagination' => $pages]); ?></center>

        <?php if (!$model) { ?>
            <div class="table-kong"></div>
            <div class="table-kong"></div>
            <p class="without-font">暂无代金券</p>
        <?php } ?>
    </div>
</div>