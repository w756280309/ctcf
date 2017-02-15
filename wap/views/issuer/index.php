<?php

$issuerId = $issuer->id;
switch ($issuerId) {
    case 2:
        $this->title = '宁富1号三都国资';
        $cssFile = 'sdgz.css?v=20161216';
        break;
    case 5:
        $this->title = '南金交 · 中盛海润1号';
        $cssFile = 'zshr.css';
        break;
    case 3:
        $this->title = '宁富17号北大高科';
        $cssFile = 'bdgk.css';
        break;
    case 10:
        $this->title = '宁富20号中科建';
        $cssFile = 'zkj.css';
        break;
}

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/videojs/video-js.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/introduce/css/<?= $cssFile ?>">
<script src="<?= FE_BASE_URI ?>libs/flex.js"></script>
<script src="<?= FE_BASE_URI ?>libs/videojs/video.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<?php if (2 === $issuerId) { ?>
    <header>
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_03.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_04_new.png" alt="">
    </header>
    <section>
        <div class="publisher">
            <h4>发  行  人</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>贵州省三都水族自治县国有资本营运有限责任公司</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/project_03.png" alt="">
                    <span>贵州省三都水族自治县国有资本营运有限责任公司于2001年7月经县人民政府批准筹建，注册资本1200万元。公司承担政府投资项目融资功能，满足基础设施和城镇化建设融资需要，2014年8月增至30亿元。截止2015年10月公司资产合计346,576万元，净资产308,439万元。</span>
                </div>
            </div>
        </div>
        <div class="guarantor">
            <h4>担  保  方</h4>
            <p><span><i></i></span></p>
            <div class="guacontent">
                <h5>三都水族自治县民族文化旅游投资开发有限责任公司</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/project_04.png" alt="">
                    <span>三都水族自治县民族文化旅游投资开发有限责任公司于2002年成立，法人代表韦景跃，注册资本31,030 万人民币，企业性质为有限责任公司(国有控股)。</span>
                </div>
            </div>
        </div>
        <?php if ($issuer->video) { ?>
            <div class="videointroduce">
                <h4>视  频  介  绍</h4>
                <p><span><i></i></span></p>
                <div class="videobox">
                    <video  id="video" class="media video-js vjs-default-skin" controls poster="<?= $issuer->videoImg ? UPLOAD_BASE_URI.$issuer->videoImg->uri : '' ?>">
                        <source src="<?= $issuer->video->uri ?>" type="video/mp4">
                    </video>
                    <div id="loading"><img src="<?= FE_BASE_URI ?>wap/introduce/img/loading.gif" alt=""></div>
                </div>
            </div>
        <?php } ?>
        <div class="safeguards">
            <h4>保  障  措  施</h4>
            <p><span><i></i></span></p>
            <div class="safecontent">
                <h5>多重保障加码  安心兑付无忧</h5>
            </div>
        </div>
    </section>
    <footer>
        <table>
            <tr>
                <td class="lf" style="line-height: 1rem;">发行人</td>
                <td class="rg" style="line-height: 0.6rem; padding-top: 0.2rem;padding-bottom: 0.2rem;">贵州省三都水族自治县国有资本<br>营运有限责任公司</td>
            </tr>
            <tr>
                <td class="lf">备案登记机构</td>
                <td class="rg">南京金融资产交易中心有限公司</td>
            </tr>
            <tr>
                <td class="lf">产品金额</td>
                <td class="rg">35000万元人民币，可分期募集</td>
            </tr>
            <tr>
                <td class="lf">产品期限</td>
                <td class="rg">2年</td>
            </tr>
            <tr>
                <td class="lf">认购起点</td>
                <td class="rg">5万元起购，按5万元的整数倍递增</td>
            </tr>
            <tr>
                <td class="special">
                    <ul>
                        <li style="width: 100%;">预期年化收益率</li>
                        <li class="lf"  style="width: 60%; padding-left:0.5333rem;">金额</li>
                        <li class="rg" style="padding-right:0.5333rem; color: #8c8c8c;">预期利率/年</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">5万元(含)至20万元(不含)</li>
                        <li class="rg rate">8%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">20万元(含)至50万元(不含)</li>
                        <li class="rg rate">8.2%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">50万元(含)至100万元(不含)</li>
                        <li class="rg rate">8.5%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">100万元(含)及以上</li>
                        <li class="rg rate">8.8%</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="lf" style="line-height: 1rem;">收益分配</td>
                <td class="rg" style="line-height: 0.6rem; padding-top: 0.2rem;padding-bottom: 0.2rem;">每自然半年付息一次，到期一次性返<br>还本金及剩余利息</td>
            </tr>
        </table>
        <?php if ($loansCount) { ?>
            <a href="/issuer/to-loan?issuerid=<?= $issuerId ?>">立即认购</a>
        <?php } ?>
    </footer>
<?php } elseif (5 === $issuerId) { ?>
    <header>
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_01.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_02.png" alt="">
        <h2>中信保内部评级AAA级的企业</h2>
        <p>国际化光伏企业一年期融资项目   实力雄厚有保障</p>
    </header>
    <section>
        <div class="publisher">
            <h4>发  行  人</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>泰通(泰州)工业有限公司</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/project_01.png" alt="">
                    <span>公司成立于2003年11月13日，注册资本8700万元美元（约6亿元人民币）。专业生产太阳能组件，代表着全球光伏行业最尖端的科技力量。在中国、美国和德国拥有三个研发中心。累计向全球90多个国家提供光伏产品。全球化的销售网络带来持续而稳定的营业收入；所在的中盛集团是全球光伏产业中的领军企业。位列2016年全球光伏电站企业20强；2016年中国光伏组建企业20强。</span>
                </div>
            </div>
        </div>
        <div class="guarantor">
            <h4>担  保  方</h4>
            <p><span><i></i></span></p>
            <div class="guacontent">
                <h5>泰州市海润国有资产经营有限公司</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/project_02.png" alt="">
                    <span>泰州市海润国有资产经营有限公司成立于2001年9月26日，注册资本为人民币2,5000万元，是地方    政府平台上国资背景的公司，其母公司泰州海陵资产经营有限公司注册资本20.7亿元，主体评级AA，是国有独资有限责任公司。由海陵区房改办、市路灯管理处作为投资主体，是海陵区主要投融资平台和基础设施建设主体，得到当地政府大力支持。</span>
                </div>
            </div>
        </div>
        <?php if ($issuer->video) { ?>
            <div class="videointroduce">
                <h4>视  频  介  绍</h4>
                <p><span><i></i></span></p>
                <div class="videobox">
                    <video  id="video" class="media video-js vjs-default-skin" controls poster="<?= $issuer->videoImg ? UPLOAD_BASE_URI.$issuer->videoImg->uri : '' ?>">
                        <source src="<?= $issuer->video->uri ?>" type="video/mp4">
                    </video>
                    <div id="loading"><img src="<?= FE_BASE_URI ?>wap/introduce/img/loading.gif" alt=""></div>
                </div>
            </div>
        <?php } ?>
        <div class="safeguards">
            <h4>保  障  措  施</h4>
            <p><span><i></i></span></p>
            <div class="safecontent">
                <h5>多重保障加码  安心兑付无忧</h5>
                <img src="<?= FE_BASE_URI ?>wap/introduce/img/tree.png" alt="">
            </div>
        </div>
    </section>
    <footer>
        <table>
            <tr>
                <td class="lf">发行人</td>
                <td class="rg">泰通(泰州)工业有限公司</td>
            </tr>
            <tr>
                <td class="lf">备案登记机构</td>
                <td class="rg">南京金融资产交易中心有限公司</td>
            </tr>
            <tr>
                <td class="lf">产品金额</td>
                <td class="rg">不超过人民币8000万元，可分期募集</td>
            </tr>
            <tr>
                <td class="lf">产品期限</td>
                <td class="rg">1年</td>
            </tr>
            <tr>
                <td class="lf">认购起点</td>
                <td class="rg">1万元起购，按1万元递增</td>
            </tr>
            <tr>
                <td class="special">
                    <ul>
                        <li style="width: 100%;">预期年化收益率</li>
                        <li class="lf"  style="width: 60%; padding-left:0.5333rem;">金额</li>
                        <li class="rg" style="padding-right:0.5333rem; color: #8c8c8c;">预期利率/年</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">1万-5万元（不含）</li>
                        <li class="rg rate">7.2%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">5万（含）-20万元（不含）</li>
                        <li class="rg rate">7.5%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">20万（含）-100万元（不含）</li>
                        <li class="rg rate">7.7%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">100万（含）及以上</li>
                        <li class="rg rate">8%</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="lf">起息日</td>
                <td class="rg">T+5个自然日</td>
            </tr>
            <tr>
                <td class="lf">收益分配</td>
                <td class="rg">按季度付息，到期偿还本金及末期利息</td>
            </tr>
        </table>

        <?php if ($loansCount) { ?>
            <a href="/issuer/to-loan?issuerid=<?= $issuerId ?>">立即认购</a>
        <?php } ?>
    </footer>
<?php } elseif (3 === $issuerId) { ?>
    <header>
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_05.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_06.png" alt="">
    </header>
    <section>
        <div class="publisher pub-special">
            <h4>产  品  亮  点</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>北大青鸟集团</h5>
                <div>
                    <span>北大青鸟集团成立于1992年11月1日，是北京大学下属大型高科技企业集团，国有控股企业集团。北大青鸟集团源于国家支持的计算机软件重大科技攻关项目“青鸟工程”，是“青鸟工程”科技成果的转化机制。在“青鸟工程”的基础上，北大青鸟集团面向行业，推动科研成果向市场产品转化，秉承“以人才为根本，以技术为核心，以产品为依托”的经营方针，为中国信息化建设和中国软件行业腾飞不断开拓。</span>
                    <span>北大青鸟集团紧密依托北京大学优良、丰富的资源，以创新求发展，目前已形成包括IT（及制造业）、教育、文化传媒、地产、新能源、旅游、金融在内的七大产业，旗下拥有数十家企业，员工近万余名，资金雄厚，业务范围遍及全国20多个主要城市，资本运营力度较大、投资回报较为显著。</span>
                </div>
            </div>
        </div>
        <div class="guarantor">
            <h4>还  款  来  源</h4>
            <p><span><i></i></span></p>
            <div class="guacontent">
                <h5>两大还款来源</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/moneyfrom_01.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/moneyfrom_02.png" alt="">
                </div>
            </div>
        </div>
        <div class="publisher">
            <h4>增  信  措  施</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>校办企业国企担保</h5>
                <div>
                    <span>北京北大科技实业发展中心向发行人出具《担保函》，约定为本定向融资工具本金及利息的到期偿付提供全额无条件不可撤销的连带责任保证担保。保证范围为本定向融资工具的本金及投资收益，以及违约金、损害赔偿金、实现债权的费用和其他应支付的费用；保证期间为本定向融资工具存续期及本定向融资工具到期日起二年。</span>
                </div>
            </div>
        </div>
        <div class="publisher">
            <h4>发  行  人</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>北京北大高科技产业投资有限公司</h5>
                <div>
                    <span>北京北大高科技产业投资有限公司是由广州北大青鸟商用信息系统有限公司、北京东方国兴科技发展有限公司、北京北大博雅投资有限公司共同出资组建的有限责任公司。</span>
                    <span>注册资本7.8亿公司是一家依托北京大学和北大青鸟集团从事教育产业、计算机及制造业、旅游产业及金融产业专业投资和主动管理的企业。</span>
                    <span>截止 2015年12月，总资产63亿，有效净资产26.53亿。</span>
                </div>
            </div>
        </div>
        <div class="publisher">
            <h4>担  保  方</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>北京北大科技实业发展中心</h5>
                <div>
                    <span>北京北大科技实业发展中心（以下简称北大科实）是于1995年经北京大学批准组建的国有高科技集团，唯一股东为北京大学。是北大青鸟集团旗下新能源高科技领域的龙头企业。</span>
                    <span>近年来，北大科实充分发挥自身产业优势和北大青鸟集团的集成优势， 2007年以来，资产规模增长107倍,营业收入增长921倍，利润增长76倍，国有资产保值增值水平在北京大学的企业中位居前列。</span>
                </div>
            </div>
        </div>
        <div class="safeguards">
            <h4>保  障  措  施</h4>
            <p><span><i></i></span></p>
            <div class="safecontent">
                <h5>多重保障加码  安心兑付无忧</h5>
            </div>
        </div>
    </section>
    <footer>
        <table>
            <tr>
                <td class="lf">发行人</td>
                <td class="rg">北京北大高科技产业投资有限公司</td>
            </tr>
            <tr>
                <td class="lf">备案登记机构</td>
                <td class="rg">南京金融资产交易中心有限公司</td>
            </tr>
            <tr>
                <td class="lf">产品金额</td>
                <td class="rg">总计20000万元，可分期募集</td>
            </tr>
            <tr>
                <td class="lf">产品期限</td>
                <td class="rg">12个月</td>
            </tr>
            <tr>
                <td class="lf">认购起点</td>
                <td class="rg">5万元起购，按1万元递增</td>
            </tr>
            <tr>
                <td class="lf">预期年化收益率</td>
                <td class="rg" style="color: #0080ff;">7.2%/年</td>
            </tr>
            <tr>
                <td class="lf">起息日</td>
                <td class="rg">T+5个自然日</td>
            </tr>
            <tr>
                <td class="lf" style="line-height: 1rem;">收益分配</td>
                <td class="rg" style="line-height: 0.6rem; padding-top: 0.2rem;padding-bottom: 0.2rem;text-align: right;">每自然半年度付息，到期偿还本金<br>及末期利息</td>
            </tr>
        </table>
        <?php if ($loansCount) { ?>
            <a href="/issuer/to-loan?issuerid=<?= $issuerId ?>">立即认购</a>
        <?php } ?>
    </footer>

    <script>
        window.onload = function() {
            FastClick.attach(document.body);
        }
    </script>
<?php } elseif (10 === $issuerId) { ?>
    <header>
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_09.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_10.png" alt="">
    </header>
    <section>
        <div class="publisher pub-special">
            <h4>发  行  人</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>中科建飞投资控股集团有限公司</h5>
                <div>
                    <span>中科建飞，中科建设开发总公司的全资子公司，成立于2015年1月，主营业务实业投资、房地产开发。截至2016年3月，货币资金4亿，总资产64.27亿，净资产13.97亿，净利润11.54亿，现金流余额1.26亿，资产负债率78%。</span>
                </div>
            </div>
        </div>
        <div class="publisher">
            <h4>担  保  方</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>中科建设开发总公司</h5>
                <div>
                    <span>中科建设开发总公司，中科院100%控股公司。2015年末合并总资产123.73亿元，净资产33.44亿元，营业收入154亿，现金流余额11.35亿，资产负债率73%；母公司总资产101.45亿，净资产31.74亿，营业收入110亿，现金流余额4.31亿。</span>
                    <span>2015年中诚信对总公司信用评级AA，评级展望稳定，贷款卡情况良好，无逾期欠息记录。</span>
                    <span>总公司于2015年12月发行15亿公司债券，承销商为招商银行。</span>
                    <span>总公司拥有四个一级资质，房屋建筑工程施工总承包、市政公用工程施工总承包、钢结构工程施工专业承包、建筑装饰装修工程专业承包、机电设备安装工程专业承包均为一级资质。</span>
                    <span>截至2015年底，总公司在建项目超过600亿，预计在16年到17年，建筑施工板块预计可为中科建带来超过300亿的主营业务收入，该部分可构成中科建总公司较为稳定的还款来源。</span>
                </div>
            </div>
        </div>
        <div class="guarantor">
            <h4>还  款  来  源</h4>
            <p><span><i></i></span></p>
            <div class="guacontent">
                <h5>两大还款来源</h5>
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/moneyfrom_03.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/introduce/img/moneyfrom_04.png" alt="">
                </div>
            </div>
        </div>
        <div class="publisher">
            <h4>增  信  措  施</h4>
            <p><span><i></i></span></p>
            <div class="pubcontent">
                <h5>中科建总公司担保</h5>
                <div>
                    <span>中科建设开发总公司承担连带责任保证担保。</span>
                </div>
            </div>
        </div>
        <div class="safeguards">
            <h4>保  障  措  施</h4>
            <p><span><i></i></span></p>
            <div class="safecontent">
                <h5>多种保障加码  安全兑付无忧</h5>
            </div>
        </div>
    </section>
    <footer>
        <table>
            <tr>
                <td class="lf">发行人</td>
                <td class="rg">中科建飞投资控股集团有限公司</td>
            </tr>
            <tr>
                <td class="lf">备案登记机构</td>
                <td class="rg">南京金融资产交易中心有限公司</td>
            </tr>
            <tr>
                <td class="lf">产品金额</td>
                <td class="rg">1亿，分期发行</td>
            </tr>
            <tr>
                <td class="lf">产品期限</td>
                <td class="rg">1年</td>
            </tr>
            <tr>
                <td class="lf">认购起点</td>
                <td class="rg">1万元起购，以1万元的整数倍递增</td>
            </tr>
            <tr>
                <td class="special">
                    <ul>
                        <li style="width: 100%;">预期年化收益率</li>
                        <li class="lf"  style="width: 60%; padding-left:0.5333rem;">金额</li>
                        <li class="rg" style="padding-right:0.5333rem; color: #8c8c8c;">预期利率/年</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">1万-5万元（不含）</li>
                        <li class="rg rate">7.2%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">5万（含）-20万元（不含）</li>
                        <li class="rg rate">7.5%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">20万（含）-100万元（不含）</li>
                        <li class="rg rate">7.7%</li>
                        <li class="lf"  style="width: 70%; padding-left:0.5333rem;">100万（含）及以上</li>
                        <li class="rg rate">8.0%</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="lf" style="line-height: 1rem;">收益分配</td>
                <td class="rg" style="line-height: 0.6rem; padding-top: 0.2rem;padding-bottom: 0.2rem;text-align: right;">每一个自然季度付息一次，到期偿还<br>投资本金及最后一笔利息</td>
            </tr>
        </table>
        <?php if ($loansCount) { ?>
            <a href="/issuer/to-loan?issuerid=<?= $issuerId ?>">立即认购</a>
        <?php } ?>
    </footer>
<?php } ?>

<script>
    $(function () {
        $('.container').removeClass('container');
    })
    <?php if ($issuer->video) { ?>
        window.onload = function() {
            FastClick.attach(document.body);
            var media = document.getElementById('video');
            var loading = document.getElementById('loading');
            media.onclick = function() {
                if (media.paused) {
                    media.play();
                } else {
                    media.pause();
                }
            };
            media.onwaiting = function() {
                loading.style.display = 'block';
            }
            media.oncanplay = function() {
                loading.style.display = 'none';
            }
        }
    <?php } ?>
</script>