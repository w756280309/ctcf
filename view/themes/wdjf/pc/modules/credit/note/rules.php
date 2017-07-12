<?php
$this->title = '转让规则';

use frontend\assets\FrontAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/productcontract.css?v=20160926', ['depends' => FrontAsset::class]);
?>

<div class="contract-box clearfix">
    <div class="contract-container">
        <div class="contract-container-box">
            <p class="contract-title">转让规则</p>
            <div class="rules-content">
                <h1>可转让时限：</h1>
                <p>转让人持有产品满30天后即可进行转让，持有天数从计息日开始计算。</p>
                <p>产品转让具体时限包括以下两种情况：</p>
                <p>（1）对于发行人不能提前还款类的产品，产品持有期间支付利息的，每次付息日前3天（含）至付息日当日不可申请转让；还款日前3天（含）至还款日当日不可申请转让。</p>
                <p>（2）对于发行人可提前还款类的产品，产品持有期间支付利息的，每次付息日前3天（含）至付息日当日不可申请转让；还款日前13天（含）至还款日当日不可申请转让。</p>

                <h1>转让申请时效：</h1>
                <p>转让申请时效为3天，即72小时。如果转让人提交转让申请后72小时内未全部完成转让，未转让部分自动撤销，继续转让需重新申请。</p>

                <h1>转让申请的撤销：</h1>
                <p>已发布的转让申请如需撤销，可在“账户中心－我的转让－转让中的项目”进行撤销操作，已成功转让的部分不可撤销。</p>
                <p>转让申请撤销后，剩余未转让部分仍在“可转让列表”中，可继续申请转让；已转让部分可在“已转让列表”中查看。</p>

                <h1>转让限制：</h1>
                <p>（1）产品期限在6个月以下的不可进行转让。</p>
                <p>（2）转让金额需要满足产品的起投金额和递增金额，如转让人不进行全部转让，则剩余可转让金额不得低于产品最低起投金额。</p>
                <p>（3）转让人可以设计一定的折让率，但根据转让人选择的折让率（折让率是指产品在转让时折让的比率，折让率可以为零）计算所得的转让价款不得低于转让人拟向受让人转让其所持有的本产品投资本金金额（转让金额）。例如：转让金额为n元，折让率为v，根据产品合同约定计算的自收益起算日至转让成功日的投资收益为m元，转让人已经收取的投资收益为j 元（如有），受让人应向转让人支付的转让价款为 t 元。则产品转让价款t =（转让金额n + 自收益起算日至转让成功日的投资收益m - 已收取的投资收益j（如有））*（1-折让率v），t应当大于等于n。</p>

                <h1>产品投资收益归属：</h1>
                <p>受让人成功支付转让价款日为转让成功日。转让人拟转让产品投资本金（转让金额）对应的自收益起算日（含）至转让成功日（含）之间的投资收益归转让人所有，自转让成功日（不含）至本产品到期日（不含）的投资收益归属受让人所有。</p>

                <h1>转让价款计算：</h1>
                <p>转让价款t =（转让金额n + 自收益起算日至转让成功日的投资收益m - 已获得的投资收益j（如有））*（1-折让率）= [（n+m-j）*（1-v）]元。</p>

                <h1>转让手续费：</h1>
                <p>转让手续费费率为3‰，由转让人承担，转让成功后直接从转让价款中扣除，不成功平台不收取转让手续费。</p>
                <p>转让手续费 = 转让金额 * 3‰</p>
            </div>
        </div>
    </div>
</div>