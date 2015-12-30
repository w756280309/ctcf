<?php

namespace common\models\user;

use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "user_account".
 *
 * @property string $id
 * @property integer $type
 * @property string $uid
 * @property string $account_balance
 * @property string $available_balance
 * @property string $freeze_balance
 * @property string $in_sum
 * @property string $out_sum
 * @property string $create_at
 * @property string $updated_at
 */
class UserAccount extends \yii\db\ActiveRecord {

    const TYPE_BUY = '1';//投资者
    const TYPE_RAISE = '2';//融资者
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_account';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'uid'], 'integer'],
            [['uid'], 'required'],
            [['account_balance', 'available_balance', 'freeze_balance', 'in_sum', 'out_sum'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'type' => '类型',
            'uid' => 'Uid',
            'account_balance' => '账户余额',
            'available_balance' => '可用余额',
            'freeze_balance' => '冻结余额',
            'in_sum' => '账户入金总额',
            'out_sum' => '账户出金总额',
            'created_at' => '创建时间',
            'updated_at' => '编辑时间',
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 获取用户账户信息
     * @param $uid 用户id
     * @param $type 用户类型
     * @return object
     */
    public static function getUserAccount($uid=null,$type=1){
        return static::findOne(['uid'=>$uid,'type'=>$type]);
    }

    /**
     *
     * @param type $user
     * @return int  返回账户类型，3全部，2融资账户，1投资账户
     */
    public static function selectAccount($user=null){
        $uatype = 0;
        $uid = $user->id;
        $uabuy = static::findOne(['type'=> self::TYPE_BUY,'uid'=>$uid]);//投资用户
        $uasale = static::findOne(['type'=> self::TYPE_RAISE,'uid'=>$uid]);//
        if(!empty($uabuy)&&!empty($uasale)){
            $uatype = 3;
        }else if(!empty($uabuy)||!empty($uasale)){
            $uac = !empty($uabuy)?$uabuy:$uasale;
            $uatype = $uac->type;
        }else{//都没有的需要创建投资账户
            $ua = new UserAccount();
            $ua->uid=$uid;
            $ua->type= UserAccount::TYPE_BUY;
            $ua->save();
            $uatype = 1;
        }
        return $uatype ;
    }

}
