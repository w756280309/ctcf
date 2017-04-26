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
            $ext = [
                'mobile' => null,
                'landline' => null,
                'gender' => null,
                'name' => null,
                'age' => null,
                'annualInvestment' => null,
                'investTotal' => null,
                'investCount' => 0,
                'availableBalance' => null,
                'investmentBalance' => null,
            ];

            $contactMobile = Contact::findContactByAccountId($record->id)->andWhere(['type' => Contact::TYPE_MOBILE])->one();
            if (null !== $contactMobile) {
                $ext['mobile'] = $contactMobile->obfsNumber;
            }

            $contactLandline = Contact::findContactByAccountId($record->id)->andWhere(['type' => Contact::TYPE_LANDLINE])->one();
            if (null !== $contactLandline) {
                $ext['landline'] = $contactLandline->obfsNumber;
            }

            $identity = $record->getIdentity();
            if (null !== $identity) {
                $ext['gender'] = $identity->getCrmGender();
                $ext['name'] = $identity->getCrmName();
                $ext['age'] = $identity->getCrmAge();

                if ($identity instanceof User) {
                    $ext['mobile'] = '*' . substr($identity->mobile, -4);
                    $ext['annualInvestment'] = $identity->annualInvestment;

                    if (isset($identity->info)) {
                        $ext['investCount'] = $identity->info->investCount;
                        $ext['investTotal'] = $identity->info->investTotal;
                    }

                    if (isset($identity->lendAccount)) {
                        $ext['availableBalance'] = $identity->lendAccount->available_balance;
                        $ext['investmentBalance'] = $identity->lendAccount->investment_balance;
                    }
                }
            }

            $data[$record->id] = $ext;
        }


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'data' => $data,
            'isConverted' => $isConverted,
        ]);
    }
}
