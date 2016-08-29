<?php

namespace api\modules\v1\controllers\settle;

use Yii;
use api\modules\v1\controllers\Controller;

class SettleController extends Controller
{
    private $typeArr = ['01', '02', '03', '04', '05', '06'];

    /**
     * 查询某一日期下某一类型的对账数据
     * @param $type string 在typeArr取值范围内（01充值02提现06开户）
     * @param $date string 形如20160130
     *
     * @throws \Exception
     */
    public function actionShow($type, $date)
    {
        if (!in_array($type, $this->typeArr)) {
            throw new \Exception('类型不对');
        }
        $content = Yii::$container->get('ump')->getSettlement($date, $type);
        echo $content;
        echo "\n\n--------END到此为止-------------";
    }
}
