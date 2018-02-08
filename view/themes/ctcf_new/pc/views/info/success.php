<?php
$this->title = $info['title'];
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/chargedeposit.css?v=1.1');

use yii\helpers\Html;
$jumpUrl = Html::encode($info['jumpUrl']);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/info/success.css?v=1.3">

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content">
                <p class="buy-txt"><i></i><span><?= $info['mainTitle'] ?></span></p>
                <?php if ($source == 'tixian') { ?>
                    <div style="text-align: center">工作日下午五点前提现当天到账</div>
                    <div style="text-align: center">工作日下午五点后提现下一个工作日到账</div>
                    <div style="color:#ff6707; text-align: center">如遇节假日，到账时间将顺延到下个工作日哦！</div>
                <?php } ?>
                <p class="buy-txt-tip"><?= $info['firstFuTitle'] ?></p>
                <?php if ($info['linkType'] === 1 || $info['linkType'] === 3) { ?>
                    <a href="<?= $info['linkType'] === 1 && $jumpUrl ? $jumpUrl : 'javascript:void(0)' ?>" class="button-close"><?= $info['jumpReferWords'] ?></a>
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

<?php if ($info['requireJump'] && $info['linkType'] === 3) { ?>
    <script>
        $(function() {
            $('.button-close').on('click', function() {
                mianmi();
            });
        });
    </script>
<?php } ?>
