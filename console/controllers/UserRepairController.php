<?php

namespace console\controllers;

use Wcg\Xii\Crm\Model\AccountContact;
use yii\console\Controller;
use common\models\offline\OfflineUser;
use yii;

class UserRepairController extends Controller
{
    public function actionDel($id){
        $db = Yii::$app->db;

        if (!is_null($id)) {

            $info =  OfflineUser::findOne($id);

            if (!is_null($info)) {
                $crm_account_id = $info->crmAccount_id;
                $transaction = $db->beginTransaction();
                try {
                    //删除用户
                    $sql = "delete from offline_user where id =" . $id;
                    if(false === $db->createCommand($sql)->execute()) {
                        $transaction->rollBack();
                    }

                    //删除用户订单
                    $sql = "delete from offline_order where user_id =" . $id;
                    if(false === $db->createCommand($sql)->execute()) {
                        $transaction->rollBack();
                    }

                    //删除用户积分记录
                    $sql = "delete from point_record where isOffline = true and user_id =" . $id;
                    if(false === $db->createCommand($sql)->execute()) {
                        $transaction->rollBack();
                    }

                    //删除用户的财富值
                    $sql = "delete from coins_record where isOffline = true and user_id = " . $id;
                    if(false === $db->createCommand($sql)->execute()) {
                        $transaction->rollBack();
                    }

                    //删除相关的crm
                    if (!is_null($crm_account_id)) {
                        $contact = AccountContact::find()->where(['account_id' => $crm_account_id])->all();

                        foreach ($contact as $contacts) {
                            //删除contact
                            $sql = "delete from crm_contact where id = " . $contacts->contact_id;
                            if(false === $db->createCommand($sql)->execute()) {
                                $transaction->rollBack();
                            }
                        }

                        //删除account
                        $sql = "delete from crm_account where id = " . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        //删除account_contact
                        $sql = "delete from crm_account_contact where account_id = " . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        //删除identity
                        $sql = "delete from crm_identity where account_id = " . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        //删除activity
                        $sql = "delete from crm_activity where account_id =" . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        $sql = "delete from crm_branch_visit where account_id =" . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        $sql = "delete from crm_gift where account_id =" . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        $sql = "delete from crm_note where account_id =" . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }

                        $sql = "delete from crm_phone_call where account_id =" . $crm_account_id;
                        if(false === $db->createCommand($sql)->execute()) {
                            $transaction->rollBack();
                        }
                    }


                } catch (\Exception $ex) {
                    $transaction->rollBack();
                    echo '删除用户失败' . $ex->getMessage() . PHP_EOL;
                }
                $transaction->commit();
                $this->stdout('线下用户删除完毕');
                return self::EXIT_CODE_NORMAL;
            }

        } else {
            $this->stdout('缺少用户id');
            return self::EXIT_CODE_ERROR;
        }
    }
}