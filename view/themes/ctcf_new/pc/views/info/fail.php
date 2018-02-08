<?php

$this->title = $info['title'];
use yii\helpers\Html;

?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/info/fail.css?v=1.1">

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-false">
            <div class="invest-content">
                <p class="buy-txt"><i></i><span><?= $info['mainTitle'] ?></span></p>
                <p class="buy-txt-tip"><?= $info['firstFuTitle'] ?></p>
                <?php if ($info['linkType'] == 1) { ?>
                <a href="<?= Html::encode($info['jumpUrl']) ?>" class="button-close"><?= $info['jumpReferWords'] ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<?php if ($info['requireJump'] && $info['linkType'] == 2) { ?>
<script>
    $(function(){
        $('.a-close').on('click',function(){
            window.close();
        });

        var em_time=5;
        var time;
        time=setInterval(function(){
            em_time--;
            $('.em_time').html(em_time);

            if(em_time==0){
                clearInterval(time);
                window.close();
            }
        },1000)

    });
</script>
<?php } ?>