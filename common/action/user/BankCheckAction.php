<?php
/**
 * Created by ShiYang.
 * Date: 17-1-9
 * Time: 上午9:58
 */

namespace common\action\user;


use common\service\BankService;
use yii\base\Action;

//根据卡号匹配开户行
class BankCheckAction extends Action
{
    public function run()
    {
        return BankService::checkBankcard(\Yii::$app->request->post('card'));
    }
}