<?php
/**
 * {{合同编号}}            <?= $contractNum ?>      为系统生成的编号
 * {{转让人}}              <?= $sellerName ?>       转让人姓名
 * {{转让人身份证号}}        <?= $sellerIdCard ?>     转让人身份证号码
 * {{受让人}}              <?= $buyerName ?>          受让人姓名
 * {{受让人身份证号}}        <?= $buyerIdCard ?>      受让人身份证号码
 * {{认购日期}}             <?= $loanOrderCreateDate ?>转让人认购原产品时的认购时间，如2016年8月8日
 * {{y}}                  <?= $loanTitle ?>-        产品名称
 * {{z}}                  <?= $loanOrderPrincipal ?>转让人在原订单中的认购金额
 * {{n}}                  <?= $creditOrderPrincipal ?>受让人购买的转让金额
 * {{发行方}}              <?= $loanIssuer ?>          产品的发行方名称
 * {{融资方}}              <?= $affiliator ?>          产品的融资方名称
 * {{募集总额}}            <?= $exceptRaisedAmount ?>   产品的募集总额
 * {{起投金额}}            <?= $incrAmount ?>           产品的起投金额
 * {{产品起息日}}          <?= $interestDate ?>          起息日
 * {{产品到期日}}          <?= $finishDate ?>            到期日
 * {{收益率}}             <?= $yieldRate ?>             年化收益率，如果是阶梯的，显示阶梯
 * {{还款方式}}            <?= $refundMethod ?>         产品还款方式
 * {{m}}                 <?= $sellerInterest ?>         转让人从起息日到转让日的收益，即已还利息+转让人应收利息
 * {{w}}                 <?= $buyerInterest ?>          受让人从转让日到到期日的收益，即还款方式所有收益-应付利息
 * {{v}}                 <?= $discountRate ?>           折让率，如2.22
 * {{j}}                 <?= $refundedInterest ?>       转让人的已还利息
 * {{t}                  <?= $creditOrderPayAmount ?>   受让人向转让人支付的，即折让后价格
 * {{e}}                 <?= $feeRate ?>                手续费比率，固定值3
 * {{k}                  <?= $feeAmount ?>              手续费金额
 */
?>
<style>
    .text_index { text-indent: 2em; }
</style>
<div>
    <p style="text-align:center; ">
        <span style=" font-size:14pt; font-weight:bold">产品转让协议</span>
    </p>
    <p style="text-align:right; ">
        <span>编号：</span>
        <span><?= $contractNum ?></span>
    </p>
    <p>
        <span>甲方：</span>
        <span><?= $sellerName ?></span>
    </p>
    <p>
        <span>身份证号：</span>
        <span><?= $sellerIdCard ?></span>
    </p>
    <p>
        <span>乙方：</span>
        <span><?= $buyerName ?></span>
    </p>
    <p>
        <span>身份证号：</span>
        <span><?= $buyerIdCard ?></span>
    </p>
    <p>
        <span>丙方：</span>
        <span>楚天财富（武汉）金融服务有限公司</span>
    </p>
    <p>
        <span>法定代表人：韦奇志</span>
    </p>
    <p>
        <span>注册地址：武汉市东湖路181号楚天文化创意产业园8号楼1层</span>
    </p>
    <p>
        <span style="font-weight:bold">鉴于：</span>
    </p>
    <p class="text_index">
        <span>甲方于<?= $loanOrderCreateDate ?>，通过丙方运营互联网金融平台（包括名为“楚天财富”、网址为</span>
        <a style="color:#0563c1" href="https://www.wenjf.com/"><span style="text-decoration:underline">https://www.hbctcf.com</span></a>
        <span>的网站和名为“楚天财富”的手机APP；下称“楚天财富”）认购<?= $loanTitle ?>产品（以下简称本产品），认购金额为<?= $loanOrderPrincipal ?>元。截至该产品的转让之日，该产品尚未到期。</span>
    </p>
    <p class="text_index">
        <span>现甲方由于自身原因，拟通过楚天财富向其他合格投资者（包括个人投资者和机构投资者）部分/全部转让其所持有的本产品（本产品的相关合同中不含有禁止转让条款），甲方拟转让本产品的投资本金金额（以下简称“转让金额”）为<?= $creditOrderPrincipal ?>元（本产品的起购金额≤<?= $creditOrderPrincipal ?>≤<?= $loanOrderPrincipal ?>）。</span>
    </p>
    <p class="text_index"><span>前款所称合格投资者是指具备一定投资条件和识别能力，对拟投资品种自行判断、自行决策、自担风险，、自享收益的自然人或机构。机构投资者应具备以下条件：依法设立并有效存续；具有相关业务资质；具有良好的商业信誉，近三年无严重违法违规行为；具有固定的经营场所和必要的设施；具有健全的内部管理制度；有与承担风险相匹配的资产投资能力和资产额度。个人投资者应具备以下条件：是符合中华人民共和国法律规定的具有完全民事权利和民事行为能力，能够独立承担民事责任的自然人；有来源合法、可自主支配的用于投资的必要资金；有相关产品风险识别和判断能力；具备投资同类金融产品的投资经历或者相关投资能力。</span>
    </p>
    <p class="text_index"><span>乙方系符合本产品的合格投资者标准的楚天财富的注册会员，且乙方同意受让甲方拟转让的本产品。乙方已完整阅读拟受让的本产品的《产品说明书》、《产品认购协议》、《产品风险揭示书》等相关产品合同，完全理解该产品的类型、性质及受让后果，对该产品可能发生的风险有足够的了解和认识并愿意自行承担投资该产品可能带来的财产损益和法律责任。</span>
    </p>
    <p class="text_index"><span>“产品转让服务”仅为楚天财富向甲方（转让方）和乙方（受让方）提供的中介服务。本次产品转让活动涉及资金和权益的处分，由此带来的任何风险包括潜在风险需要甲方和乙方自行判断和承担。</span>
    </p>
    <p class="text_index"><span>现就甲方持有的本产品转让事宜，甲乙丙三方达成以下协议：</span></p>
    <p class="text_index"><span style="font-weight:bold">一、本产品信息：</span></p>
    <p class="text_index"><span>产品名称：<?= $loanTitle ?></span></p>
    <p class="text_index"><span>产品类型：</span><span style="font-style:italic">收益权凭证、定向融资工具、理财计划</span></p>
    <p class="text_index"><span>发行人/管理人：<?= $loanIssuer ?></span></p>
<!--    <p class="text_index"><span>挂牌机构：--><?//= $affiliator ?><!--</span></p>-->
    <p class="text_index"><span>发行规模：<?= $exceptRaisedAmount ?></span></p>
    <p class="text_index"><span>起购金额：<?= $incrAmount ?></span></p>
    <p class="text_index"><span>收益起算日：<?= $interestDate ?></span></p>
    <p class="text_index"><span>产品到期日：<?= $finishDate ?></span></p>
    <p class="text_index"><span>预期年化收益率：<?= $yieldRate ?></span></p>
    <p class="text_index"><span>支付本金及预期收益方式：<?= $refundMethod ?></span></p>
    <p class="text_index"><span style="font-weight:bold">二、本产品的转让价款计算及结算</span></p>
    <p class="text_index">
        <span>2.1乙方成功支付转让价款日为本协议定义的转让成功日。自本产品的收益起算日（含）至转让成功日（含），按照本产品预期收益率计算的投资收益<?= $sellerInterest ?>元归属甲方所有；</span>
        <span style="font-weight:bold">自转让成功日（不含）至本产品到期日（不含），按照本产品预期收益率计算的投资收益<?= $buyerInterest ?>归属乙方所有。产品到期后，产品发行人向乙方支付投资本金及收益<?= $buyerInterest ?>。</span>
    </p>
    <p class="text_index"><span>2.2甲方拟向乙方转让其持有的本产品投资本金金额（转让金额）<?= $creditOrderPrincipal ?>元，且同意以折让率（指产品在转让时折让的比率，折让率可以为零）<?= $discountRate ?>%向乙方转让。截至转让成功日，根据产品合同约定计算的自收益起算日至转让成功日的投资收益为<?= $sellerInterest ?>元，甲方已经收取的投资收益为<?= $refundedInterest ?>元（如有），乙方应向甲方支付的转让价款<?= $creditOrderPayAmount ?>元。产品转让价款t=（转让金额n+自收益起算日至转让成功日的投资收益m-已收取的投资收益j（如有））*（1-折让率v）=<?= $creditOrderPayAmount ?>元。</span></p>
    <p class="text_index">
        <span>2.3因丙方为甲方的产品转让提供了技术服务，甲方同意在产品成功转让后，按照转让金额的<?= $feeRate ?>‰向丙方支付转让手续费<?= $feeAmount ?>元。转让手续费k=转让金额n*转让手续费费率e‰=<?= $feeAmount ?>元。</span>
    </p>
    <p class="text_index">
        <span>2.4甲方、丙方同意在转让成功日一次性结算转让手续费。丙方将从本产品转让价款中直接扣取本协议约定的转让手续费，将扣取手续费后的剩余转让价款划转到甲方在与丙方合作的第三方支付机构开立的账户内。</span>
    </p>
    <p class="text_index">
        <span>三、产品转让协议的签署及生效方式</span>
    </p>
    <p class="text_index">
        <span>甲方在楚天财富网页以点击确认的形式向合格投资者发出的部分/全部转让所持有本产品之要约，乙方在楚天财富网页以点击确认的形式签署本转让协议且成功支付转让价款的，视为向甲方发出的受让甲方持有的部分/全部本产品要约之承诺，本转让协议生效。
        </span>
    </p>
    <p class="text_index">
        <span>四、相关风险揭示</span>
    </p>
    <p class="text_index"><span>4.1 甲方的主要风险</span></p>
    <p class="text_index">
        <span>4.1.1 损失部分利息风险。楚天财富转让规则规定，根据转让人设定的折让率计算所得的转让价款不得低于转让金额（转让金额为转让人拟转让的投资本金，折让率是指产品在转让时折让的比率）。因此，如果您设定的折让率为零，您不会因转让行为损失投资本金和根据合同约定所计算的自收益起算日至转让成功日期间的投资收益；但如果您设定的折让率大于零，您将损失部分投资收益。</span>
    </p>
    <p class="text_index">
        <span>4.1.2 转让失败风险。楚天财富仅为转让人和受让人提供中介服务，并不承诺保证每一个转让申请都能全部/部分成功完成转让。因此，在转让申请时效内，甲方可能面临全部/部分产品转让失败的风险。</span>
    </p>
    <p class="text_index">
        <span>4.2 乙方的主要风险</span>
    </p>
    <p class="text_index">
        <span>4.2.1 发行人经营风险及信用。如在产品存续期间发行人无法继续经营或经营出现困难，则可能会对本产品的本金及收益的兑付产生不利影响。乙方还可能面临因发行人提前兑付或延迟兑付所带来的损失收益及投资机会的风险。</span>
    </p>
    <p class="text_index">
        <span>4.2.2 受让失败风险。乙方受让甲方持有的本产品可能因为网络、操作等问题而导致本产品的认购失败、资金划拨失败等，从而导致乙方的本金及收益发生损失。</span>
    </p>
    <p class="text_index">
        <span>4.2.3 操作风险。包括不可预测或无法控制的系统故障、设备故障、通讯故障、停电等突发事故将有可能给乙方造成一定损失；由于乙方密码失密、操作不当、决策失误、黑客攻击等原因可能会造成损失；网上交易、热键操作完毕，未及时退出，他人进行恶意操作将可能造成损失；委托他人代理交易、或长期不关注账户变化，可能致使他人恶意操作而造成损失；由于银行系统延迟、代扣代收机构系统故障等原因，造成交易双方不能及时收取或支付本协议项下款项。</span>
    </p>
    <p class="text_index">
        <span>以上风险事项仅为列举性质，未能详尽列明甲方、乙方所面临的全部风险和可能导致资产损失的所有因素。</span>
    </p>
    <p class="text_index">
        <span>五、其他</span>
    </p>
    <p class="text_index">
        <span>5.1 乙方在楚天财富以点击形式签署本协议并成功支付转让价款之日起成为继受的产品持有人，享有</span>
        <span><?= $loanTitle ?></span>
        <span>产品的产品合同中约定的所有权利义务。</span>
    </p>
    <p class="text_index">
        <span>5.2 甲方、丙方共同确认，转让价款按照甲方购买<?= $loanTitle ?>产品时的资金路径支付至甲方的账户中。</span>
    </p>
    <p class="text_index">
        <span>5.3 转让成功后，乙方即成为本产品投资者，根据产品相关合同的约定，享有本产品投资者的权利、承担相应义务。</span>
    </p>
    <p class="text_index">
        <span>5.4 本次转让完成后，丙方需及时向南京金融资产交易中心提交产品转让登记申请。</span>
    </p>
    <p class="text_index">
        <span>注：本协议属于产品转让协议的通用版，适用于在丙方发布的所有产品的产品转让行为。投资者（包括转让人和受让人）对本协议条款没有异议的，请以点击确认的方式予以签署。</span>
    </p>
</div>
