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
use common\models\affiliation\AffiliateCampaign;
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
            $postObj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA); //转换post数据为simplexml对象
            if ($postObj->Event == 'subscribe') {
                //欢迎信息
                $res = self::hello($postObj->FromUserName);
            } else if (strtolower($postObj->Event) == 'click') {
                $res = self::sendMessage($postObj->EventKey, $postObj->FromUserName);
            }
            //被动回复
            self::passiveREsponse($postObj);

            if (($postObj->Event == 'subscribe' || $postObj->Event == 'SCAN') && $postObj->FromUserName) {
                //绑定渠道
                if ($postObj->Event == 'subscribe') {
                    $event_key = mb_substr(strval($postObj->EventKey), 8);
                } else {
                    $event_key = strval($postObj->EventKey);
                }
                $affiliator = Affiliator::findOne($event_key);
                if (!is_null($affiliator)) {
                    $redis = Yii::$app->redis;
                    $redis->hset('wechat-push', $postObj->FromUserName, $event_key);
                    //推送客服人员
                    $lc_mes = '您目前的客服专员是【'.$affiliator->name.'】，有任何疑问他(她)都会帮您解答。';
                    Yii::$container->get('weixin_wdjf')->staff->message($lc_mes)->to(strval($postObj->FromUserName))->send();
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
                    } else {
                        //已经绑定微信的则删除缓存
//                        $redis->hdel('wechat-push', $postObj->FromUserName);
                    }
                }
            }
        }
    }

    //绑定微信扫码用户的渠道
    public static function bindQD($user, $openId)
    {
        if (is_null(UserAffiliation::findOne(['user_id' => $user->id]))) {
            $redis = Yii::$app->redis;
            $affiliator_id = $redis->hget('wechat-push', $openId);
            if ($affiliator_id) {
                $affiliator = Affiliator::findOne($affiliator_id);
                $affiliate_cam = AffiliateCampaign::findOne(['affiliator_id' => $affiliator->id]);
                $model = new UserAffiliation();
                $model->user_id = $user->id;
                $model->trackCode = $affiliate_cam->trackCode;
                $model->affiliator_id = $affiliator->id;
                if ($model->save()) {
                    $redis->hdel('wechat-push', $openId);
                }
            }
        } else {
            //throw new \Exception('用户[$user->id]已经绑定渠道');
        }
    }

    //发送欢迎消息
    private function hello($openid)
    {
        //推送欢迎消息
        $message = new Text(['content' => '亲！欢迎来到温都金服！
国资平台，靠谱！优质项目，赚钱！全程监管，放心！
初次见面，先送您二重好礼略表心意！
一重注册礼：注册就送288红包！
二重首投礼：首次投资送最高160元超市卡！

' .'<a href="https://m.wenjf.com/luodiye/v2">领超市卡点这里</a>

' .'<a href="https://m.wenjf.com/user/wechat/bind">账户绑定点这里</a>

' . '<a href="https://mp.weixin.qq.com/mp/profile_ext?action=home&scene=115&__biz=MzI0MTIwOTQxMQ==#wechat_redirect">往期精彩点这里</a>

' . '<a href="https://www.wenjf.com">官方网站</a>'
        ]);
        Yii::$container->get('weixin_wdjf')->staff->message($message)->to(strval($openid))->send();
    }
    /**
     * 自定义菜单事件的推送
     */
    static function sendMessage($key, $openid)
    {
        $key = strval($key);
        $message = [
            'COMPANYINTRODUC' => '温州温都金融信息服务股份有限公司简称[温都金服]，是温州报业传媒旗下理财平台，重要股东为温州报业传媒有限公司和南京金融资产交易中心，双方整合自身强大的公信力、受众、行业资源优势，以及专业的风控能力、理财产品采集能力、网络技术能力等，为用户呈现更契合自身需求的理财产品，为用户创造更多的价值，为社会营造诚信良好的金融环境，有效推动普惠金融的发展。',
            'OPINIONFEEDBACK' => '温都金服上线啦/鼓掌/鼓掌/鼓掌，这是咱们温州自己的金融服务平台，刚刚上线，有什么问题或意见可以告诉我们，好的建议我们会给予一定奖励哟~/呲牙/呲牙/呲牙/握手/握手。请点击左侧键盘，回复内容[意见：+正文]',
            'CONTACTUS' => '客服电话： 4001015151
客服工作时间：周一至周日8:30-20:00
客服QQ：1430843929
电子邮箱：wzwdjf@sina.com
工作时间：周一至周六8:30-17:00
办公地点：温州市鹿城区飞霞南路657号保丰大楼四层

服务体验店地址：
保丰服务体验店：温州市鹿城区飞霞南路657号保丰大楼一层
服务体验店工作时间：周一至周六8:30-17:00',
        ];
        if (array_key_exists($key, $message)) {
            $content = $message[$key];
            Yii::$container->get('weixin_wdjf')->staff->message($content)->to(strval($openid))->send();
        }
    }
    //被动回复
    public static function passiveREsponse($postObj)
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