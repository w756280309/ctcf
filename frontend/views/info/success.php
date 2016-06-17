<?php

$this->title = $info['title'];

?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/info/success.css">

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content">
                <p class="buy-txt"><i></i><span><?= $info['mainTitle'] ?></span></p>
                <p class="buy-txt-tip"><?= $info['firstFuTitle'] ?></p>
                <?php if ($info['linkType'] == 1) { ?>
                    <a href="<?= $info['jumpUrl'] ?>" class="button-close"><?= $info['jumpReferWords'] ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

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

