<?php

namespace Xii\Crm\Controller;


use common\models\user\User;
//use common\models\user\UserAccount;
use common\models\user\UserInfo;
use Xii\Crm\Model\Account;
//use Xii\Crm\Model\Contact;
use yii\data\ActiveDataProvider;
//use yii\data\ArrayDataProvider;
use yii\web\Controller;

class AccountController extends Controller
{
    public function actionIndex()
    {
        $u = User::tableName();
        $ui = UserInfo::tableName();
        $a = Account::tableName();
        $query = Account::find()
            ->leftJoin($u, "$u.crmAccount_id = $a.id")
            ->leftJoin($ui, "$ui.user_id = $u.id")
            ->orderBy(["$ui.lastInvestDate" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $records = $dataProvider->getModels();
        $data = [];
        foreach ($records as $k=>$record) {
            $identity = $record->getIdentity();
            if (isset($identity->info)) {
                $data[$k]['investCount'] = $record->info->investCount;
                $data[$k]['investTotal'] = $record->info->investTotal;
                $data[$k]['annualInvestment'] = $record->annualInvestment;
            }
            if (isset($identity->lendAccount)) {
                $data[$k]['available_balance'] = $record->lendAccount->available_balance;
                $data[$k]['investmentBalance'] = $record->lendAccount->investmentBalance;
            }
        }
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}
