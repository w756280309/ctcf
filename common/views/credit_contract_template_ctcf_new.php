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
        <span>合同编号：</span>
        <span><?= $contractNum ?></span>
    </p>
    <p>
        <span>本协议由以下各方于<?= $loanOrderCreateDate ?>在武汉市武昌区签订：</span>
    </p>
    <p>
        <span>甲方（债权转让人）：</span>
        <span><?= $sellerName ?></span>
    </p>
    <p>
        <span>身份证号码：</span>
        <span><?= $sellerIdCard ?></span>
    </p>
    <p>
        <span>楚天财富用户名：</span>
        <span><?= $sellerMobile ?></span>
    </p>
    <p>
        <span>乙方（债权受让人）：</span>
        <span><?= $buyerName ?></span>
    </p>
    <p>
        <span>身份证号码：</span>
        <span><?= $buyerIdCard ?></span>
    </p>
    <p>
        <span>楚天财富用户名：</span>
        <span><?= $buyerMobile ?></span>
    </p>
    <p>
        <span>丙方（服务方）：</span>
        <span>楚天财富金融服务有限公司</span>
    </p>
    <p>
        <span>住所：武汉市武昌区东湖路楚天181产业园8号楼1层</span>
    </p>
    <p class="text_index">
        <span>鉴于甲方在丙方楚天财富金融服务有限公司运营的楚天财富平台（网址：https://www.hbctcf.com//移动客户端）上对债务人<?= $affiliator ?>拥有合法债权，现甲、乙双方本着诚实、自愿的原则，就甲方通过丙方楚天财富平台向乙方转让债权事宜，达成如下协议：</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第一条 债权转让标的</span>
    </p>
    <p class="text_index">
        <span>1、借款人：<?= $affiliator ?>；</span>
    </p>
    <p class="text_index">
        <span>2、借款项目名称：<?= $loanTitle ?>；</span>
    </p>
    <p class="text_index">
        <span>3、债权转让标的金额：<?= $creditOrderPrincipal + $sellerInterest - $refundedInterest; ?> 元；</span>
    </p>
    <p class="text_index">
        <span>4、债权年化利率(365天/年)：<?= $yieldRate ?>；</span>
    </p>
    <p class="text_index">
        <span>5、转让后债权期限：<?= $interestDate ?>到<?= $finishDate ?>；</span>
    </p>
    <p class="text_index">
        <span>6、还款方式：<?= $refundMethod ?>。</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第二条 债权转让价款</span>
    </p>
    <p class="text_index">
        <span>1、债权转让价款为：<?= $creditOrderPayAmount ?>元</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第三条 债权转让流程</span>
    </p>
    <p class="text_index">
        <span>3.1 本协议自乙方在楚天财富平台点击“确定转让”时成立且视为乙方不可撤销地授权丙方有权委托银行或第三方支付机构，将转让价款在扣除甲方应支付给丙方的转让管理费之后划转给甲方，上述转让价款划转完成即视为本协议生效且标的债权转让成功。</span>
    </p>
    <p class="text_index">
        <span>3.2 本协议生效后，丙方视情况（必要时）通知与标的债权对应的借款人。</span>
    </p>
    <p class="text_index">
        <span>3.3 自标的债权转让成功时，乙方成为标的债权的新债权人，承继网络借贷相关协议项下出借人的权利和义务。</span>
    </p>
    <p class="text_index">
        <span>3.4 丙方有权对甲方提交的债权转让申请进行审核，以确保该转让申请符合法律法规及楚天财富平台交易规则的要求。</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第四条</span> 甲方因转让债权而产生的相关费用及支付方式见楚天财富平台在债权转让模块的展示信息。
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第五条 保证与承诺</span>
    </p>
    <p class="text_index">
        <span>5.1 甲方保证其转让的债权系其合法、有效的债权，不存在转让的限制。甲方同意并承诺按本协议及楚天财富网站有关规则向丙方支付转让管理费。</span>
    </p>
    <p class="text_index">
        <span>5.2 乙方保证其出借资金来源合法，是其出借资金的合法所有人，完全有权出借该笔资金。如果第三人对资金归属、合法性问题提出异议，由乙方负责解决并自行承担相关责任，与本协议其他各方无关。</span>
    </p>
    <p class="text_index">
        <span>5.3 甲乙双方应对在本协议签订过程中所获悉的其他方信息，包括但不限于个人身份信息、公司财务信息及商业信息等，承担保密义务。非经书面同意，任何一方均不得将上述任何信息向其他第三方擅自转让或披露，违反保密义务给他方造成损失的，由违约方承担全部责任。</span>
    </p>
    <p class="text_index">
        <span>5.4 丙方为甲乙双方的债权转让和受让交易提供信息搜集、信息公布、信息交互、借贷撮合等服务，针对楚天财富网站上的债权，丙方仅在债权形成前对借款人的借款需求及相关信息进行必要审核，如果发生债权转让，丙方不会在债权转让时再次对借款人进行信用审核，并且丙方不对借款人相关信息的真实性、准确性、完整性和合法性作出保证，不对借款人还款能力及本协议的履行做出任何明示或默示的担保或保证，也不对甲方本金及/或收益做出任何明示或默示的担保或保证。丙方将依据相关法律法规的要求对于网络借贷有关信息进行披露，但甲乙双方之间因网络借贷交易发生的或与交易有关的任何纠纷，应由纠纷各方自行解决，网络借贷交易风险应由甲乙双方各自承担，丙方不承担任何交易风险及法律责任。</span>
    </p>
    <p class="text_index">
        <span>5.5 乙方自本协议项下的债权成功转让之日起承继甲方在原借款合同下的所有权利义务。</span>
    </p>
    <p class="text_index">
        <span>5.6 乙方受让后的债权可通过楚天财富平台再次转让给在楚天财富平台注册的第三人，但转让前乙方应至少持有受让后的债权三十个自然日，否则不得再次转让。</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第六条 违约责任</span>
    </p>
    <p class="text_index">
        <span>6.1 各方同意，如果一方违反其在本协议中所作的保证、承诺或任何其他义务，致使其他方遭受或发生损害、损失等责任，违约方须向守约方赔偿守约方因此遭受的一切经济损失，包括但不限于守约方为实现权利支付的调查费、律师费和诉讼费等。</span>
    </p>
    <p class="text_index">
        <span>6.2 各方均有过错的，应根据各方实际过错程度，分别承担各自的违约责任。</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第七条 适用法律和争议解决</span>
    </p>
    <p class="text_index">
        <span>7.1 本协议的签订、履行、终止、解释均适用中华人民共和国法律。</span>
    </p>
    <p class="text_index">
        <span>7.2 本协议在履行过程中，如发生任何争执或纠纷，各方应友好协商解决；若协商不成，任何一方均有权向丙方所在地人民法院提起诉讼。</span>
    </p>
    <p class="text_index">
        <span style="font-weight:bold">第八条 其他</span>
    </p>
    <p class="text_index">
        <span>8.1 本协议自约定的债权转让价款支付至甲方在丙方合作的银行或第三方支付机构开设的账户之日起生效。</span>
    </p>
    <p class="text_index">
        <span>8.2 本协议的签署、生效和履行以不违反中国的法律法规为前提。如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。</span>
    </p>
    <p class="text_index">
        <span>8.3 本协议及其附件均通过楚天财富网站以电子文本形式制成，可以一份或多份，每份具有同等法律效力。本协议在本协议有效期间及终止后5年内，由楚天财富在专用服务器上保管和备查。</span>
    </p>
    <p class="text_index">
        <span>8.4 本协议经各方协商一致，可以以电子文本形式做出修改和补充。补充协议是本协议组成部分，与本协议具有同等法律效力。</span>
    </p>
    <p class="text_index">
        <span>甲方：<?= $sellerName ?></span>
    </p>
    <p class="text_index">
        <span>乙方：<?= $buyerName ?></span>
    </p>
    <p class="text_index">
        <span>丙方（服务方）：楚天财富（武汉）金融服务有限公司</span>
    </p>
</div>
