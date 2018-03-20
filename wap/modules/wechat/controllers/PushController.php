<?php

namespace app\modules\wechat\controllers;

use common\models\user\User;
use common\models\wechat\Reply;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use yii\web\Controller;
use Yii;
use common\models\thirdparty\SocialConnect;
use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
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
        if (!empty($data)) {
            try {
                $postObj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA); //转换post数据为simplexml对象
                if ($postObj->Event == 'subscribe') {
                    //欢迎信息
                    self::hello($postObj->FromUserName);
                    Yii::info('【' . $postObj->FromUserName . '】扫码关注了公众号');
                } else if (strtolower($postObj->Event) == 'click') {
                    //菜单点击事件
                    self::sendMessage($postObj->EventKey, $postObj->FromUserName);
                    Yii::info('【' . $postObj->FromUserName . '】点击了【' . $postObj->EventKey . '】');
                }
                //被动回复
                self::passiveResponse($postObj);
                if (($postObj->Event == 'subscribe' || $postObj->Event == 'SCAN') && $postObj->FromUserName) {
                    //绑定渠道
                    if ($postObj->Event == 'subscribe') {
                        $event_key = mb_substr(strval($postObj->EventKey), 8);
                    } else {
                        $event_key = strval($postObj->EventKey);
                    }
                    Yii::info('【' . $postObj->FromUserName . '】扫了渠道码：' . $event_key);
                    $affiliator = Affiliator::findOne($event_key);
                    if (!is_null($affiliator)) {
                        $redis = Yii::$app->redis;
                        $redis->hset('wechat-push', $postObj->FromUserName, $event_key);
                        //推送客服人员
                        $lc_mes = '您目前的客服专员是【'.$affiliator->name.'】，有任何疑问他(她)都会帮您解答。';
                        Yii::$container->get('weixin_wdjf')->staff->message($lc_mes)->to(strval($postObj->FromUserName))->send();
                        /**
                         * 推送有可能在用户注册后到达
                         * 暂定有效时间为15分钟
                         */
                        $social = SocialConnect::findOne([
                            'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
                            'resourceOwner_id' => $postObj->FromUserName,
                        ]);
                        if (!is_null($social) && !is_null($social->user) && time() - $social->user->created_at < 15 * 60) {
                            //绑定渠道
                            $user = User::findOne($social->user_id);
                            $openId = $postObj->FromUserName;
                            if (!$user->userAffiliation) {
                                self::bindQD($user, $openId);
                            }
                        }
                    }
                }
            } catch (\Exception $ex) {
                return '';
            }
        }
        return '';  //给微信服务器返回空字符串，防止重复请求
                    //（注：微信服务器在五秒内收不到响应会断掉连接，并且重新发起请求，总共重试三次。）
    }

    //绑定微信扫码用户的渠道
    public static function bindQD($user, $openId)
    {
        $redis = Yii::$app->redis;
        $affiliator_id = $redis->hget('wechat-push', $openId);
        if ($affiliator_id) {
            $affiliator = Affiliator::findOne($affiliator_id);
            $model = UserAffiliation::findOne(['user_id' => $user->id]);
            if (is_null($model)) {
                $model = new UserAffiliation();
            }
            $model->user_id = $user->id;
            $model->trackCode = $affiliator->campaign->trackCode;
            $model->affiliator_id = $affiliator->id;
            $res = $model->save();
            $logMessage = $res ? '用户【' . $user->id . '】成功绑定渠道：' . $model->trackCode
                : '用户【' . $user->id . '】绑定渠道失败，原因：' . json_encode($model->getErrors());
            Yii::info($logMessage);
        }
    }

    //发送欢迎消息
    private function hello($openid)
    {
        $hello = Yii::$app->params['wechat_push']['hello_message'];
        if (!empty($hello)) {
            //推送欢迎消息
            $message = new Text(['content' => $hello]);
            Yii::$container->get('weixin_wdjf')->staff->message($message)->to(strval($openid))->send();
        }
    }
    /**
     * 自定义菜单事件的推送
     */
    static function sendMessage($key, $openid)
    {
        $key = strval($key);
        $message = Yii::$app->params['wechat_push']['click_message'];
        if (array_key_exists($key, $message)) {
            $content = $message[$key];
            if (!empty($content)) {
                Yii::$container->get('weixin_wdjf')->staff->message($content)->to(strval($openid))->send();
            }
        }
    }
    //被动回复
    public static function passiveResponse($postObj)
    {
        if (strtolower($postObj->MsgType) == 'text' && !is_null($postObj->Content)) {
            $openid = strval($postObj->FromUserName);
            $app = Yii::$container->get('weixin_wdjf');
            //自动编辑的
            $replys = Reply::find()->where(['isDel' => false])->all();
            foreach ($replys as $reply) {
                if(strpos(strval($postObj->Content), $reply->keyword) !== false) {
                    if ($reply->type == 'text') {   //文本
                        $message = $reply->content;
                        $app->staff->message($message)->to(strval($openid))->send();
                    } else if ($reply->type == 'image') {   //图片
                        $message = new Image(['media_id' => $reply->content]);
                        $app->staff->message($message)->to(strval($openid))->send();
                    } else if ($reply->type == 'layout') {  //模板
                        $datas = json_decode($reply->content);
                        $template_id = $datas->template_id;
                        $url = $datas->url;
                        $data = $datas->data;
                        $app->notice->to($openid)->uses($template_id)->andUrl($url)->data($data)->send();
                    }
                }
            }
        }
    }
}