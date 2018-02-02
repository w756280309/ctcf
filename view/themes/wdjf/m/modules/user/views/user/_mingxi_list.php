<?php
use common\models\user\MoneyRecord;
?>

<?php foreach ($model as $val) : $isIn = $val['in_money'] >= $val['out_money'] && !in_array($val['type'], [MoneyRecord::TYPE_CREDIT_NOTE_FEE, MoneyRecord::TYPE_DRAW_FEE, MoneyRecord::TYPE_DRAW_FEE_RETURN, MoneyRecord::TYPE_FEE]); ?>
    <div class="clear"></div>
    <div  class="row jiaoyi">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <div class="col-xs-6 lf"><p  class="way"><?= Yii::$app->params['mingxi'][$val['type']] ?></p><p class="revenue">余额：<span><?= number_format($val['balance'], 2) ?>元</span></p></div>
            <div class="col-xs-6 rg"><p  class="money <?= $isIn ? 'red' : 'green' ?>" ><?= $isIn ? ('+'.number_format($val['in_money'], 2)):('-'.number_format($val['out_money'], 2)) ?></p><p  class="date" ><span class="data1"><?= date('Y-m-d', $val['created_at']) ?></span>  <span class="data2"><?= date('H:i:s', $val['created_at']) ?></span></p></div>
        </div>
        <div class="col-xs-1"></div>
    </div>
<?php endforeach; ?>