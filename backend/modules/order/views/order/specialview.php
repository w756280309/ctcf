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
        <h3>详情</h3>
    </div>
</div>
<div class="tab" id="tab">
    <a class="selected" href="/order/order/specialorder?psn=<?=$psn ?>">返回列表</a>
</div>

<div class="page_form">
        <div class="page_table form_table">
            <?php $form = ActiveForm::begin(['id' => 'order_form', 'action' => "specialview?id=".$model['id']."&psn=".$psn]); ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="100">报价时间:</th>
                    <td width="300">
                        <?= date("Y-m-d H:i",$model['order_time']);?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">投资人账号:</th>
                    <td width="300">
                        <?= $model['username'];?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">报价金额:</th>
                    <td width="300">
                        <?= $model['order_money'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">是否缴纳保证金:</th>
                    <td width="300">
                        <?= $form->field($model, 'deposit_status', ['template' => '{input}'])->dropDownList(['否','是']) ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">是否成交:</th>
                    <td width="300">
                        <?= $form->field($model, 'deal_status', ['template' => '{input}'])->dropDownList(['否','是']) ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">保证金:</th>
                    <td width="300">
                        <?= $form->field($model, 'deposit_money', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="100">合同号:</th>
                    <td width="300">
                        <?= $form->field($model, 'contract_sn', ['template' => '{input}'])->textInput() ?>
                    </td>
                    <td></td>
                </tr>
                <tr><td colspan="2"><button type="submit" class="button ajax_button">保存</button></td></tr>
            </table>
             <?php $form->end(); ?>
        </div>
</div>

<?php $this->endBlock(); ?>