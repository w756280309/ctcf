
<?php foreach ($model as $value) { ?>
    <a class="underway" href="<?= $value['link'] ?>">
        <img src="<?= $value['uri'] ?>" alt="">
        <?php if (!$value['status']) { ?>
            <div class="cover"><p>&nbsp;活动已结束&nbsp;</p></div>
        <?php } ?>
    </a>
<?php } ?>
