<?php
use yii\helpers\Html;

$this->title = '立合旺通充值工具';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <h3><?= Html::encode($this->title) ?></h3>

        <div class="span12">
            <table class="table">
                <tr>
                    <td>当前平台账户金额: <?= $platformBalance?> 元</td>
                </tr>
            </table>
        </div>
        <div class="span12">
            <dl style="font-size: 20px;">
                <dt>
                    立合旺通投资者账户企业网银充值(可复制链接地址进行充值)
                </dt>
                <br>
                <dd style="font-size: 18px;">
                    1. 链接地址: &nbsp;&nbsp;<?= Yii::$app->request->hostName?>/lhwt/recharge?money=充值金额(元)
                </dd>
            </dl>
        </div>
    </div>
</div>

<?php $this->endBlock()?>
