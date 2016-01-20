<?php
    $this->title="限额说明";
?>
 <style>
    body {
        padding-bottom: 50px;
    }
    h1 {
        font-size: 32px;
        color: #595757;
        margin-top: 42px;
        margin-bottom: 46px;
        margin-right: 64px;
    }
    .qpay-quota {
        padding: 1px 64px;
        background-color: #ffffff;
    }
    .qpay-quota table {
        width: 100%;
        font-size: 28px;
        color: #595757;
    }
    .qpay-quota th, td {
        line-height: 2em;
        text-align: center;
        border: 2px solid #9fa0a0;
    }
    .desc {
        list-style: decimal;
        margin: 0px 57px;
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
        <tr>
            <td>邮储银行</td>
            <td>1万</td>
            <td>1万</td>
        </tr>
        <tr>
            <td>工商银行</td>
            <td>5万</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>农业银行</td>
            <td>5万</td>
            <td>10万</td>
        </tr>
        <tr>
            <td>中国银行</td>
            <td>20万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>建设银行</td>
            <td>100万</td>
             <td>500万</td>
        </tr>
        <tr>
            <td>交通银行</td>
            <td>2万</td>
            <td>2万</td>
        </tr>
        <tr>
            <td>中信银行</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>光大银行</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>民生银行</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>广发银行</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>平安银行（含深发）</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>兴业银行</td>
            <td>5万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>浦发银行</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
        <tr>
            <td>上海银行</td>
            <td>5000</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>北京银行</td>
            <td>5000</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>宁波东海银行</td>
            <td>10万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>南京银行</td>
            <td>2000</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>徽商银行</td>
            <td>10万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>江苏银行</td>
            <td>5000</td>
            <td>2万</td>
        </tr>
    </table>
    <h1>提示：</h1>
    <ol style="font-size: 28px; color: #737373;">
        <li class="desc">根据同卡进出原则，用户只能使用唯一一张绑定的银行卡进行充值和提现</li>
        <li class="desc">暂不支持变更绑定银行卡，如需帮助，请联系客服</li>
        <li style="text-align: right; color: #f44639"><img src="" alt=""><?= Yii::$app->params['contact_tel'] ?></li>
    </ol>
 </div>
 <!--footer-->
<div class="row navbar-fixed-bottom footer">
    <div class="col-xs-4 footer-title">
        <div class="footer-inner">
            <a href="/" class="shouye1"><span class="shouye"></span>首页</a>
        </div>
    </div>
    <div class="col-xs-4 footer-title">
        <div class="footer-inner1">
            <a href="/deal/deal/index"><span class="licai"></span>理财</a>
        </div>
    </div>
    <div class="col-xs-4 footer-title">
        <div class="footer-inner2">
            <?php if (!\Yii::$app->user->isGuest) { ?>
            <a href="/user/user"><span class="zhanghu"></span>账户</a>
            <?php } else { ?>
            <a href="/site/login"><span class="zhanghu"></span>账户</a>
            <?php } ?>
        </div>
    </div>
</div>