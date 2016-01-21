<?php
$this->title = '温都金服 - 首页';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/jquery.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/TouchSlide.1.1.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/jquery.classyloader.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/index.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerCssFile('/css/index.css', ['depends' => 'yii\web\YiiAsset']);
?>

<div class="slideBox" id="slideBox">
    <div class="bd">
        <ul>
             <?php foreach($adv as $val): ?>
                <li> <a class="pic" href="<?= $val['link'] ?>"><img src="/upload/adv/<?= $val['image'] ?>" alt=""></a> </li>
             <?php endforeach; ?>
        </ul>
    </div>
    <div class="hd">
        <ul></ul>
    </div>
</div>
<?php foreach($deals as $val): ?>
<a class="row column dealdata <?= in_array($val['status'], [2])?"nowing":"";?>" href="/deal/deal/detail?sn=<?= $val['num'] ?>" data-bind="<?= $val['k'] ?>" data-status="<?= $val['status'] ?>" data-per="<?= number_format($val['finish_rate']*100,0) ?>">
    <div class="hidden-xs col-sm-1"></div>
    <div class="col-xs-12 col-sm-10 column-title"><span><?= $val['title'] ?></span></div>
    <div class="<?= in_array($val['status'], [1,2,7])?"column-title-rg":"column-title-rg1";?>"><?= $val['statusval'] ?></div>
    <div class="container">
        <ul class="row column-content">
            <li class="col-xs-4">
                <div><?= doubleval($val['yr']) ?><span class="column-lu">%</span></div> <span>年化收益率</span>
            </li>
            <li class="col-xs-4">
                <div><?= $val['qixian'] ?><span class="column-lu">天</span></div> <span>期限</span>
            </li> <li class="col-xs-4 nock1">
            <div class="nock">
                <canvas data-status="<?= $val['status'] ?>" data-per="<?= (7 === (int) $val['status']) ? 100 : ($val['finish_rate']) ?>"></canvas>
                <?php if ($val['status'] == 1) { ?>
                    <div class="column-clock"><span><?= $val['start_desc'] ?></span><?= $val['start'] ?></div>
                <?php } else if ($val['status'] == 2) { ?>
                    <div class="column-clock column-clock_per"><?= number_format($val['finish_rate'] * 100, 0) ?>%</div>
                <?php } else if ($val['status'] == 7) { ?>
                    <div class="column-clock column-clock_per">成立</div>
                <?php } else { ?>
                    <div class="column-clock column-clock_per">100%</div>
                <?php } ?>
            </div>
            </li>
        </ul>
    </div>
    <div class="hidden-xs col-sm-1"></div>
</a>
<?php endforeach; ?>

