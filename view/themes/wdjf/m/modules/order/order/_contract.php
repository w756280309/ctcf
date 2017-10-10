<div class="container-text">
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= $content ?></div>
        </div>
    </div>
</div>

<?php if (isset($bq)) { ?>
    <div class="bao_quan_button">
        <?php if (!is_null($miitBQ)) : ?>
            <button class="btn btn-primary ybq" onclick="window.location = '<?= $miitBQ ?>'">工信部保权合同</button>
        <?php endif; ?>
        <?php if (isset($bq['downUrl'])) : ?>
            <button class="btn btn-primary download-ybq" <?php if (!(isset($isDisDownload) && $isDisDownload)) : ?>onclick="window.location = '<?= $bq['downUrl'] ?>'"<?php endif; ?>>下载合同</button>
        <?php endif; ?>
        <?php if (isset($bq['linkUrl'])) : ?>
            <button class="btn btn-primary ybq" onclick="window.location = '<?= $bq['linkUrl'] ?>'">保全证书</button>
        <?php endif; ?>
    </div>

    <?php if (isset($isDisDownload) && $isDisDownload) : ?>
        <!-- App端弹框提示 start  -->
        <div class="mask" id="mask-back"></div>
        <div class="bing-info" id="mask">
            <div class="bing-tishi">温馨提示</div>
            <p class="tishi-p">如需下载合同，请用浏览器登录官网下载，官网地址：www.wenjf.com</p>
            <div class="bind-btn">
                <span class="true">我知道了</span>
            </div>
        </div>
    <?php endif; ?>
<?php } ?>