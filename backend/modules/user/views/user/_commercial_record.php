<?php

use common\models\user\UserFreepwdRecord;
use yii\grid\GridView;

?>

<?=
GridView::widget([
    'id' => 'grid_view_recharge',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination recharge_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'recharge_record_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '用户ID',
            'value' => function ($record) {
                return $record['uid'];
            }
        ],

        [
            'label' => '用户银行卡号',
            'value' => function ($record) {
                if ($record['card_number']) {
                    return substr_replace($record['card_number'], '**** **** **** ', 0, -4);
                } else {
                    return substr_replace($record['card_number'], '**** **** **** ', 0, -4);
                }
            }
        ],

        [
            'label' => '状态',
            'value' => function ($record) {
                switch ($record['status']) {
                    case UserFreepwdRecord::STATUS_UN_DEAL:
                        $desc = "未处理";
                        break;
                    case UserFreepwdRecord::STATUS_FAIL:
                        $desc = "快捷支付开通失败";
                        break;
                    case UserFreepwdRecord::STATUS_SUCCESS:
                        $desc = "快捷支付开通成功";
                        break;
                    case UserFreepwdRecord::SETTLE_NO_PASSWORD_FAIL:
                        $desc = "快捷免密充值开通失败";
                        break;
                    case UserFreepwdRecord::SETTLE_NO_PASSWORD_SUCCESS:
                        $desc = "快捷免密充值开通成功";
                        break;
                    default:
                        $desc = "未处理";
                }
                return $desc;
            }
        ],
        [
            'label' => '创建时间',
            'value' => function ($record){
                return date('Y-m-d h:i:s',$record['created_at']);
            }
        ],
    ],
])
?>

