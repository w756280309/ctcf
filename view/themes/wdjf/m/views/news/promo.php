<?php

use yii\web\View;

$this->title = '活动专区';

$this->registerCssFile(FE_BASE_URI . 'wap/common/css/wenjfbase.css', ['position' => View::POS_HEAD]);
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['position' => View::POS_HEAD]);
$this->registerJsFile(FE_BASE_URI . 'libs/vue.min.js', ['position' => View::POS_HEAD]);

?>
<script type="text/javascript">
    var url = '/news/promo';
    var tp = '<?= $header['tp'] ?>';
</script>
<style>
    body{background-color:#fff}.flex-content{padding:.4rem .53333333rem}.underway{display:block;position:relative;margin:.4rem auto;width:8.93333333rem;height:4.16rem;-webkit-box-shadow:0 4px 12px rgba(0,0,0,.4);-moz-box-shadow:0 4px 12px rgba(0,0,0,.4);box-shadow:0 4px 12px rgba(0,0,0,.4)}.underway img{width:100%;height:100%;display:block}.underway:first-child{margin-top:0}.cover{width:100%;height:100%;background-color:rgba(0,0,0,.5);position:absolute;top:0;left:0;color:#fff;font-size:.42666667rem}.cover p{text-align:center;line-height:4.16rem}.cover p::after,.cover p::before{content:'';display:inline-block;height:.02666667rem;background-color:#fff;width:.8rem;margin-bottom:.13333333rem}
</style>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>
<!-- 主体 -->
<?php if($model) { ?>
    <?= $this->renderFile('@wap/views/news/_promo_list.php', ['model' => $model]) ?>
    <div class="load"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>
