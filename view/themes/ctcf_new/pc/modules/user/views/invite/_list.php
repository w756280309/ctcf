<?php
use common\utils\StringUtils;
use common\widgets\Pager;
?>

<table>
    <tr>
        <th width="135">姓名</th>
        <th width="135">手机号</th>
        <th width="135">注册日期</th>
        <th width="135">代金券(元)</th>
    </tr>
    <?php if (!empty($data)) : $count = 0; ?>
        <?php foreach ($data as $val) : ++$count; ?>
            <tr class="<?= $count % 2 === 0 ? 'td-back-color' : '' ?>">
                <td><?= empty($val['name']) ? '---' : StringUtils::obfsName($val['name']) ?></td>
                <td><?= StringUtils::obfsMobileNumber($val['mobile']) ?></td>
                <td><?= $val['day'] ?></td>
                <td><?= $val['coupon'] ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<?php if (empty($model)) : ?>
    <div class="without-font">暂未获得邀请奖励，快前去邀请吧</div>
<?php endif; ?>

<center><?= Pager::widget(['pagination' => $pages]); ?></center>