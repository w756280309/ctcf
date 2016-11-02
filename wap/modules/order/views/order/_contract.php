<div class="container-text">
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= $content ?></div>
        </div>
    </div>
</div>

<?php if (isset($bq)) { ?>
    <div class="bao_quan_button">
        <?php if (isset($bq['downUrl'])) : ?>
            <button class="btn btn-primary" onclick="window.location = '<?= $bq['downUrl'] ?>'" style="background:#f54337;border: 0px;">下载合同</button>
        <?php endif; ?>
        <?php if (isset($bq['linkUrl'])) : ?>
            <button class="btn btn-primary" onclick="window.location = '<?= $bq['linkUrl'] ?>'" style="background:#f54337;border: 0px;margin-top: 5px;">保全证书</button>
        <?php endif; ?>
    </div>
<?php } ?>