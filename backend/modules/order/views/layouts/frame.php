

<?php $this->beginContent('@app/modules/user/views/layouts/left.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>
<?php $this->beginContent('@app/views/layouts/frame.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>


