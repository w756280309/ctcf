<?php

namespace Xii\Crm\Controller;

use common\models\user\User;
use common\models\user\UserInfo;
use common\utils\SecurityUtils;
use Xii\Crm\Model\Account;
use Xii\Crm\Model\Contact;
use Xii\Crm\Model\Identity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class AccountController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request->get();
        $mobile = !empty($request['mobile']) ? trim($request['mobile']) : '';
        $landline = !empty($request['landline']) ? trim($request['landline']) : '';
        $isConverted = isset($request['isConverted']) ? $request['isConverted'] : '';

        $u = User::tableName();
        $ui = UserInfo::tableName();
        $a = Account::tableName();
        $c = Contact::tableName();
        $leftJoinContact = false;
        $query = Account::find()
            ->leftJoin($u, "$u.crmAccount_id = $a.id")
            ->leftJoin($ui, "$ui.user_id = $u.id");

        if ('' !== $mobile) {
            $query->leftJoin($c, "$c.account_id = $a.id");
            $query->andWhere([
                "$c.encryptedNumber" => SecurityUtils::encrypt($mobile),
                "$c.type" => Contact::TYPE_MOBILE,
            ]);
            $leftJoinContact = true;
        }

        if ('' !== $landline) {
            if ('0' !== substr($landline, 0, 1)) {
                $landline = '0577-' . $landline;
            }

            if ($leftJoinContact) {
                $query->orWhere([
                    "$c.encryptedNumber" => SecurityUtils::encrypt($landline),
                    "$c.type" => Contact::TYPE_LANDLINE,
                ]);
            } else {
                $query->leftJoin($c, "$c.account_id = $a.id");
                $query->andWhere([
                    "$c.encryptedNumber" => SecurityUtils::encrypt($landline),
                    "$c.type" => Contact::TYPE_LANDLINE,
                ]);
            }
        }

        if ('' !== $isConverted) {
            $isConverted = $isConverted === 'false' ? false : (bool) $request['isConverted'];
            $query->andWhere(["$a.isConverted" => $isConverted]);
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
            $contactMobile = Contact::findContactByAccountId($record->id)->andWhere(['type' => Contact::TYPE_MOBILE])->one();
            $contactLandline = Contact::findContactByAccountId($record->id)->andWhere(['type' => Contact::TYPE_LANDLINE])->one();
            $data[$record->id]['mobile'] = isset($contactMobile) ? $contactMobile->obfsNumber : null;
            $data[$record->id]['landline'] = isset($contactLandline) ? $contactLandline->obfsNumber : null;
            if ($identity instanceof User || $identity instanceof Identity) {
                $data[$record->id]['gender'] = $identity->getCrmGender();
                $data[$record->id]['name'] = $identity->getCrmName();
                $data[$record->id]['age'] = $identity->getCrmAge();
            }
            if ($identity instanceof User) {
                $data[$record->id]['mobile'] = '*' . substr($identity->mobile, -4);
                $data[$record->id]['annualInvestment'] = $identity->annualInvestment;
                if (isset($identity->info)) {
                    $data[$record->id]['investCount'] = $identity->info->investCount;
                    $data[$record->id]['investTotal'] = $identity->info->investTotal;
                }
                if (isset($identity->lendAccount)) {
                    $data[$record->id]['availableBalance'] = $identity->lendAccount->available_balance;
                    $data[$record->id]['investmentBalance'] = $identity->lendAccount->investment_balance;
                }
            }
        }


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'data' => $data,
            'isConverted' => $isConverted,
        ]);
    }
}
