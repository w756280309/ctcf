<?php

use common\utils\StringUtils;
use common\widgets\Pager;
use frontend\assets\FrontAsset;

$this->title = '我的代金券';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/mycoupon.css?v=161030', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/JPlaceholder.js', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/couponcode.js', ['depends' => FrontAsset::class]);

?>

<div class="myCoupon-box">
    <div class="myCoupon-header">
        <div class="myCoupon-header-icon"></div>
        <span class="myCoupon-header-font">我的代金券</span>
        <a href="javascript:" id="couponcode" class="couponcode">我有兑换码</a>
    </div>
    <div class="myCoupon-content">
        <div class="display_number">
            <p class="p_left">可用代金券：<span><?= empty($data['totalAmount']) ? 0 : StringUtils::amountFormat2($data['totalAmount']) ?></span>元</p>
            <p class="p_right">共：<span><?= $data['count'] ?></span>个</p>
            <a href="/licai/">立即投资</a>
        </div>
        <table>
            <tr>
                <th width="150">名称</th>
                <th width="120" class="text-align-lf">面值（元）</th>
                <th width="360" class="text-align-lf">使用规则</th>
                <th width="200" class="text-align-lf">使用期限</th>
                <th width="80" class="text-align-lf">状态</th>
            </tr>
            <?php foreach ($model as $key => $val) : ?>
                <?php if (!$val->isUsed && date('Y-m-d') <= $val->expiryDate) { ?>
                    <tr class="<?= 0 === $key%2 ? '' : 'td-back-color' ?>">
                        <td class="coupon-name"><?= $val->couponType->name ?></td>
                        <td class="table-text-number color-red"><?= StringUtils::amountFormat2($val->couponType->amount) ?></td>
                <?php } else { ?>
                    <tr class="already-use <?= 0 === $key%2 ? '' : 'td-back-color' ?>">
                        <td class="coupon-name"><?= $val->couponType->name ?></td>
                        <td class="table-text-number"><?= StringUtils::amountFormat2($val->couponType->amount) ?></td>
                <?php } ?>
                    <td>
                        单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $val->couponType->minInvest) ?>可用；
                        <?php
                            if ($val->couponType->loanExpires) {
                                echo '期限满'.$val->couponType->loanExpires.'天可用(除转让)';
                            } elseif (empty($val->couponType->loanCategories)) {
                                echo '新手标、转让不可用';
                            } else {
                                $arr = array_filter(explode(',', $val->couponType->loanCategories));

                                foreach ($arr as $k => $v) {
                                    $arr[$k] = \Yii::$app->params['pc_cat'][$v];
                                }

                                echo implode('、', $arr).'项目可用';
                            }
                         ?>；
                    </td>
                    <td><?= date('Y-m-d', $val->created_at) ?>至<?= $val->expiryDate ?></td>
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
<div class="code-mark"></div>
<div class="couponcode-box" id="couponcode-box">
    <form action="/user/couponcode/duihuan" method="post" id="code-forms">
    <h3 class="code-top">领取代金券<img class="close" src="<?= ASSETS_BASE_URI ?>images/login/close.png" alt=""></h3>
    <div class="code-content">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
            <div class="code-box">
                <label for="code">兑换码</label>
                <input id="code" class="coupon-code" type="text" maxlength="16" placeholder="请输入代金券兑换码" name="code" autocomplete="off" tabindex="1">
                <div style="clear: both"></div>
                <div class="popUp code_err"></div>
            </div>
            <div style="width: 100%">
                <p class="refer">*兑换码一般从楚天财富宣传页、合作网站等获得</p>
                <p class="refer">*必须在有效期内兑换代金券，过期无法兑换</p>
            </div>
    </div>
    <div class="code-success">
        <div class="code-box code-info"><i></i><span>兑换成功!</span></div>
        <div class="code-txt"><p>恭喜您获得了<span id="success-refer"></span></p></div>
    </div>
    <div class="code-bottom">
        <a id="code_submit_button" tabindex="2">立即兑换</a>
    </div>
    <input type="text" style="display:none" />
    </form>
</div>
