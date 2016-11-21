<?php

$this->title = "安全保障";

?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/safeguard.css">

<div class="safeguard-box clearfix">
    <div class="safeguard-banner">
        <div class="banner"></div>
    </div>
    <div class="safeguard-container">
        <div class="safeguard-container-box">
            <div class="safeguard-icons">
                <ul class="icons">
                    <li class="active"><a href="javascript:;"><img src="../images/safeguard/safeguard-icon1.png" alt="专业合规"></a><p>专业合规</p></li>
                    <li class=""><a href="javascript:;"><img src="../images/safeguard/safeguard-icon2.png" alt="风控先进"></a><p>风控先进</p></li>
                    <li class=""><a href="javascript:;"><img src="../images/safeguard/safeguard-icon3.png" alt="信息安全"></a><p>信息安全</p></li>
                    <li class="last"><a href="javascript:;"><img src="../images/safeguard/safeguard-icon4.png" alt="数据安全"></a><p>数据安全</p></li>
                    <div class="clear"></div>
                </ul>
                <p class="arrow"></p>
                <div class="safetxt">
                    <div class="safetxt-icon ">
                        <h3>为什么在温都金服投资是安全的？</h3>
                        <h4>（1）对接专业机构产品</h4>
                        <p class="p-padding-lf">温都金服理财的产品主要为各类金融机构产品、优质企业政信类产品，国有平台挂牌金融资产项目，风险更低，且有多重还款进行保障。</p>

                        <h4>（2）坚持合法合规运营</h4>
                        <p class="p-padding-lf">温都金服恪守银监会、地方金融监管部门及证监会、股转系统的监管要求，上线之初严格要求，坚持合法合规运营；</p>

                        <h4>（3）坚持资金与平台分离</h4>
                        <p class="p-padding-lf">温都金服严守平台规范。用户投资的每一笔资金都经过第三方支付平台联动优势（由中国银联和中国移动联合组建），一一匹配真实债权，对应每个资产项目；不设资金池，平台与用户资金严格分离，所有资金均由联动优势全程托管。</p>

                        <h4>（4）股东背景强势</h4>
                        <p class="p-padding-lf">温州温都金融信息服务有限公司（简称温都金服）由温州报业传媒、南京金融资产交易中心等股东构成，其强大的媒体公信力、专业的金融机构背景，助力温都金服全力发展互联网金融业务，给予温都金服理财更长足的发展动力。</p>
                    </div>

                    <div class="safetxt-icon hide">
                        <h3>温都金服理财的风控先进在哪？</h3>
                        <p class="p-margin-top ">温都金服平台自身不生产资产。获取资产的方式是通过股东资源、合作机构、团队专业性等多重维度，筛选第三方优质资产方以拓展资产端资源，将其做好风控的项目或标的放到平台上与投资者对接。这些资产方包括南京金融资产交易中心、银行类理财产品、优质企业政信类产品等优质债权提供方。</p>
                    </div>

                    <div class="safetxt-icon hide">
                        <h3>温都金服怎么保证客户的信息安全？</h3>
                        <h4>温都金服采用五大防控措施，充分保障用户的个人信息安全。</h4>
                        <p>（1）可靠系统：运行于行业领先的云架构，冗余设计加多重备份确保业务高速稳定</p>
                        <p>（2）严控数据：采用先进的通信、信息加密技术对关键数据进行审核及保密，确保信息安全</p>
                        <p>（3）证书加密：采用先进的256位SSL服务器证书加密传输数据，确保网络传输安全</p>
                        <p>（4）保护隐私：采取多重多维度的隐私保护机制，严格保护用户的隐私信息</p>
                        <p>（5）先进架构：通过安全扫描、定期排查等多种手段加固平台安全，实现主动防御技术保障体系</p>
                    </div>

                    <div class="safetxt-icon hide">
                        <h3>温都金服线上的电子合同是否有效？</h3>
                        <p class="p-margin-top">最新修正的《刑法诉讼法》、《民事诉讼法》均将电子数据列为证据的一种，电子数据保全中心提供的保全证书，可作为司法人员和律师分析、认定案件事实的有效证据，让受保者在司法纠纷中占据有利地位。电子数据保全中心还可以为受保者提供合作机构出具的公证书或司法鉴定书。</p>
                        <p class="p-margin-top">温都金服联手易保全电子数据保全中心，为投资者提供交易凭证保全服务，交易凭证（担保函、担保合同等信息）一旦保全，其内容、生成时间等信息将被加密固定，且生成唯一的保全证书供下载。事后任何细微修改，都会导致保全证书函数值变化，有效防止人为篡改。如发生司法纠纷，保全证书持有人，可以通过易保全电子数据保全中心提供的认证证书向法院或仲裁机构提供有效、可靠的证据，从而获得举证的优势地位。</p>
                        <p class="p-margin-top">电子数据保全中心打通保全存证、司法鉴定、法律服务等环节，以电子数据第三方保全为核心的平台，该平台目前已获得三项专利，6项国家CNAS资格认证，与司法鉴定中心、公证处实行对接，实时进行保全的信息同步，是国内最大的电子数据保全平台。</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.icons li').each(function(){

            var index=$(this).index();

            $('.icons li').eq(index).on('click',function(){
                $('.icons li').each(function(){$(this).removeClass('active')}).eq(index).addClass('active');
                $('.safetxt-icon').each(function(){$(this).addClass('hide')}).eq(index).removeClass('hide');
            });
        });
    });
</script>
