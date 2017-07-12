<?php

$this->title = '平台优势';
$this->registerCssFile(ASSETS_BASE_URI.'css/help/advantage.css', ['depends' => 'frontend\assets\FrontAsset']);

?>

<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="advantage-box">
                <div class="advantage-header">
                    <span class="advantage-header-font">平台优势</span>
                </div>
                <div class="advantage-content">
                    <div class="section">
                        <div class="number">1</div>
                        <span class="focus">国有</span><span>平台</span>
                        <p>隶属于湖北省级国有传媒旗下机构，国资平台，值得信赖；</p>
                    </div>

                    <div class="section">
                        <div class="number">2</div>
                        <span class="focus">股东</span><span>强势</span>
                        <p>系湖北日报新媒体集团控股子公司；</p>
                    </div>

                    <div class="section">
                        <div class="number">3</div>
                        <span>资金</span><span class="focus">安全</span>
                        <p>资金全程托管，与平台隔离不被挪用；</p>
                        <p>绑定本人银行卡，同卡进出，保证本人操作；</p>
                        <p>层层风控，专业稳健，合法合规；</p>
                    </div>

                    <div class="section">
                        <div class="number">4</div>
                        <span>产品</span><span class="focus">优质</span>
                        <p>主流金融机构产品、优质政府平台类产品、</p>
                        <p>优质供应链金融产品，期限多样，收益可观；</p>
                    </div>

                    <div class="section">
                        <div class="number">5</div>
                        <span>服务</span><span class="focus">便捷</span>
                        <p>线上有平台，线下有门店，楚天财富在您身边；</p>
                        <p>千元起投，手机操作，随时随地实现财富增值。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
