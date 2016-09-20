<?php
use common\widgets\Pager;
use common\utils\StringUtils;
?>

<div class="piD-data-box">
    <table class="piD-data-inner" id="note-order">
        <tr>
            <th class="piD-th1 piD-center"><span>序号</span></th>
            <th class="piD-th2 piD-left"><span>受让人</span></th>
            <th class="piD-th3 piD-left"><span>投资时间</span></th>
            <th class="piD-th4 piD-right"><span>投资金额(元)</span></th>
            <th class="piD-th5 piD-center"><span>状态</span></th>
        </tr>
        <?php foreach ($data as $key => $val) { ?>
            <tr>
                <td class="piD-th1 piD-center"><span><?= $pages->totalCount - $pages->offset - $key ?></span></td>
                <td class="piD-th1 piD-left"><span><?= StringUtils::obfsMobileNumber($users[$val['user_id']]['mobile']) ?></span></td>
                <td class="piD-th3 piD-left"><span><?= $val['createTime'] ?></span></td>
                <td class="piD-th4 piD-right"><span><?= StringUtils::amountFormat3(bcdiv($val['amount'], 100, 2)) ?></span></td>
                <td class="piD-th1 piD-center"><span><img src="<?= ASSETS_BASE_URI ?>images/deal/plD-right.png" alt=""></span></td>
            </tr>
        <?php } ?>
    </table>
    <?php if (!empty($data)) { ?>
        <center><?= Pager::widget(['pagination' => $pages])?></center>
    <?php } else { ?>
        <p class="not_yet show">暂无转让记录</p>
    <?php } ?>
</div>

<script type="text/javascript">
    $(function() {
        $('#note-order table tr:first').attr('background', 'rgb(239, 239, 243)');
        for (var i = 0; i <= $('.piD-data-inner tr').length; i++) {
            if (i % 2 === 0) {
                $('.piD-data-inner tr').eq(i).css({background: '#efeff3'});
            }
        }
    });
</script>