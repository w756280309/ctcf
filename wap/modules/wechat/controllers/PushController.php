<?php

namespace app\modules\wechat\controllers;

use common\models\user\User;
use EasyWeChat\Core\Http;
use EasyWeChat\Foundation\Application;
use GuzzleHttp\Client;
use yii\web\Controller;
use Yii;
use common\models\thirdparty\SocialConnect;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\affiliation\UserAffiliation;
use common\helpers\HttpHelper;
use GuzzleHttp\Client as HttpClient;
use EasyWeChat\Core\AccessToken;

/**
 * 微信推送服务
 * 目前记录用户关注微信号的渠道信息
 */
class PushController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        //token验证
        if (Yii::$app->request->isGet) {
            $token = Yii::$app->params['weixin']['token'];
            $signature = Yii::$app->request->get('signature');
            $timestamp = Yii::$app->request->get('timestamp');
            $nonce = Yii::$app->request->get('nonce');
            if (is_null($token) || is_null($signature) || is_null($timestamp) || is_null($nonce)) {
                return false;
            }
            // 将时间戳，随机字符串，token按照字母顺序排序并连接
            $tmp_arr = array($token, $timestamp, $nonce);
            sort($tmp_arr, SORT_STRING);// 字典顺序
            $tmp_str = implode($tmp_arr);//连接
            $tmp_str = sha1($tmp_str);// sha1签名   计算字符串的散列值

            if ($signature == $tmp_str) {
                return Yii::$app->request->get('echostr');
            } else {
                return false;
            }
        }
        //todo 验证请求来源，只许微信服务器访问
        $data = file_get_contents("php://input"); //接收post数据
        if (empty($data)) throw new \Exception('数据为空');
        $postObj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA); //转换post数据为simplexml对象

        //推送欢迎消息
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';
        $appId = Yii::$app->params['weixin']['appId'];
        $appSecret = Yii::$app->params['weixin']['appSecret'];
        if (empty($appId) || empty($appSecret)) {
            throw new \Exception();
        }
        $app = new AccessToken($appId, $appSecret);
        $accessToken = $app->getToken();
        $url = $url . $accessToken;
        $post_data = '{
            "touser":"'.$postObj->FromUserName.'",
            "msgtype":"text",
    "text":
    {
         "content":"亲！欢迎来到温都金服！
国资平台，靠谱！优质项目，赚钱！全程监管，放心！
初次见面，先送您二重好礼略表心意！
一重注册礼：注册就送288红包！
二重首投礼：首次投资送积分、送购物卡！

<a href=\"https://m.wenjf.com/user/wechat/bind\">账户绑定点这里</a>

<a href=\"https://m.wenjf.com/promotion/wrm170210\">领豪礼点这里</a>

<a href=\"\">往期精彩点这里</a>

<a href=\"https://www.wenjf.com\">官方网站</a>",
    }
}';
        self::post($url, $post_data);

        //接受的推送是用户未关注时，进行关注后的事件推送
        if ($postObj->Event != 'subscribe') throw new \Exception('推送事件不是subscribe');
        
        //渠道是否存在
        if (!Affiliator::findOne(mb_substr($postObj->EventKey, 8))) {
            throw new \Exception('缺少渠道');
        }
        //redis保存微信用户渠道信息
        $redis = Yii::$app->redis;
        if (!$redis->hexists('wechat-push', $postObj->FromUserName)) {
            $redis->hset('wechat-push', $postObj->FromUserName, mb_substr($postObj->EventKey, 8));
        }
        /**
         * 推送有可能在用户注册后到达
         */

        $social = SocialConnect::findOne([
            'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
            'resourceOwner_id' => $postObj->FromUserName,
        ]);
        if (!is_null($social)) {
            //绑定渠道
            $user = User::findOne($social->user_id);
            $openId = $postObj->FromUserName;
            if (!UserAffiliation::findOne(['user_id' => $user->id])) {
                self::bindQD($user, $openId);
            }
        }
    }

    //绑定微信扫码用户的渠道
    public static function bindQD($user, $openId)
    {
        if (is_null(UserAffiliation::findOne(['user_id' => $user->id]))) {
            $redis = Yii::$app->redis;
            $affiliator_id = $redis->hget('wechat-push', $openId);
            $affiliator = Affiliator::findOne($affiliator_id);
            $affiliate_cam = AffiliateCampaign::findOne(['affiliator_id' => $affiliator->id]);
            $model = new UserAffiliation();
            $model->user_id = $user->id;
            $model->trackCode = $affiliate_cam->trackCode;
            $model->affiliator_id = $affiliator->id;
            if ($model->save()) {
                $redis->hdel('wechat-push', $openId);
            }
        } else {
            throw new \Exception('用户[$user->id]已经绑定渠道');
        }
    }
    private function post($requestUrl, $post_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, strlen($post_string));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //禁止直接显示获取的内容 重要
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}