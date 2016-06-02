<?php
    $this->title = '交易明细';
    use common\models\user\MoneyRecord;
    use common\models\product\OnlineProduct;
    use common\models\order\OnlineOrder;
    use common\widgets\Pager;
?>
<table>
    <tr>
        <td>类型</td>
        <td>时间</td>
        <td>变动资金（元）</td>
        <td>余额</td>
        <td>详细</td>
    </tr>
    <?php foreach($lists as $model) { ?>
        <tr>
            <td><?= Yii::$app->params['mingxi'][$model['type']] ?></td>
            <td><?= date('Y-m-d H:i:s', $model['created_at']) ?></td>
            <td class="<?= ($model['in_money'] > $model['out_money']) ? 'red' : 'green' ?>">
                <?= ($model['in_money'] > $model['out_money']) ? ('+'.number_format($model['in_money'], 2)):('-'.number_format($model['out_money'], 2)) ?>
            </td>
            <td><?= number_format($model['balance'], 2) ?></td>
            <td>
                <?php
                    if ($model['type'] === MoneyRecord::TYPE_ORDER) {
                        $ord = OnlineOrder::find()->where(['sn' => $model['osn']])->one();
                        if (null !== $ord) {
                            $pro = OnlineProduct::find()->where(['id' => $ord['online_pid']])->one();
                            if (null !== $pro) {
                                echo $pro['title'];
                            } else {
                                echo '流水号：' . $model['osn'];
                            }
                        } else {
                            echo '流水号：' . $model['osn'];
                        }
                    } else {
                        echo '流水号：' . $model['osn'];
                    }
                ?>
            </td>
        </tr>
    <?php }?>
</table>
<?= Pager::widget(['pagination' => $pages,]); ?>
