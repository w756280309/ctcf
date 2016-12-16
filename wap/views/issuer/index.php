<?php

$this->title = 1 === $type ? '宁富1号三都国资' : '南金交 · 中盛海润1号';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/base.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/videojs/video-js.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/introduce/css/<?= 1 === $type ? 'sdgz.css' : 'zshr.css' ?>">
<script src="<?= FE_BASE_URI ?>libs/flex.js"></script>
<script src="<?= FE_BASE_URI ?>libs/videojs/video.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<?php if (1 === $type) { ?>
    <header>
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_03.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/introduce/img/banner_04.png" alt="">
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
        <?php if ($loan->issuerInfo && $loan->issuerInfo->video) { ?>
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
        <a href="/deal/deal/detail?sn=<?= $loan->sn ?>">立即认购</a>
    </footer>
<?php } else { ?>
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
        <?php if ($loan->issuerInfo && $loan->issuerInfo->video) { ?>
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
        <a href="/deal/deal/detail?sn=<?= $loan->sn ?>">立即认购</a>
    </footer>
<?php } ?>

<script>
    window.onload = function() {
        FastClick.attach(document.body);
        $('.container').removeClass('container');
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
</script>
