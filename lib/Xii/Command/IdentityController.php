<?php

namespace Xii\Command;

use common\models\user\User;
use Xii\Crm\Model\Account;
use Xii\Crm\Model\Contact;
use Yii;
use yii\console\Controller;

class IdentityController extends Controller
{
    /**
     * 定时任务脚本
     *
     * 每5分钟执行一次
     *
     * 同步规则：
     *
     * 每一次取十条
     * 用户创建时间由远到近
     * 当前用户表mobile字段未混淆
     */
    public function actionImport()
    {
        //主要涉及Contact表及account表
        $users = User::find()
            ->where(['is_soft_deleted' => false])
            ->andWhere(['type' => User::USER_TYPE_PERSONAL])
            ->andWhere(['crmAccount_id' => null])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(10)
            ->all();

        $db = Yii::$app->db;
        $num = 0;
        //循环十个用户获得相应的用户信息
        foreach ($users as $user) {
            $transaction = $db->beginTransaction();
            try {
                //写入Contact
                $account = new Account();
                $account->isConverted = true;
                $account->type = Account::TYPE_PERSON;
                if (!$account->save(false)) {
                    throw new \Exception('目标用户写入失败');
                }
                //更新目标用户关联ID
                $user->crmAccount_id = $account->id;
                if (!$user->save(false)) {
                    throw new \Exception('目标用户关联关系写入失败');
                }
                //添加联系方式
                $contact = new Contact();
                $contact->account_id = $account->id;
                $contact->type = Contact::TYPE_MOBILE;
                //obfsNumber为*最后四位
                $contact->obfsNumber = '*' . substr($user->mobile, -4);
                $contact->encryptedNumber = $user->safeMobile;
                if (!$contact->save(false)) {
                    throw new \Exception('目标用户联系方式写入失败');
                }
                //更新目标用户与联系方式的关联
                $account->primaryContact_id = $account->id;
                if (!$account->save(false)) {
                    throw new \Exception('目标用户与联系方式关联关系写入失败');
                }
                $transaction->commit();
                $num++;
            } catch (\Exception $ex) {
                $transaction->rollBack();
                echo $user->id . PHP_EOL;
                continue;
            }
        }
        $this->stdout('共同步目标用户'. $num .'条');

        return self::EXIT_CODE_NORMAL;
    }
}
