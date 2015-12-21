<?php

/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-18
 * Time: 下午7:46
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
  // * @var $model common\models\news\Category
 * @var yii\web\View $this
 * @var $categories

  $this->registerJsFile('/admin/js/validate.js', ['depends' => 'yii\web\YiiAsset']);
 *  */
$uid = Yii::$app->request->get('uid');
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="page_function">
    <div class="info">
        <h3>投资详情</h3>
    </div>
</div>
<div class="tab" id="tab">
    <a class="selected" href="/order/order/index?user_id=<?=$uid ?>">返回列表</a>
</div>

<div class="page_form">
        <div class="page_table form_table">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="100">项目编号:</th>
                    <td width="300">
                        <?= $model['product_sn'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">项目名称:</th>
                    <td width="300">
                        <?= $model['product_title'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">合同编号:</th>
                    <td width="300">
                        <?= $model['contract_sn'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">年化收益:</th>
                    <td width="300">
                        <?= $model['yield_rate'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">项目期限:</th>
                    <td width="300">
                        <?= $model['product_duration'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">认购时间:</th>
                    <td width="300">
                        <?= date("Y-m-d H:i",$model['order_time']) ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">到期兑付日:</th>
                    <td width="300">
                        <?= date("Y-m-d H:i",$model['pay_time']) ?>
                    </td>
                    <td></td>
                </tr>
                
                <tr>
                    <th width="100">认购金额:</th>
                    <td width="300">
                        <?= $model['order_money'] ?>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
</div>

<?php $this->endBlock(); ?>