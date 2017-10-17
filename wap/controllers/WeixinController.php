<?php

namespace app\controllers;

use common\models\weixin\WeixinAuth;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use common\controllers\HelpersTrait;

class WeixinController extends Controller
{
    use HelpersTrait;

    public function actionAuth($appId, $url)
    {
        try {
            $auth = WeixinAuth::findOne($appId);

            if (null === $auth || empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception();
            }

            $this->getJsApiTicket($auth);   //得到有效的jsApiTicket

            $params = $this->sign($auth, $url);   //签名
        } catch (Exception $ex) {
            Yii::$app->response->statusCode = 400;

            return [
                'message' => '请求失败',
            ];
        }

        return [
            'timestamp' => $params['timestamp'], // 必填，生成签名的时间戳
            'nonceStr' => $params['noncestr'], // 必填，生成签名的随机串
            'signature' => $params['sign'],// 必填，签名，见附录1
        ];
    }

    /**
     * 获取有效的JsApiTicket
     */
    private function getJsApiTicket(WeixinAuth $weiAuth)
    {
        if (null === $weiAuth) {
            throw new \Exception();
        }

        if ($weiAuth->expiresAt >= time()) {
            return $weiAuth;
        }

        $appId = Yii::$app->params['weixin']['appId'];
        $appSecret = Yii::$app->params['weixin']['appSecret'];

        if (empty($appId) || empty($appSecret)) {
            throw new \Exception();
        }

        $token = $this->get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret);
        $accessToken = json_decode($token, true);
        $ticket = $this->get('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='. $accessToken['access_token'] .'&type=jsapi');
        $jsapiTicket = json_decode($ticket, true);

        if (empty($accessToken['access_token']) || empty($jsapiTicket['ticket'])) {
            throw new \Exception();
        }

        $weiAuth->accessToken = $accessToken['access_token'];
        $weiAuth->jsApiTicket = $jsapiTicket['ticket'];
        $weiAuth->expiresAt = time() + 7200 - 600;

        $weiAuth->save();

        return $weiAuth;
    }

    /**
     * 以get的方式请求.
     */
    private function get($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }

    /**
     * 签名函数.
     */
    private function sign(WeixinAuth $auth, $url)
    {
        $params = [
            'noncestr' => sha1(uniqid(mt_rand(), true)),
            'jsapi_ticket' => $auth->jsApiTicket,
            'timestamp' => time(),
            'url' => $url,
        ];

        ksort($params);

        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs[] = $key.'='.$value;
        }

        $params['sign'] = sha1(implode('&', $pairs));

        return $params;
    }

    /**
     * 用户授权
     * @param $url 获取用户信息后跳转的地址
     */
    public function actionCallback()
    {
        $isWx = $this->fromWx();
        if (!$isWx || Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }
        $code = Yii::$app->request->get('code');
        $state = Yii::$app->request->get('state');
        $redirect = Yii::$app->request->get('redirect');
        if (!Yii::$app->session->has('resourceOwnerId') || !Yii::$app->session->has('resourceOwnerNickName')) {
            if ($code && $state == Yii::$app->session->get('getGrantState')) {
                $wxClient = Yii::$container->get('wxClient');
                $response = $wxClient->getGrant($code);
                $info = $wxClient->getResourceOwnerInfo($response);
                Yii::$app->session->set('resourceOwnerId', $response['resource_owner_id']);
                Yii::$app->session->set('resourceOwnerNickName', $info['nickName']);
            }

            $resourceOwnerId = Yii::$app->session->get('resourceOwnerId');

            if ($resourceOwnerId && Yii::$app->user->isGuest) {
                $social = SocialConnect::findOne([
                    'resourceOwner_id' => $resourceOwnerId,
                    'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
                ]);

                if (is_null($social)) {
                    return new ForbiddenHttpException();
                }

                $user = User::findOne($social->user_id);

                if (is_null($user)) {
                    return new ForbiddenHttpException();
                }

                Yii::$app->user->login($user);    //微信绑定,自动登录
            }
        }

        return Yii::$app->controller->redirect($redirect);
    }
}
