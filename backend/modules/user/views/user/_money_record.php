<?php
    use common\models\user\MoneyRecord;
?>

<?=
\yii\grid\GridView::widget([
    'id' => 'grid_view_money_record',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination money_record_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'money_record_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '交易类型',
            'value' => function ($record) use ($recordTypes) {
                return isset($recordTypes[$record->type]) ? $recordTypes[$record->type] : '---';
            }
        ],
        [
            'label' => '流水号',
            'value' => function ($record) {
                return $record->sn;
            }
        ],
        [
            'label' => '金额',
            'value' => function ($record) {
                return number_format(max($record->in_money, $record->out_money), 2);
            }
        ],
        [
            'label' => '余额',
            'value' => function ($record) {
                return number_format($record->balance, 2);
            }
        ],
        [
            'label' => '项目名称',
            'value' => function ($record) use ($data) {
                if (in_array($record->type ,[MoneyRecord::TYPE_ORDER, MoneyRecord::TYPE_HUIKUAN, MoneyRecord::TYPE_LOAN_CANCEL])) {
                    if (isset($data[$record->osn])) {
                        return $data[$record->osn]['title'];
                    }
                } elseif (in_array($record->type, [MoneyRecord::TYPE_CREDIT_NOTE, MoneyRecord::TYPE_CREDIT_NOTE_FEE, MoneyRecord::TYPE_CREDIT_REPAID])) {
                    $creditOrder = Yii::$container->get('txClient')->get('credit-order/detail', [
                        'id' => $record->osn,
                    ]);
                    $creditNode = Yii::$container->get('txClient')->get('credit-note/detail', [
                        'id' => $creditOrder['note_id'],
                    ]);
                    $loan = \common\models\product\OnlineProduct::find()->select('title')->where(['id' => $creditNode['loan_id']])->asArray()->one();
                    return $loan['title'];
                }
                return '';
            }
        ],
        [
            'label' => '交易时间',
            'value' => function ($record) {
                return date('Y-m-d H:i:s', $record->created_at);
            }
        ],
    ],
])
?>

<script>
    $(function(){
        $('.money_record_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getMoneyRecord($(this).attr('href'));
        })
    })
</script>

