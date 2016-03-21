<?php

namespace common\service;

use Yii;
use Exception;
use common\models\user\User;
use common\models\epay\EpayUser;

/**
 * 联动服务类.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class UmpService
{
    /**
     * 联动开户
     * 注：联动方测试环境注册不判断重复，但是会判断60s内不能注册.
     *
     * @param User $user
     *
     * @return EpayUser
     */
    public function register(User $user)
    {
        $user->real_name = Yii::$app->functions->removeWhitespace($user->real_name);//去除所有空格
        $resp = Yii::$container->get('ump')->register($user);
        Yii::trace('开户响应内容:'.http_build_query($resp->toArray()), 'umplog');
        if (!$resp->isSuccessful()) {
            throw new Exception($resp->get('ret_code').':'.$resp->get('ret_msg'));
        }
        $transaction = Yii::$app->db->beginTransaction();
        $epayUser = new EpayUser([
            'appUserId' => $user->getUserId(),
            'clientIp' => ip2long(Yii::$app->request->userIP),
            'regDate' => $resp->get('reg_date'),
            'createTime' => date('Y-m-d H:i:s'),
            'epayUserId' => $resp->get('user_id'),
            'accountNo' => $resp->get('account_id'),
        ]);

        if (!$epayUser->save(false)) {
            $transaction->rollBack();
            throw new Exception('开户失败');
        }

        $user->scenario = 'idcardrz';
        $user->idcard_status = User::IDCARD_STATUS_PASS;
        if (!$user->save(false)) {
            $transaction->rollBack();
            throw new Exception('实名认证失败');
        }
        $transaction->commit();

        return $epayUser;
    }
}
