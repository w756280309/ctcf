<?php
$this->title = '债券转让';

use common\utils\StringUtils;
use common\widgets\Pager;

?>

<?php foreach ($model as $key => $val) : ?>

----><br>

标的名称: <?= $val->loan->title ?> <br>
还款方式: <?= Yii::$app->params['refund_method'][$val->loan->refund_method] ?> <br>
年化率: <?= $val->order->yield_rate * 100 ?>% <br>
剩余期限: <?= $data[$key]['surplusExpire'] ?> <br>
转让总额: <?= StringUtils::amountFormat1('{amount}{unit}', $val->amount) ?> <br>
进度: <?= $val->status === 1 ? round($val->tradedAmount / $val->amount * 100) : 100 ?>% <br>
截止时间: <?= $data[$key]['surplusTime'] ?> <br>
转让状态: <?= $val->status === 1 ? '转让中' : '已转让' ?> <br>

<----<br><br>

<?php endforeach; ?>

<center><?= Pager::widget(['pagination' => $pages]); ?></center>