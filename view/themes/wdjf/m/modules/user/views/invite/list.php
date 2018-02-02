<?php
use common\utils\StringUtils;
?>

<?php if (!empty($data)) : ?>
    <?php foreach ($data as $key => $val) : ?>
        <tr>
            <td><?= empty($val['name']) ? '---' : StringUtils::obfsName($val['name']) ?></td>
            <td><?= $val['day'] ?></td>
            <td><?= $val['coupon'] ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>