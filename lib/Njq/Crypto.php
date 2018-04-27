<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-17
 * Time: 下午5:28
 */
namespace Njq;

use common\helpers\HttpHelper;
use common\models\thirdparty\Channel;
use common\models\user\User;
use common\utils\SecurityUtils;
use yii\helpers\ArrayHelper;
use Yii;

class Crypto
{
    private $appSecret; //秘钥
    private $platformId; //平台ID
    private $requestTime = null; //请求时间
    private $version = '1.0'; //版本

    public function __construct()
    {
        $this->appSecret = Yii::$app->params['njq']['appSecret'];
        $this->platformId = Yii::$app->params['njq']['platformId'];
        $this->requestTime = time();
    }

    /**
     * 签名
     * @param array $data
     * @return array
     */
    public function sign(array $data)
    {
        $publicData = [
            'platformId' => $this->platformId,//平台ID
            'requestTime' => $this->requestTime,//请求时间戳
            'version' => $this->version,//版本号
            'appSecret' => $this->appSecret,
        ];
        $data = array_merge($data, $publicData);
        //按照参数名排序
        ksort($data);
        $signStr = '';
        foreach ($data as $v) {
            $signStr .= $v;
        }
        unset($data['appSecret']);  //去掉“appSecret”
        return array_merge($data, ['sign' => md5($signStr)]);
    }

    /**
     * 注册南金中心免登
     * @param User $user
     * @return null
     */
    public function signUp(User $user)
    {
        try {
            $data = [
                'mobile' => $user->mobile,
                'name' => $user->real_name,
                'idCard' => SecurityUtils::decrypt($user->safeIdCard),
                'bankCardNo' => !is_null($user->qpay) ? $user->qpay->card_number : null,
                'bankMobile' => $user->mobile,
            ];

            //获取注册的渠道，并添加到注册的参数中
            $campaignSource = $user->campaign_source;
            if (null !== $campaignSource) {
                $data['campaignSource'] = $campaignSource;
            }

            $signData = $this->sign($data);
            $res = HttpHelper::doGet(Yii::$app->params['njq']['baseUri'] . 'user/account/register?' . http_build_query($signData));
            if ($res) {
                $res = json_decode($res, true);
            }
            if ($res['code'] == '2000') {
                $model = new Channel([
                    'userId' => $user->id,
                    'thirdPartyUser_id' => $res['data']['uid'],
                    'createTime' => date('Y-m-d H:i:s'),
                ]);
                $model->save(false);
                return $res['data']['uid'];
            }
        } catch (\Exception $ex) {
            Yii::info('用户【' .$user->id. '】注册南金中心失败，原因：' . $ex->getMessage());
        }

        return null;
    }
}