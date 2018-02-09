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
                    <p>
                        楚天财富（武汉）金融服务有限公司是湖北日报新媒体集团控股子公司、具有国资背景的、专业从事互联网金融服务的企业，是湖北省首家按照省人民政府办公厅《关于规范发展民间融资机构的意见》（鄂政办发〔2014〕65号）文件精神设立的互联网金融服务公司，经过相关监管部门备案，明确以“个人、企业网络借贷信息中介服务”为主营业务。</p>
                    <p>
                        湖北日报新媒体集团：湖北日报是中共湖北省委机关报，创刊于1949年。2007年4月组建湖北日报传媒集团，现有总资产超过60亿元，在全省17个市州和北上广均设有机构。已成为一家以《湖北日报》党报为核心，拥有11报12刊5网站和1家出版机构、21个全资公司的综合性传媒集团。旗下拥有发行量超过百万的《楚天都市报》、以及《楚天金报》、《特别关注》等系列媒体刊物，在湖北省内拥有巨大影响力。</p>
                    <p>
                        湖北荆楚网络科技股份有限公司：由湖北日报传媒集团控股，是全国首家挂牌新三板市场的省级全国重点新闻网站。公司简称“荆楚网”，证券代码830836。旗下拥有《湖北手机报》、《荆楚网》、《腾讯•大楚网》等5家网站。其中，《荆楚网》是由中共湖北省委宣传部、湖北省人民政府新闻办公室主管，国务院新闻办公室批准的湖北省唯一重点新闻网站。</p>

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
