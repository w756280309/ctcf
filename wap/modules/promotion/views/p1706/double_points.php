<?php

$this->title = '金瓯永筑2积分翻倍';
$this->share = $share;
$this->headerNavOn = true;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/double-points/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <div class="pointsBanner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-points/img/banner_02.png" alt="">
    </div>
    <div class="pointsGifts">
        <div class="pointsRegular">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/double-points/img/introduce_03.png?v=170620" alt="">
        </div>
        <div class="pointsIntroduce">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/double-points/img/introduce_01.png" alt="">
        </div>
    </div>

    <a class="inviteFriends" href="">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/double-points/img/invest.png" alt="">
    </a>
</div>

<script type="text/javascript">
    $('.inviteFriends').on('click', function (e) {
        e.preventDefault();

        var url = '/issuer/to-loan?issuerid=7';

        if ('undefined' !== typeof _paq) {
            _paq.push(['trackEvent', 'loan', 'go', null, 'jinou', function () {
                location.href = url;
            }]);

            setTimeout(function() {
                location.href = url;
            }, 1500);
        } else {
            location.href = url;
        }
    });
</script>
