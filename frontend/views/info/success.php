<?php

$this->title = $info['title'];
use yii\helpers\Html;

$jumpUrl = Html::encode($info['jumpUrl']);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/info/success.css">

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content">
                <p class="buy-txt"><i></i><span><?= $info['mainTitle'] ?></span></p>
                <p class="buy-txt-tip"><?= $info['firstFuTitle'] ?></p>
                <?php if ($info['linkType'] == 1) { ?>
                    <a href="<?= $jumpUrl ?>" class="button-close"><?= $info['jumpReferWords'] ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php if ($info['requireJump'] && $info['linkType'] == 2) { ?>
<script>
    $(function(){
        var url = '<?= $jumpUrl ?>';
        var close = <?= intval(in_array($source,['bangka', 'huanka', 'chongzhi', 'tixian']))?>;
        $('.a-close').on('click',function(){
            if (close ==  1){
                window.close();
            } else {
                location.href = url;
            }
        });

        var em_time=5;
        var time;
        time=setInterval(function(){
            em_time--;
            $('.em_time').html(em_time);

            if(em_time==0){
                clearInterval(time);
                if (close ==  1){
                    window.close();
                } else {
                    location.href = url;
                }
            }
        },1000);
    });
</script>
<?php } ?>

