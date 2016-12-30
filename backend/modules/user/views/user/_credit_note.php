<?=
\yii\grid\GridView::widget([
    'id' => 'grid_view_credit_note',
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'tableOptions' => ['class' => 'credit_note_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '序号',
            'value' => function ($record) {
                return $record['id'];
            }
        ],
        [
            'label' => '项目名称',
            'value' => function ($record) use ($loans) {
                return $loans[$record['loan_id']]['title'];
            }
        ],
        [
            'label' => '转让时间',
            'value' => function ($record) {
                return $record['createTime'];
            }
        ],
        [
            'label' => '发起转让金额',
            'value' => function ($record) {
                return number_format(bcdiv($record['amount'], 100), 2);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '已转让金额',
            'value' => function ($record) {
                return number_format(bcdiv($record['tradedAmount'], 100), 2);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '状态',
            'value' => function ($record) {
                if ($record['tradedAmount'] == $record['amount']) {
                    return '已售罄';
                } elseif ($record['isClosed']) {
                    return '已结束';
                } else {
                    return '转让中';
                }
            }
        ],
    ],
])
?>
<div class="credit_note_pager pagination" style="text-align: center;">
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
</div>
<script>
    $(function(){
        $('.credit_note_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCreditNoteList($(this).attr('href'));
        });
    })
</script>
