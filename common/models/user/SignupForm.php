<?php
namespace common\models\user;

use Yii;
use yii\base\Model;
use common\service\SmsService;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $phone;
    public $password;
    public $sms;
    public $reset_flag = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => '手机号码不能为空!'],
            ['sms', 'required', 'message' => '短信验证码不能为空!'],
            ['sms', 'validateSms'],
            ['password', 'required', 'message' => '密码不能为空!'],
            [['phone'],'checkPhoneUnique'],
            [['phone'],'match','pattern'=>'/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/','message'=>'手机号码格式错误'],
            [
                'password',
                'string',
                'length' => [6, 12],

            ],
            //验证密码格式 不能是纯数字，或是纯字母
            ['password', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/','message' => '密码必须为数字和字母的组合'],  
        ];
    }

    /**
     * 检查手机号是否已经注册过
     * @param type $attribute
     * @param type $params
     * @return boolean
     */
    public function checkPhoneUnique($attribute,$params){
        $num = $this->$attribute;
        $re = User::findOne(['mobile'=>$num]);
        
        if($this->reset_flag) {
            if(empty($re)){
                $this->addError($attribute, "该手机号未注册过"); 
            }else{
                return true;  
            }
        } else {
            if(empty($re)){
                return true;
            }else{
                $this->addError($attribute, "该手机号已经注册过");  
            }
        }
    }
    
    /**
     * 验证手机验证码
     */
    public function validateSms($attribute, $params) {
            $code = $this->$attribute;
            $uid = $this->phone;
            $data = SmsService::validateSmscode($uid,$code);
            if($data['code']==1) {
                $this->addError($attribute, $data['message']);
            } else {
                return TRUE;
            }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => '手机号码',
            'password' => '密码',
            'sms' => '短信验证码',
        ];
    }

    /**
     * 注册用户主函数
     */
    public function signup()
    {
        if($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            $user = new User();
            $user->scenario = 'signup';
            $user->usercode = User::create_code();   //生成usercode
            $user->type = User::USER_TYPE_PERSONAL;
            $user->mobile = $this->phone;
            $user->setPassword($this->password);
            if(!$user->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $user_acount = new UserAccount();
            $user_acount->uid = $user->id;
            $user_acount->type = UserAccount::TYPE_BUY;
            if(!$user_acount->save()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            $res = SmsService::editSms($user->id,$user->mobile);
            return $user;
        } else {
            return false;
        }
    }
    
    /**
     * 找回密码主函数
     */
    public function resetpass()
    {
        if($this->validate()) {
            $model = User::findByUsername(false, $this->phone);
            $model->scenario = 'editpass';
            $model->setPassword($this->password);
            $res = $model->save();
            return $res;
        } else {
            return false;
        }
    }

}
