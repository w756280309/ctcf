<?php

$this->title = '限额说明';

use common\utils\StringUtils;

$this->registerCssFile(ASSETS_BASE_URI.'css/banklimit.css', ['depends' => 'wap\assets\WapAsset']);
?>

 <div class="qpay-quota">
    <h1>各银行限额说明<span>（仅限储蓄卡）</span></h1>
    <table>
        <tr>
            <th>银行</th>
            <th>单笔</th>
            <th>单日</th>
        </tr>
        <?php foreach ($banks as $bank): ?>
        <tr>
            <td><?= $bank->bank->bankName ?>
                <?php if ($bank->isDisabled) {
                    echo '(暂停)';
                } ?>
            </td>
            <td><?= StringUtils::amountFormat1('{amount}{unit}', $bank->singleLimit) ?></td>
            <td><?= StringUtils::amountFormat1('{amount}{unit}', $bank->dailyLimit) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h1>提示：</h1>
    <ol class="desc">
        <li>根据同卡进出原则，用户只能使用唯一一张绑定的银行卡进行充值和提现</li>
        <li>客服电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></li>
    </ol>
 </div>
