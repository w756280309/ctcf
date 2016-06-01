<?php
$this->title = Yii::$app->params['pc_page_title'];

use common\models\product\OnlineProduct;
?>

轮播图:<br>
<?php foreach ($adv as $val) : ?>
    <a href="<?= $val['link'] ?>"><img src="/upload/adv/<?= $val['image'] ?>" alt="<?= $val['title'] ?>"></a><br>
<?php endforeach; ?>

<br>理财公告:<br>
<?php foreach ($notice as $val) : ?>
    <?= $val['title'] ?><br>
<?php endforeach; ?>

<br>媒体报道:<br>
<?php foreach ($media as $val) : ?>
    <?= $val['title'] ?><br>
<?php endforeach; ?>

<br>推荐标的:<br>
<?php foreach ($loans as $val) : ?>
    ***************<br>
    项目名称:<?= $val['title'] ?><br>
    计息方式:<?= Yii::$app->params['refund_method'][$val['refund_method']] ?><br>
    融资金额:<?= rtrim(rtrim(number_format($val['money'], 2), '0'), '.') ?><br>
    可投金额:<?= rtrim(rtrim(number_format($val['money'] - $val['funded_money'], 2), '0'), '.') ?><br>
    进度:<?= number_format($val['finish_rate'] * 100) ?><br>
    年化收益率:<?= rtrim(rtrim(number_format(OnlineProduct::calcBaseRate($val['yield_rate'], $val['jiaxi']), 2), '0'), '.') ?><br>
    项目期限:<?= $val['expires'] ?><br>
    项目状态:<?= Yii::$app->params['deal_status'][$val['online_status']] ?><br>
    ***************<br>
<?php endforeach; ?>

<br>最新资讯:<br>
<?php foreach ($news as $val) : ?>
    <?= $val['title'] ?><br>
<?php endforeach; ?>

<br>