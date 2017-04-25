<?php

namespace Xii\Crm\Controller;


use common\models\user\User;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Xii\Crm\Model\Account;
use Xii\Crm\Model\Contact;
use Xii\Crm\Model\IdentityForm;
use yii\filters\AccessControl;
use yii\web\Controller;

class IdentityController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

    }

    public function actionCreate()
    {
        $model = new IdentityForm();
        $model->numberType = Contact::TYPE_MOBILE;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $contact = Contact::fetchOneByNumber($model->number);
            if (is_null($contact)) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($model->number)]);
                    if (!is_null($user)) {
                        throw new \Exception('用户已经注册，请等待定时任务同步数据');
                    }
                    $account = new Account([
                        'creator_id' => \Yii::$app->user->getIdentity()->getId(),
                        'type' => Account::TYPE_PERSON,
                        'isConverted' => false,
                    ]);
                    $account->save(false);

                    if ($model->numberType === Contact::TYPE_MOBILE) {
                        $obfsNumber = StringUtils::obfsMobileNumber($model->number);
                    } else {
                        $obfsNumber = substr_replace($model->number, '****', strpos($model->number, '-') + 3, 4);
                    }

                    $contact = new Contact([
                        'account_id' => $account->id,
                        'creator_id' => $account->creator_id,
                        'type' => $model->numberType,
                        'obfsNumber' => $obfsNumber,
                        'encryptedNumber' => SecurityUtils::encrypt($model->number),
                    ]);
                    $contact->save(false);

                    $account->primaryContact_id = $contact->id;
                    $account->save(false);

                    $transaction->commit();

                    return $this->redirect('/crm/identity');
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('number', $e->getMessage());
                }

            } else {
                $model->addError('number', '该号码已经被添加');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}