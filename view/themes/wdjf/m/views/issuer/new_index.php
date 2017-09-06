<?php

$this->title = $jxPage->title;
extract(unserialize($jxPage->content));

?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/videojs/video-js.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/introduce/css/zkj.css?v=1.2">
<script src="<?= FE_BASE_URI ?>libs/flex.js"></script>
<script src="<?= FE_BASE_URI ?>libs/videojs/video.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<?php if (!empty($pic)) { ?>
<header>
    <img src="<?= UPLOAD_BASE_URI . $pic ?>" alt="">
</header>
<?php } ?>

<section>
    <?php foreach ($zdy as $k => $zy) { ?>
    <div class="publisher <?php if (0 === $k) { ?>pub-special<?php } ?>">
        <?php
            if (empty(array_filter($zy))) {
                continue;
            }
        ?>
        <?php if (!empty($zy['mt'])) { ?>
            <h4><?= trim($zy['mt']) ?></h4>
            <p><span><i></i></span></p>
            <?php if (!empty($zy['ft']) || !empty($zy['content'])) { ?>
                <div class="pubcontent">
                    <?php if (!empty($zy['ft'])) { ?>
                        <h5 style="width: 103%;margin-left: -1.5%;"><?= trim($zy['ft']) ?></h5>
                    <?php } ?>
                    <?php
                        if (!empty($zy['content'])) {
                            $sections = explode(PHP_EOL, $zy['content']);
                            foreach ($sections as $section) {
                    ?>
                                <div>
                                    <?php if ('' !== trim($section)) { ?>
                                        <span><?= trim($section) ?></span>
                                    <?php } ?>
                                </div>
                    <?php
                            }
                        }
                    ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if (!empty(trim($hkly[0]['content'])) && !empty(trim($hkly[1]['content']))) { ?>
    <div class="guarantor">
        <h4>还  款  来  源</h4>
        <p><span><i></i></span></p>
        <div class="guacontent">
            <h5>两大还款来源</h5>
            <div>
                <!--可编辑的还款框模板-->
                <dl class="clearfix editorKuang">
                    <dt class="lf">1</dt>
                    <dd class="rg">
                        <i></i>
                        <em></em>
                        <div>第一还款来源</div>
                        <p class="Ectn">
                            <?= trim($hkly[0]['content']) ?>
                        </p>
                    </dd>
                </dl>
                <dl class="clearfix editorKuang">
                    <dt class="lf">2</dt>
                    <dd class="rg">
                        <i></i>
                        <em></em>
                        <div>第二还款来源</div>
                        <p class="Ectn">
                            <?= trim($hkly[1]['content']) ?>
                        </p>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty(array_filter($zxcs))) { ?>
    <div class="publisher">
        <h4>增  信  措  施</h4>
        <p><span><i></i></span></p>
        <div class="pubcontent">
            <?php if (!empty($zxcs['ft'])) { ?>
                <h5><?= trim($zxcs['ft']) ?></h5>
            <?php } ?>
            <?php
            if (!empty($zxcs['content'])) {
                $sectionzs = explode(PHP_EOL, $zxcs['content']);
                foreach ($sectionzs as $section) {
                    ?>
                    <div>
                        <?php if ('' !== trim($section)) { ?>
                            <span><?= trim($section) ?></span>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($issuer->video) { ?>
        <div class="publisher">
            <h4>视  频  介  绍</h4>
            <p><span><i></i></span></p>
            <style>
                .publisher #video {
                     width: 10rem;
                     height: 5.6rem;
                     margin-left: -.53333333rem;
                }
            </style>
            <div class="videobox">
                <video id="video" class="media video-js vjs-default-skin" controls poster="<?= $issuer->videoImg ? UPLOAD_BASE_URI.$issuer->videoImg->uri : '' ?>">
                    <source src="<?= $issuer->video->uri ?>" type="video/mp4">
                </video>
                <div id="loading"><img src="<?= FE_BASE_URI ?>wap/introduce/img/loading.gif" alt=""></div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty(array_filter($bzcs))) { ?>
    <div class="safeguards">
        <h4>保  障  措  施</h4>
        <p><span><i></i></span></p>
        <div class="safecontent pubcontent">
            <?php if (!empty($bzcs['ft'])) { ?>
                <h5><?= trim($bzcs['ft']) ?></h5>
            <?php } ?>
            <?php
            if (!empty($bzcs['content'])) {
                $sectionbs = explode(PHP_EOL, $bzcs['content']);
                foreach ($sectionbs as $section) {
            ?>
                    <div>
                        <?php if ('' !== trim($section)) { ?>
                            <span><?= trim($section) ?></span>
                        <?php } ?>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
    <?php } ?>
</section>

<footer>
    <?php if (!empty(array_filter($cpys))) { ?>
    <table>
        <?php foreach ($cpys as $k => $list) { ?>
            <?php
                if (!empty($list['content'])) {
                    $flag = '收益分配' === $list['ft'] || '发行人' === $list['ft'];
                    $isSy = '预期年化收益率' === $list['ft'];
            ?>
                <tr>
                    <td class="lf" <?php if ($flag) { ?>style="line-height: 1rem;"<?php } ?>><?= trim($list['ft']) ?></td>
                    <td class="rg" <?php if ($flag) { ?>style="line-height: 0.6rem; padding-top: 0.2rem;padding-bottom: 0.2rem;text-align: right;width:75%;" <?php } elseif ($isSy) { ?> style="color: #0080ff;"<?php } ?>><?= trim($list['content']) ?></td>
                </tr>
            <?php } ?>
            <?php if ('1' === $syl && $isSy) { ?>
                <?php if (!empty(array_filter($jieti))) { ?>
                <tr>
                    <td class="special">
                        <ul>
                            <li style="width: 100%;">预期年化收益率</li>
                            <li class="lf"  style="width: 60%; padding-left:0.5333rem;">金额</li>
                            <li class="rg" style="padding-right:0.5333rem; color: #8c8c8c;">预期利率/年</li>
                            <?php foreach ($jieti as $val) { ?>
                                <?php
                                    if (empty($val['title'])) {
                                        continue;
                                    }
                                ?>
                                <li class="lf" style="width: 70%; padding-left:0.5333rem;"><?= trim($val['title']) ?></li>
                                <li class="rg rate"><?= trim($val['content']) ?></li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </table>
    <?php } ?>

    <?php if ($loansCount) { ?>
        <a href="/issuer/to-loan?issuerid=<?= $issuer->id ?>">立即认购</a>
    <?php } ?>
</footer>
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
