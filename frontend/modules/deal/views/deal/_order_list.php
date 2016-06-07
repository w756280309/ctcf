<div>
    <div>
        <table>
            <?php foreach($data as $v) { ?>
                <tr>
                    <td><?= \common\utils\StringUtils::obfsMobileNumber($v->mobile) ?></td>
                    <td><?= date('Y-m-d', $v->order_time) . ' ' . date('H:i:s', $v->order_time) ?></td>
                    <td><?= rtrim(rtrim(number_format($v->order_money, 2), '0'), '.') ?></td>
                </tr>
            <?php }?>
        </table>
    </div>
    <div>
        <?= \common\widgets\Pager::widget(['pagination' => $pages])?>
    </div>
</div>
