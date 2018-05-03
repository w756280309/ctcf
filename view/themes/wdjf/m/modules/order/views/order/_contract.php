<div class="container-text">
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= $content ?></div>
        </div>
    </div>
</div>

<?php if (isset($bq)) { ?>
    <div class="bao_quan_button">
        <?php if (isset($miitBQ) && $miitBQ) : ?>
            <button class="btn btn-primary" style="margin-bottom: 5%" onclick="window.location = '<?= $miitBQ ?>'">国家电子合同</button>
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