<?php

namespace common\models\thirdparty;

use common\models\user\User;
use Yii;
use yii\db\ActiveRecord;

class SocialConnect extends ActiveRecord
{
    const PROVIDER_TYPE_WECHAT = 'wechat';

    public static function tableName()
    {
        return 'social_connect';
    }

    public static function initNew(User $user, $ownerId, $type)
    {
        return new self([
            'user_id' => $user->id,
            'resourceOwner_id' => $ownerId,
            'provider_type' => $type,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 用户绑定某种类型的第三方ID
     *
     * @param User   $user      用户对象
     * @param int    $ownerId   第三方ID
     * @param string $type      类型
     *
     * @return bool
     * @throws \Exception
     */
    public static function bind(User $user, $ownerId, $type)
    {
        if (!$user || !$ownerId || !$type) {
            throw new \Exception('缺少参数');
        }

        $connect = SocialConnect::find()
            ->where(['resourceOwner_id' => $ownerId])
            ->andWhere(['provider_type' => $type])
            ->one();

        if (null !== $connect) {
            if ($connect->user_id !== $user->id) {
                throw new \Exception('您已绑定其他账号');
            }
            throw new \Exception('您已绑定此账号');
        }

        $connect = SocialConnect::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['provider_type' => $type])
            ->one();

        if (null !== $connect) {
            throw new \Exception('该账号已被其他微信号绑定，如需绑定，请在其他微信号上进行解绑');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $newConnect = self::initNew($user, $ownerId, $type);
            $newConnect->save(false);
            SocialConnectLog::initNew($newConnect, 'bind')->save(false);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 解绑用户与某种类型的第三方ID的关联
     *
     * @param int    $userId    用户ID
     * @param int    $ownerId   第三方ID
     * @param string $type      类型
     *
     * @return bool
     * @throws \Exception
     */
    public static function unbind($userId, $ownerId, $type)
    {
        if (!$userId || !$ownerId || !$type) {
            throw new \Exception('缺少参数');
        }
        $connect = SocialConnect::findOne([
            'user_id' => $userId,
            'resourceOwner_id' => $ownerId,
            'provider_type' => $type,
        ]);
        if (null === $connect) {
            throw new \Exception('未找到对应的绑定关系');
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $flag = (bool) $connect->delete();
            if ($flag) {
                SocialConnectLog::initNew($connect, 'unbind')->save(false);
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        return $flag;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'resourceOwner_id' => '来源所属ID',
            'provider_type' => '类型',
            'createTime' => '创建时间',
        ];
    }
}
