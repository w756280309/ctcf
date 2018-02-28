<?php

namespace console\modules\ctcf\controllers;

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\console\Controller;
use Yii;

class ProductController extends Controller
{
    /**
     * 标的的详情生成
     */
    public function actionDescription()
    {
        $onlineProducts = OnlineProduct::find()->all();
        foreach ($onlineProducts as $onlineProduct) {
            if (false !== strpos($onlineProduct->sn, 'CTCF-LEGACY')) {
                $description = '';
                $description .= '项目名称：' . $onlineProduct->title . '<br><br>';
                $description .= '项目金额：' . StringUtils::amountFormat1('{amount}{unit}', $onlineProduct->money) . '<br><br>';
                $description .= '预期年收益：' . LoanHelper::getDealRate($onlineProduct) . '%<br><br>';
                $ex = $onlineProduct->getDuration();
                $description .= '投资期限：' . $ex['value'] . $ex['unit'] . '<br><br>';
                $description .= '起投金额：' . StringUtils::amountFormat2($onlineProduct->start_money) . '元<br><br>';
                $description .= '募集开始时间：' . date('Y.m.d', $onlineProduct->start_date) . '<br><br>';
                $description .= '募集结束时间：' . date('Y.m.d', $onlineProduct->end_date) . '<br><br>';
                $description .= '收益起算日：' . date('Y.m.d', $onlineProduct->jixi_time) . '<br><br>';
                $description .= '收益到期日：' . date('Y.m.d', $onlineProduct->finish_date) . '<br><br>';
                $description .= '收益说明：本产品募集期内(' . date('Y.m.d', $onlineProduct->start_date) . '-' . date('Y.m.d', $onlineProduct->end_date) . ')不计算收益，自( ' . date('Y.m.d', $onlineProduct->jixi_time) . ' )起计算收益，产品到期日（' . date('Y.m.d', $onlineProduct->finish_date) . '）不计算收益。' . '<br><br>';
                $description .= '还款方式：' . Yii::$app->params['refund_method'][$onlineProduct->refund_method] . '<br><br>';
                $onlineProduct->description = $description;
                $onlineProduct->scenario = 'create';
                $onlineProduct->save(false);
            }
        }
    }

    /**
     * 历史标的隐藏
     */
    public function actionHidden()
    {
        OnlineProduct::updateAll(
            ['isPrivate' => 1],
            ['sn' =>
                [
                    'CTCF-LEGACY-1',
                    'CTCF-LEGACY-2',
                    'CTCF-LEGACY-5',
                    'CTCF-LEGACY-6',
                    'CTCF-LEGACY-7',
                    'CTCF-LEGACY-9',
                    'CTCF-LEGACY-10',
                    'CTCF-LEGACY-11',
                    'CTCF-LEGACY-594',
                    'CTCF-LEGACY-596',
                    'CTCF-LEGACY-597',
                    'CTCF-LEGACY-598',
                    'CTCF-LEGACY-599',
                    'CTCF-LEGACY-601',
                    'CTCF-LEGACY-602',
                    'CTCF-LEGACY-603',
                    'CTCF-LEGACY-696',
                    'CTCF-LEGACY-697',
                    'CTCF-LEGACY-702',
                    'CTCF-LEGACY-703',
                    'CTCF-LEGACY-717'
                ]
            ]
        );
    }
}
