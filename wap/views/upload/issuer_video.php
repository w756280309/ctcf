<?php

$this->title = '项目视频介绍';

use wap\assets\WapAsset;
use yii\web\JqueryAsset;

$this->registerCssFile(FE_BASE_URI.'wap/video-list/css/zshy.css', ['depends' => WapAsset::class, 'position' => 1]);
$this->registerCssFile(FE_BASE_URI.'libs/videojs/video-js.min.css', ['position' => 1]);
$this->registerJsFile(FE_BASE_URI.'libs/lib.flexible2.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI.'libs/fastclick.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI.'libs/videojs/video.min.js', ['position' => 1]);
?>

<style>
    .video0-dimensions {
        width: 100%;
    }
    .video-js {
        width: 100%;
        height: 210px;
    }
    .video-js .vjs-big-play-button {
        line-height: 2em;
        height: 2em;
        width: 2em;
        top: 36%;
        left: 43%;
        border-radius: 2rem;
    }
</style>

<p class="wifi-tip">以下视频建议在WiFi环境下播放<span></span></p>

<?php $count = count($issuers); ?>
<?php foreach ($issuers as $key => $issuer) : ?>
    <h3 class="video-title"><?= $issuer->mediaTitle ?></h3>
    <div class="video-items">
        <video id="video<?= $key ?>" key="<?= $key ?>" class="media video-js vjs-default-skin" controls  preload="none"
               poster="<?= $issuer->videoImg ? UPLOAD_BASE_URI.$issuer->videoImg->uri : '' ?>" data-setup='{}'>
            <source src="<?= $issuer->video->uri ?>" type="video/mp4">
            您的浏览器不支持此种视频格式。
        </video>
    </div>
    <?php if ($key !== $count - 1) { ?>
        <p class="video-bottom-grey"></p>
    <?php } ?>
<?php endforeach; ?>

<script>
    $(function () {
        FastClick.attach(document.body);
        //wifi提示
        $('.wifi-tip span').on('click', function () {
            $(this).parent().hide();
        });
    });

    window.onload = function () {
        var medias = document.querySelectorAll(".media");
        var mediaLength = medias.length;
        var loading = document.querySelectorAll('.loading');

        for(var i=0; i< mediaLength; i++) {
            document.getElementById("video"+i).onclick = function(){
                var _this = this;
                var k = _this.getAttribute('key');

                for (var j = 0; j < mediaLength; j++) {
                    if (j == k) {
                        continue;
                    }
                    medias[j].pause();
                    loading[j].style.display = 'none';
                }

                if (this.paused) {
                    this.play();
                } else {
                    this.pause();
                }
            }
        }
    }
</script>