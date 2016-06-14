<div class="piD-data-box">
    <?php if(count($data)>0){?>
        <table class="piD-data-inner">
            <tbody>
            <tr style="background: rgb(239, 239, 243);">
                <th class="piD-th1 piD-left"><span>序号</span></th>
                <th class="piD-th2 piD-left"><span>投资人</span></th>
                <th class="piD-th3 piD-left"><span>投资时间</span></th>
                <th class="piD-th4 piD-right"><span>投资金额(元)</span></th>
                <th class="piD-th5 piD-left"><span>状态</span></th>
            </tr>
            <?php foreach($data as $k=>$v) { ?>
                <tr style="<?= ($k%2 != 0) ? "background: rgb(239, 239, 243);":"" ?>">
                    <td class="piD-th1 piD-left"><span><?= $pages->totalCount - $pages->offset - $k ?></span></td>
                    <td class="piD-th1 piD-left"><span><?= \common\utils\StringUtils::obfsMobileNumber($v->mobile) ?></span></td>
                    <td class="piD-th1 piD-left" style="width: 200px;"><span><?= date('Y-m-d', $v->order_time) . ' ' . date('H:i:s', $v->order_time) ?></span></td>
                    <td class="piD-th4 piD-right"><span><?= rtrim(rtrim(number_format($v->order_money, 2), '0'), '.') ?></span></td>
                    <td class="piD-th1 piD-left"><span><img src="/images/deal/plD-right.png" alt=""></span></td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    <?php }else{?>
        <!--无数据是显示-->
        <p class="not_yet">暂无投资记录</p>
    <?php }?>
    <!--分页-->
    <?= \common\widgets\Pager::widget(['pagination' => $pages])?>
</div>
