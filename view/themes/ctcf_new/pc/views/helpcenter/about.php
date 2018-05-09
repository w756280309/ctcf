<?php

$this->title = '关于我们';
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/help/about.css?v=1.0', ['depends' => 'frontend\assets\FrontAsset']);

?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="about-box1">
                <div class="about-header">
                    <span class="about-header-font">关于我们</span>
                </div>
                <div class="about-content">
                    <p align="center">楚天财富（武汉）金融服务有限公司简介</p>
                    <p>
                        楚天财富（武汉）金融服务有限公司（以下简称“楚天财富”）系湖北日报新媒体集团旗下控股公司，是湖北省首家具有国资背景的从事互联网金融综合信息服务的企业，是国内传统“纸媒”行业响应国家“十二五”规划和新时代发展战略的创新举措。作为财富管理平台，楚天财富在武汉的“产业与资本，金融与民生”战略规划中深耕细作，深度挖掘战略发展契合点并抓住联动机遇，一方面服务于符合国家未来发展规划的产业和企业，另一方面为出借者提供更丰富的资产配置策略与出借渠道。未来，楚天财富将由湖北武汉逐步辐射至全国，实现其作为传媒集团旗下互联网金融服务平台的多元化和规模化发展。</p>
                    <p>
                        湖北荆楚网络科技股份有限公司，是全国首家挂牌新三板市场的省级全国重点新闻网站（830836），由湖北日报传媒集团（以下简称“传媒集团”）控股。传媒集团系由1949年创刊的中共湖北省委机关报——湖北日报于2007年4月组建。传媒集团在深厚的历史积淀基础之上，经过十余年开拓创新，已发展成为一家以湖北日报为核心，拥有11报12刊5网站和1家出版机构、21个全资公司的综合性传媒集团，在湖北省内拥有巨大影响力。</p>
                    <p>
                        湖北新海天出借有限公司（以下简称“新海天出借”）于 2001年6月创立，是一家以自主深度研究为基础、专业从事中国境内出借的出借管理公司。新海天出借历经十七年成长，已经成为一家以金融业务为核心的资本经营管理集团，公司聚焦资本领域，着力打造核心竞争力。新海天出借拥有一批专业背景深厚，从业经验丰富的经营管理人才，拥有良好的政府关系和广泛的人脉资源，专注资本领域，致力于为中国高净值出借者、企业和国际出借机构提供专业的出借管理服务。</p>

                    <p class="zizhi">
                        <span>——————</span>
                        平台资质
                        <span>——————</span>
                    </p>
                    <p class="business-license">
                        <img  src="<?= ASSETS_BASE_URI ?>ctcf/images/help/business-license.jpg">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
