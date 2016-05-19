<?php

namespace app\controllers;

use common\models\weixin\WeixinAuth;
use common\models\weixin\WeixinUrl;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class WeixinController extends Controller
{
    public function actionAuth($id)
    {
        try {
            $weiUrl = WeixinUrl::findOne($id);

            if (null === $weiUrl) {
                throw new \Exception();
            }

            $this->getJsApiTicket($weiUrl->auth);   //得到有效的jsApiTicket

            $params = $this->sign($weiUrl);   //签名
        } catch (Exception $ex) {
            Yii::$app->response->statusCode = 400;

            return [
                'message' => '请求失败',
            ];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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

        $token = $this->get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. $weiAuth->appId .'&secret=a1168f2164fdb691cc0483ebbda3fee4');
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
    private function sign(WeixinUrl $weiUrl)
    {
        $params = [
            'noncestr' => sha1(uniqid(mt_rand(), true)),
            'jsapi_ticket' => $weiUrl->auth->jsApiTicket,
            'timestamp' => time(),
            'url' => $weiUrl->url,
        ];

        ksort($params);

        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs[] = $key.'='.$value;
        }

        $params['sign'] = sha1(implode('&', $pairs));

        return $params;
    }
}
