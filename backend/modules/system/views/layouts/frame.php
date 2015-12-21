<?php $this->beginContent('@app/modules/system/views/layouts/left.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>
<?php $this->beginContent('@app/views/layouts/frame.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>