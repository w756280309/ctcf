<?php

$this->title = '温股投';

$this->registerCssFile(ASSETS_BASE_URI.'css/booking/introduction.css', ['depends' => 'frontend\assets\FrontAsset']);

?>

<div class="mask mask-show"></div>
<div class="mask-over">
    <p class="mask-over-head">提示</p>
    <div class="mask-over-content">
        <p>根据《私募投资基金监督管理暂行办法》第四章第十四条规定：“私募基金管理人、私募基金销售机构不得向合格投资者之外的单位和个人募集资金，不得通过报刊、电台、电视、互联网等公众传播媒体或者讲座、报告会、分析会和布告、传单、手机短信、微信博客和电子邮件等方式，向不特定对象宣传推介。”</p>
        <p>温服金服谨遵《私募投资基金监督管理暂行办法》之规定，只向特定的合格投资者宣传推介相关私募投资基金产品。</p>
        <p>
            阁下如有意进行私募投资基金投资且满足《私募投资基金监督管理暂行办法》关于“合规投资者”标准之规定，即具备相应风险识别能力和风险承担能力，投资于单只私募基金的金额不低于100万元，且个人金融类资产不低于300万元或者最近三年个人年均收入不低于50万元人民币。请阁下详细阅读本提示，并注册成为温都金服”温股投“特定的合规投资者，方可获得私募投资基金产品宣传推介服务。</p>
    </div>
    <a class="confirm-inner" href="/order/booking/book?pid=<?= $model->id ?>">确定预约</a>
</div>
<div class="introduction-box">
    <div class="introduction-header">
        <p><span>过去十年</span>，是不动产资产为王的时代。</p>
        <p>把握不动产升值趋势的家庭，实现了<span>财富梦想</span>。</p>
        <p><span>未来十年</span>，是金融资产为王的时代。</p>
        <p>能够聪明配置金融资产的家庭，将<span>引领财富增长</span>。</p>
        <p><span>而股权资产，是金融资产中的钻石。</span></p>
    </div>
    <div class="introduction-content">
        <h3><span></span>温股投介绍</h3>
        <p>温都金服甄选优秀的股权投资基金管理机构，为市民家庭提供良好的股权投资机会，推出“温股投”系列产品。以私募基金形式，向合资格投资者定向非公开开放。</p>
    </div>
    <div class="introduction-content">
        <h3><span></span>投资方向</h3>
        <p>第一期温股投产品，投向中国一二线城市的优秀青年白领公寓企业，分享中国城市青年租房需求升级的红利。</p>
    </div>
    <div class="introduction-content">
        <h3><span></span>退出方式</h3>
        <p>被并购，IPO，股东回购，投资份额转让等。</p>
    </div>
    <div class="introduction-content">
        <h3><span></span>投资期限</h3>
        <p>2+2年。</p>
    </div>
    <div class="introduction-content">
        <h3><span></span>起投金额</h3>
        <p><?= $model->min_fund ?>万元人民币。</p>
    </div>
    <div class="introduction-content">
        <h3><span></span>基金管理人</h3>
        <p>正规持牌备案，合法合规运行，投资能力卓越。收房租，好生意。温股投，只投稳妥的股权。有意者，请点击预约登记。专业投资机构将派专人为您服务。</p>
    </div>
    <?php if ($end_flag) { ?>
        <a class="link-arrange" href="Javascript:void(0)">预约结束</a>
    <?php } elseif($exist_flag) { ?>
        <a class="link-arrange" href="Javascript:void(0)">已预约</a>
    <?php } else { ?>
        <a class="link-arrange" style="background-color: #f44336;" onclick="$('.mask-show').show();$('.mask-over').show();">预约</a>
    <?php } ?>
</div>