<?php

use common\assets\FeAsset;
use common\view\AnalyticsHelper;
use common\view\WxshareHelper;
use yii\helpers\Html;

WxshareHelper::registerTo($this, $share);
AnalyticsHelper::registerTo($this);
FeAsset::register($this);

$this->registerCssFile(FE_BASE_URI.'wap/common/css/activeComHeader.css?v=20170630', ['depends' => FeAsset::class]);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
            <meta charset="UTF-8">
            <meta name="format-detection" content="telephone=no" />
            <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no"/>
            <title>楚天财富 - 湖北日报新媒体集团旗下理财平台</title>
            <?= Html::csrfMetaTags() ?>
            <?php $this->head() ?>
            <style>
                    body {
                            margin: 0;
                            padding: 0;
                    }
                    .pic {
                            width: 100%;
                            display: block;
                    }
            </style>
    </head>
    <body>
        <?php $this->beginBody() ?>
            <?php if (!defined('IN_APP')) { ?>
                <div class="topTitle f18">
                    <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="window.location.href='/?_mark=<?= time() ?>'">
                    平台介绍
                </div>
            <?php } ?>

            <img class="pic" src="<?= ASSETS_BASE_URI ?>images/ctcf/company_desc.jpg" alt>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
