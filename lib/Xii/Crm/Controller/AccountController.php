<?php

namespace Xii\Crm\Controller;

use common\models\user\User;
use common\models\user\UserInfo;
use Xii\Crm\Model\Account;
use Xii\Crm\Model\Contact;
use Xii\Crm\Model\Identity;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class AccountController extends Controller
{
    public function actionIndex($isConverted = null)
    {
        $u = User::tableName();
        $ui = UserInfo::tableName();
        $a = Account::tableName();
        $query = Account::find()
            ->leftJoin($u, "$u.crmAccount_id = $a.id")
            ->leftJoin($ui, "$ui.user_id = $u.id");

        if (null !== $isConverted) {
            $isConverted = $isConverted === 'false' ? false : (bool) $isConverted;
            $query->where(["$a.isConverted" => $isConverted]);
        }
        $query->orderBy(["$ui.lastInvestDate" => SORT_DESC, "$a.id" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $records = $dataProvider->getModels();
        $data = [];
        foreach ($records as $record) {
            $identity = $record->getIdentity();
            if ($identity instanceof User || $identity instanceof Identity) {
                $data[$record->id]['gender'] = $identity->getCrmGender();
                $data[$record->id]['name'] = $identity->getCrmName();
                $data[$record->id]['age'] = $identity->getCrmAge();
            }
            if ($identity instanceof User) {
                $data[$record->id]['mobile'] = '*' . substr($identity->mobile, -4);
                if (isset($identity->info)) {
                    $data[$record->id]['investCount'] = $identity->info->investCount;
                    $data[$record->id]['investTotal'] = $identity->info->investTotal;
                }
                if (isset($identity->lendAccount)) {
                    $data[$record->id]['availableBalance'] = $identity->lendAccount->available_balance;
                    $data[$record->id]['investmentBalance'] = $identity->lendAccount->investment_balance;
                }
            } elseif($identity instanceof Identity) {
                $contactMobile = $identity->fetchContact(Contact::TYPE_MOBILE)->one();
                $contactLandline = $identity->fetchContact(Contact::TYPE_LANDLINE)->one();
                $data[$record->id]['mobile'] = isset($contactMobile->obfsNumber) ? $contactMobile->obfsNumber : null;
                $data[$record->id]['landline'] = isset($contactLandline->obfsNumber) ? $contactMobile->obfsNumber : null;
            } else {
                $data[$record->id] = [];
            }
        }
        return $this->render('index', ['dataProvider' => $dataProvider, 'data' => $data]);
    }
}
