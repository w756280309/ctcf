<?php

$this->title = '积分商城介绍';
?>
<link rel="stylesheet" href="/ctcf/css/t1706/base.css">
<link rel="stylesheet" href="/ctcf/css/t1706/index.min.css">
<script src="/js/lib.flexible3.js"></script>

<div class="flex-content">
    <div class="top-part"></div>
    <div class="middle-part">
        <a href="/deal/deal/index"></a>
    </div>
    <div class="bottom-part">
        <a href="/site/app-download?redirect=/mall/portal/guest<?= (Yii::$app->request->get('token') && defined('IN_APP')) ? '?token=' . Yii::$app->request->get('token') : '' ?>"></a>
    </div>
</div>