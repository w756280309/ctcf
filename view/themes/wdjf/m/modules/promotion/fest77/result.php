<?php
use common\models\adv\Share;

$this->title = Yii::$app->session->get('resourceOwnerNickName').'的月老签';
$this->share = new Share([
    'title' => '我抽取了一根月老签，你也来和我一起测缘分，好不好？',
    'description' => '在温都金服月老祠，抽中专属于你的那枚签',
    'imgUrl' => FE_BASE_URI.'wap/campaigns/active20170821/images/wx_share.jpg',
    'url' => Yii::$app->request->hostInfo.'/promotion/fest77/',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170821/css/result.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<div class="flex-content" style="display: none">
    <div class="part-one"></div>
    <div class="part-two">
        <!--此处放置无二维码的图片-->
        <img src="/promotion/fest77/no-code?xcode=<?= $xcode ?>" alt="">
        <!--此处放置有二维码的图片-->
        <img src="/promotion/fest77/with-code?xcode=<?= $xcode ?>" alt="" class="xia">
    </div>
    <div class="part-three">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/pic_couple.png" alt="">
    </div>
</div>
<a id="anchor" href="#Myanchor"></a>
<div id="Myanchor"></div>
<script>
    $(function () {
        $(".flex-content").fadeIn(3000);
        $('#anchor')[0].click();
    })
</script>