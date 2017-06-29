<?php
use yii\helpers\Html;

$this->title = '后台工具';
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

        <p>后台工具列表</p>

        <div class="span12">
            <dl>
                <dt>
                    平台现金红包充值
                </dt>
                <dd>
                    1. 链接地址: <?= Yii::$app->request->hostName?>/tool/recharge?money=充值金额(元)
                </dd>
            </dl>
        </div>
    </div>
</div>

<?php $this->endBlock()?>
