<?php

namespace common\models\user;

use common\lib\validator\LoginpassValidator;
use common\models\affiliation\AffiliationManager;
use common\models\affiliation\Affiliator;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\InviteRecord;
use common\models\promo\PromoService;
use common\models\sms\SmsConfig;
use common\service\SmsService;
use common\utils\SecurityUtils;
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
        $re = User::findOne(['safeMobile' => SecurityUtils::encrypt($num)]);

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
     * 注册主函数，返回false/User(登陆成功后)
     *
     * @param integer      $regFrom    注册来源0未知、1wap、2微信、3app、4pc
     * @param string       $regContext 注册位置
     * @param null|integer $promoId    注册参与的活动ID
     *
     * @return boolean|User
     */
    public function signup($regFrom = User::REG_FROM_OTHER, $regContext, $promoId = null)
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            //初始化User并保存 - 注册模块1
            $user = new User([
                'usercode' => User::create_code(),
                'type' => User::USER_TYPE_PERSONAL,
                'mobile' => $this->phone,
                'username' => '',
                'law_mobile' => '',
                'regContext' => $regContext,
                'safeMobile' => SecurityUtils::encrypt($this->phone),
            ]);
            $user->scenario = 'signup';
            $user->setPassword($this->password);

            //获得当前用户的渠道码
            $campaignSource = Yii::$app->request->cookies->getValue('campaign_source');
            $user->campaign_source = $campaignSource;

            //添加来源 - APP或微信
            if (defined('IN_APP') && IN_APP) {
                $regFrom = User::REG_FROM_APP;
            }
            if ($_SERVER["HTTP_USER_AGENT"] && false !== strpos($_SERVER["HTTP_USER_AGENT"], 'MicroMessenger')) {
                $regFrom = User::REG_FROM_WX;
            }

            //添加注册IP、注册来源、活动ID
            $user->registerIp = Yii::$app->request->getUserIP();
            $user->regFrom = $regFrom;
            $user->promoId = $promoId;
            if (!$user->save()) {
                $transaction->rollBack();
                return false;
            }

            //初始化UserAccount并保存 - 注册模块2
            $user_acount = new UserAccount();
            $user_acount->uid = $user->id;
            $user_acount->type = UserAccount::TYPE_LEND;
            if (!$user_acount->save()) {
                $transaction->rollBack();
                return false;
            }

            //判断用户是否存在邀请码，添加邀请关系 - 注册模块3
            $isInvitee = false; //是否为被邀请者
            $inviteCode = Yii::$app->session->get('inviteCode');
            $inviterCampaignSource = null;
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
                    $inviterCampaignSource = $u->campaign_source;
                    $isInvitee = true;
                }
            }

            //初始化UserInfo并保存 - 注册模块4
            $userInfo = new UserInfo([
                'user_id' => $user->id,
                'isAffiliator' => $isInvitee,
            ]);
            if (!$userInfo->save()) {
                $transaction->rollBack();
                return false;
            }

            //注册即送288元代金券，代金券发送成功后发送短信 - 注册模块5
            $issuedCoupon = false;
            $regCouponTypes = CouponType::findAll(['sn' => [
                '0015:10000-20',    //20元，起投1万，有效期30天
                '0015:20000-30',    //30元，起投2万，有效期30天
                '0016:1000-8',      //8元，起投1000元，有效期30天
                '0016:100000-80',   //80元，起投10万，有效期30天
                '0016:200000-150',  //150元，起投20万，有效期30天
            ]]);
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
                $templateId = '155661';
                $smsConfig = SmsConfig::findOne(['template_id' => $templateId]);
                if ($smsConfig) {
                    SmsService::send(SecurityUtils::decrypt($user->safeMobile), $templateId, $smsConfig->getConfig(), $user);
                }
            }

            //提交注册模块 - 事务提交
            $transaction->commit();

            //注册成功后，所有发送的注册短信验证码置为1，已无效 - 注册后续处理模块1
            SmsService::editSms(SecurityUtils::decrypt($user->safeMobile));

            //记录用户与分销商关系 - 注册后续处理模块2
            //判断如果当前渠道未被标记，且邀请者属于瑞安分销商，则记录用户属于瑞安分销商
            if (null === $campaignSource) {
                //其中2为正式站瑞安分销商ID
                $affiliator = Affiliator::findOne(2);
                if (null !== $affiliator && $affiliator->isAffiliatorCampaign($inviterCampaignSource)) {
                    $campaignSource = $inviterCampaignSource;
                }
            }
            if (null !== $campaignSource) {
                (new AffiliationManager())->log($campaignSource, $user);
            }

            //用户参与活动逻辑 - 注册后续处理模块3
            try {
                //新用户注册，添加抽奖机会
                PromoService::addTicket($user, 'register');
                //用户注册之后给被邀请者送代金券
                PromoService::addInviteeCoupon($user);
            } catch (\Exception $ex) {

            }

            return $user;
        }

        return false;
    }

    /**
     * 找回密码主函数
     *
     * @return boolean
     */
    public function resetpass()
    {
        if ($this->validate()) {
            $model = User::findByUsername(false, $this->phone);
            $model->scenario = 'editpass';
            $model->setPassword($this->password);
            $res = $model->save();
            SmsService::editSms(SecurityUtils::decrypt($model->safeMobile));

            return $res;
        } else {
            return false;
        }
    }
}
