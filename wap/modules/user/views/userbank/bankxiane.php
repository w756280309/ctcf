<?php

$this->title = '限额说明';

use common\utils\StringUtils;

?>
 <style>
    h1 {
        font-size: 16px;
        color: #595757;
        padding-top: 14px;
        margin-bottom: 18px;
        overflow: hidden;
    }
    .qpay-quota {
        padding-left: 26px;
        padding-right: 26px;
        background-color: #ffffff;
    }
    .qpay-quota table {
        width: 100%;
        font-size: 14px;
        color: #595757;
    }
    .qpay-quota th, td {
        line-height: 2em;
        text-align: center;
        border: 1px solid #9fa0a0;
    }
    .desc {
        list-style: decimal;
        width: 86%;
        margin: 0 auto;
    }
 </style>

 <div class="qpay-quota">
    <h1>各银行限额说明<font style="size: 30px; color:#898989">（仅限储蓄卡）</h1>
    <table>
        <tr>
            <th width="33%">银行</th>
            <th width="33%">单笔</th>
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
    <ol style="font-size: 14px; color: #737373;padding-bottom:30px;">
        <li class="desc">根据同卡进出原则，用户只能使用唯一一张绑定的银行卡进行充值和提现</li>
        <li class="desc">暂不支持变更绑定银行卡，如需帮助，请联系客服</li>
        <li style="text-align: right; color: #f44639"><img src="" alt=""><?= \Yii::$app->params['contact_tel'] ?></li>
    </ol>
 </div>