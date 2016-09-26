<?php

namespace common\models\user;

use common\lib\validator\LoginpassValidator;
use common\models\affiliation\AffiliationManager;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\InviteRecord;
use common\service\SmsService;
use Yii;
use yii\base\Model;
use Zii\Validator\CnMobileValidator;

/**
 * Signup form.
 */
class SignupForm extends Model
{
    public $phone;
    public $password;
    public $sms;
    public $reset_flag = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => '手机号码不能为空!'],
            ['sms', 'required', 'message' => '短信验证码不能为空!'],
            ['sms', 'validateSms'],
            ['password', 'required', 'message' => '密码不能为空!'],
            [['phone'], 'checkPhoneUnique'],
            [['phone'], CnMobileValidator::className(), 'skipOnEmpty' => false],
            [
                'password',
                'string',
                'length' => [6, 20],

            ],
            //验证密码格式 不能是纯数字，或是纯字母
            ['password', LoginpassValidator::className(), 'skipOnEmpty' => false],
        ];
    }

    /**
     * 检查手机号是否已经注册过.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkPhoneUnique($attribute, $params)
    {
        $num = $this->$attribute;
        $re = User::findOne(['mobile' => $num]);

        if ($this->reset_flag) {
            if (empty($re)) {
                $this->addError($attribute, '该手机号未注册过');
            } else {
                return true;
            }
        } else {
            if (empty($re)) {
                return true;
            } else {
                $this->addError($attribute, '该手机号已经注册过');
            }
        }
    }

    /**
     * 验证手机验证码
     */
    public function validateSms($attribute, $params)
    {
        $code = $this->$attribute;
        $data = SmsService::validateSmscode($this->phone, $code);
        if ($data['code'] === 1) {
            $this->addError($attribute, $data['message']);
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
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
     * 注册用户主函数.
     */
    public function signup($regFrom = User::REG_FROM_OTHER)
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            $user = new User();
            $user->scenario = 'signup';
            $user->usercode = User::create_code();   //生成usercode
            $user->type = User::USER_TYPE_PERSONAL;
            $user->mobile = $this->phone;
            $user->setPassword($this->password);
            $user->username = '';
            $user->law_mobile = '';
            if (Yii::$app->request->cookies->getValue('campaign_source')) {
                $user->campaign_source = Yii::$app->request->cookies->getValue('campaign_source');
            }
            //添加来源
            if (defined('IN_APP') && IN_APP) {
                $regFrom = User::REG_FROM_APP;
            }
            if ($_SERVER["HTTP_USER_AGENT"] && false !== strpos($_SERVER["HTTP_USER_AGENT"], 'MicroMessenger')) {
                $regFrom = User::REG_FROM_WX;
            }
            $user->regFrom = $regFrom;
            if (!$user->save()) {
                $transaction->rollBack();

                return false;
            }

            $user_acount = new UserAccount();
            $user_acount->uid = $user->id;
            $user_acount->type = UserAccount::TYPE_LEND;
            if (!$user_acount->save()) {
                $transaction->rollBack();

                return false;
            }

            //邀请好友
            $inviteCode = Yii::$app->session->get('inviteCode');
            if ($inviteCode) {
                $u = User::findOne(['usercode' => $inviteCode]);
                if ($u) {
                    $invite = new InviteRecord([
                        'user_id' => $u->id,
                        'invitee_id' => $user->id,
                    ]);

                    if (!$invite->save()) {
                        $transaction->rollBack();
                        return false;
                    }

                    $couponType = CouponType::findOne(['sn' => '0011:10000-50']);   //赠送50元代金券
                    if ($couponType && $couponType->allowIssue()) {
                        UserCoupon::addUserCoupon($user, $couponType)->save();
                    }
                }
            }

            //注册即送代金券
            $regCouponTypes = CouponType::findAll(['sn' => [
                '0015:10000-20',    //20元，起投1万，有效期30天
                '0015:20000-30',    //30元，起投2万，有效期30天
                '0016:1000-8',      //8元，起投1000元，有效期30天
                '0016:100000-80',   //80元，起投10万，有效期30天
                '0016:200000-150',  //150元，起投20万，有效期30天
            ]]);
            $issuedCoupon = false;

            foreach ($regCouponTypes as $regCouponType) {
                try {
                    if (UserCoupon::addUserCoupon($user, $regCouponType)->save()) {
                        $issuedCoupon = true;
                    }
                } catch (\Exception $ex) {
                    // do nothing.
                }
            }

            if ($issuedCoupon) {
                $message = [
                    'http://dwz.cn/43x1YL',
                    Yii::$app->params['contact_tel'],
                ];

                $templateId = Yii::$app->params['sms']['register_coupon'];

                SmsService::send($user->mobile, $templateId, $message, $user);
            }

            $transaction->commit();
            SmsService::editSms($user->mobile);
            if (Yii::$app->request->cookies->getValue('campaign_source')) {
                (new AffiliationManager())->log(Yii::$app->request->cookies->getValue('campaign_source'), $user);
            }
            return $user;
        } else {
            return false;
        }
    }

    /**
     * 找回密码主函数.
     */
    public function resetpass()
    {
        if ($this->validate()) {
            $model = User::findByUsername(false, $this->phone);
            $model->scenario = 'editpass';
            $model->setPassword($this->password);
            $res = $model->save();
            SmsService::editSms($model->mobile);

            return $res;
        } else {
            return false;
        }
    }
}
