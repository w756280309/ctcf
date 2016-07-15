<?php

namespace common\models\epay;

use yii;

/**
 * This is the model class for table "EpayUser".
 *
 * @property int $id
 * @property string $appUserId
 * @property int $epayId
 * @property string $epayUserId
 * @property string $accountNo
 * @property string $regDate
 * @property int $clientIp
 * @property string $createTime
 */
class EpayUser extends \yii\db\ActiveRecord
{
    use \Zii\Model\ErrorExTrait;

    const MIANMI_AGREEMENT_NUMBER = 'ZTBB0G00'; //免密协议编号
    const KUAIJIE_AGREEMENT_NUMBER = 'ZKJP0700'; //借记卡快捷协议

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EpayUser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appUserId', 'epayId', 'epayUserId', 'regDate', 'clientIp', 'createTime'], 'required'],
            [['epayUserId'], 'unique'],
            [['epayId', 'clientIp'], 'integer'],
            [['regDate', 'createTime'], 'safe'],
            [['appUserId', 'epayUserId', 'accountNo'], 'string', 'max' => 60],
            ['epayId', 'default', 'value' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appUserId' => '应用方用户ID（兼容非数字的用户标识）',
            'epayId' => '托管方ID',
            'epayUserId' => '托管用户ID',
            'accountNo' => '托管账户号',
            'regDate' => '开户日期',
            'clientIp' => 'IP',
            'createTime' => '记录时间',
        ];
    }

    /**
     * 返回联动信息，若无法成功从联动接口获得数据抛异常
     * @param EpayUser $user
     * @return array 形如['code'=> , 'message'=> '']
     * code      message
     * 1         '已开户'
     * 2         '已开通免密'
     * 3         '已开通快捷'
     * 4         '用户余额'
     * @throws \Exception
     */
    public function getUmpAccountStatus(EpayUser $user)
    {
        $userumpinfo = Yii::$container->get('ump')->getUserInfo($user->epayUserId);

        if (!empty($userumpinfo)) {
            if ($userumpinfo->isSuccessful()) {
                if ($userumpinfo->get('balance') > 0) {
                    return ['code' => 4, 'message' => $userumpinfo->get('balance')];
                }
                try{
                    //捕捉是否存在user_bind_agreement_list键，不存在证明没有协议信息，程序继续进行
                    if ('' !== $userumpinfo->get('user_bind_agreement_list')) {
                        //已签订的协议信息数组
                        $agreement_list = explode('|', $userumpinfo->get('user_bind_agreement_list'));
                        if (in_array(static::KUAIJIE_AGREEMENT_NUMBER, $agreement_list)) {
                            //返回已开通快捷
                            return ['code' => 3, 'message'=> '已开通快捷'];
                        }
                        if (in_array(static::MIANMI_AGREEMENT_NUMBER, $agreement_list)) {
                            //返回已开通免密
                            return ['code' => 2, 'message'=> '已开通免密'];
                        }
                    }
                } catch(\Exception $e) {

                }

                //返回状态-开户
                return ['code' => 1, 'message'=> '已开户'];
            } else {
                //抛异常
                throw new \Exception("查询失败");
            }
        } else {
            //抛异常
            throw new \Exception("无法查询到联动信息");
        }
    }
}
