<?php
    use common\models\user\MoneyRecord;
?>
<div class="portlet-body">

    <table class="table">
        <tr>
            <td>
                <span class="title">状态</span>
                <select name="status" id="money_record_form_type" m-wrap span6>
                    <option value="">---全部---</option>
                    <option value="0" <?= ($status === '0') ? "selected='selected'" : "" ?> >充值</option>
                    <option value="1" <?= ($status === '1') ? "selected='selected'" : "" ?> >提现申请</option>
                    <option value="100" <?= ($status === '100') ? "selected='selected'" : "" ?> >提现申请失败</option>
                    <option value="101" <?= ($status === '101') ? "selected='selected'" : "" ?> >提现成功</option>
                    <option value="102" <?= ($status === '102') ? "selected='selected'" : "" ?> >提现失败</option>
                    <option value="4" <?= ($status === '4') ? "selected='selected'" : "" ?> >回款</option>
                    <option value="2" <?= ($status === '2') ? "selected='selected'" : "" ?> >投资</option>
                </select>
            </td>
            <td>
                <div align="right" class="search-btn">
                    <button class="btn blue btn-block loan_order_search" style="width: 100px;">搜索 <i
                                class="m-icon-swapright m-icon-white"></i></button>
                </div>
            </td>
        </tr>
    </table>
</div>
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
            'format' => 'html',
            'value' => function ($record) {
                if ($record->in_money > $record->out_money) {
                    return '<span class="red">+'.number_format($record->in_money, 2).'</span>';
                } else {
                    return '<span class="green">-'.number_format($record->out_money, 2).'</span>';
                }
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '余额',
            'value' => function ($record) {
                return number_format($record->balance, 2);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
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
                } elseif ($record->type === MoneyRecord::TYPE_CREDIT_HUIKUAN) {
                    $userAsset = Yii::$container->get('txClient')->get('assets/detail', [
                        'id' => $record->osn,
                    ]);
                    if ($userAsset && $userAsset['loan_id']) {
                        $loan = \common\models\product\OnlineProduct::find()->select('title')->where(['id' => $userAsset['loan_id']])->asArray()->one();
                        return $loan['title'];
                    }
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
        });
        $('.loan_order_search').on('click', function(){
            var status = $('#money_record_form_type').val();
            getMoneyRecord('/user/user/detail?id=<?= $normalUser->id?>&key=money_record&status='+status);
        });
    })

</script>


