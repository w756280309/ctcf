<?php

$this->title = "帮助中心";

?>

<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/helpcenter/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="help-box">
                <div class="about-header about-header-margin">
                    <span class="about-header-font">产品篇</span>
                </div>
                <!-- 主体 -->
                <div class="kong-width">
                    <div class="row single">
                        <h1>资产品种：</h1>
                        <p>温都金服共包括三款理财产品，分别是温盈金，温盈宝，温股投。</p>
                        <p><b>温盈金：</b>由各类金融机构提供全额本金和收益保障，预期年化利率4%-6.5%，1000元起投，投资期限1-12个月。</p>
                        <p><b>温盈宝：</b>由各类金融机构/优质企业/政府平台提供全额本金和收益保障，预期年化利率6.5%-9%，1万元起投，投资期限1-24个月。</p>
                        <p><b>温股投：</b>温都金服甄选优秀的股权投资基金管理机构，推出“温股投”系列产品。以私募基金形式，向合资格投资者定向非公开开放。预期回报3-6倍，100万元起投，投资期限2~5年。</p>
                    </div>
                    <div class="row single">
                        <h1>特点和优势：</h1>
                        <p>1、品种丰富：分有短期中期和长期，适合不同客户的需要。
                        </p>
                        <p>2、风险低：产品为各类金融机构产品、优质企业政信类产品，国有平台挂牌金融资产项目，由南京金融资产交易中心把控风险，全程持续监管融资方的运作，让客户没有后顾之忧。
                        </p>
                        <p>3、增信措施：强大的担保方，由信用评级为AA的国企做担保，提供不可撤销的连带责任担保。
                        </p>
                        <p>4、信息安全：有专业、强大的第三方资金托管平台对客户资金进行全面监管和保护，保证客户信息、账户的安全。
                        </p></div>
                    <div class="row single">
                        <h1>收益计算：</h1>
                        <p>到期本息项目为按天计息，项目截止日一次性偿还本金+利息。
                        </p>
                    </div>
                    <div class="row single">
                        <h1>结息计算：</h1>
                        <p>项目的还款方式分为分期及到期本息项目，年化利率按365天计算。
                        </p>
                        <p>计算公式：利息=（本金*年化率/365）* N天
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

