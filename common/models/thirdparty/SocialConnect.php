<?php

namespace common\models\thirdparty;

use common\models\mall\PointRecord;
use common\models\message\PointMessage;
use common\models\user\User;
use common\service\PointsService;
use Lhjx\Noty\Noty;
use Yii;
use yii\db\ActiveRecord;

class SocialConnect extends ActiveRecord
{
    const PROVIDER_TYPE_WECHAT = 'wechat';

    public static function tableName()
    {
        return 'social_connect';
    }

    public static function initNew(User $user, $openId, $type)
    {
        return new self([
            'user_id' => $user->id,
            'resourceOwner_id' => $openId,
            'provider_type' => $type,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 用户绑定某种类型的第三方ID
     *
     * @param User   $user      用户对象
     * @param int    $openId   第三方ID
     * @param string $type      类型
     *
     * @return bool
     * @throws \Exception
     */
    public static function connect(User $user, $openId, $type)
    {
        if (!$user || !$openId || !$type) {
            throw new \Exception('缺少参数');
        }

        $connect = SocialConnect::find()
            ->where(['resourceOwner_id' => $openId])
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
            $newConnect = self::initNew($user, $openId, $type);
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
     * @param int    $openId   第三方ID
     * @param string $type      类型
     *
     * @return bool
     * @throws \Exception
     */
    public static function disconnect($userId, $openId, $type)
    {
        if (!$userId || !$openId || !$type) {
            throw new \Exception('缺少参数');
        }
        $connect = SocialConnect::findOne([
            'user_id' => $userId,
            'resourceOwner_id' => $openId,
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

    /**
     * 判断一个指定用户有没有绑定指定设备.
     */
    public function isConnected(User $user, $openId, $type)
    {
        $socialConnect = self::findOne([
            'user_id' => $user->id,
            'provider_type' => $type,
            'resourceOwner_id' => $openId,
        ]);

        return !is_null($socialConnect);
    }
    /**
     * 绑定微信后续操作
     * 发放积分
     */
    public static function bind($user, $openId)
    {
        $social = SocialConnect::findOne([
            'user_id' => $user->id,
            'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
            'resourceOwner_id' => $openId,
        ]);

        if (!is_null($social)) {

            $pointRecord = PointRecord::findOne([
                'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                'user_id' => $user->id,
            ]);

            if (is_null($pointRecord)) {
                //绑定成功,发放10积分
                $pointRecord = new PointRecord([
                    'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                    'ref_id' => $social->id,
                    'incr_points' => 10,
                ]);

                $res = PointsService::addUserPoints($pointRecord, false, $user);

                if ($res) {
                    $pointRecord = PointRecord::findOne([
                        'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                        'ref_id' => $social->id,
                        'incr_points' => 10,
                        'user_id' => $user->id,
                    ]);

                    if ($pointRecord) {
                        Noty::send(new PointMessage($pointRecord));
                    }
                }
            }
        }
        return true;
    }
    //获取绑定该微信号的用户
    public function getUser()
    {
        return User::findOne($this->user_id);
    }
}
