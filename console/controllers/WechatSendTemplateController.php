<?php

namespace console\controllers;

use common\models\thirdparty\SocialConnect;
use common\models\wechat\Reply;
use Yii;
use yii\console\Controller;

/**
 * 微信公众号发送模板消息
 * ＠author ZouJianShuang
 * ＠date 2017-11-01
 */
class WechatSendTemplateController extends Controller
{
    public function actionIndex($id)
    {
        $model = Reply::findOne(['id' =>$id, 'style' => 'whole_message', 'isDel' => false]);
        if (is_null($model)) {
            echo '相关模板消息不存在或已禁用';die;
        }
        //获取关注公众号的用户
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $data = $userService->lists();
        if ($data['count'] > 0) {
            $users = $data['data']['openid'];
            self::send($users, $model);
        }

    }

    public static function send($users, $model)
    {
        if (isset($users) && count($users) > 0) {
            echo count($users) . '个微信用户等待发送';
            $app = Yii::$container->get('weixin_wdjf');
            $array = json_decode($model->content);
            $template_id = $array->template_id;
            $url = $array->url;
            $data = $array->data;

//            $template_id = 'Wf2CgM-J0s1Pp7DYngnxNTK6bn-86H2Qehm42uVHP0g';
//            $url = 'https://m.wenjf.com/promotion/p171212/?utm_source=wxmp_wdjf&utm_medium=message&utm_content=171212-01';
//            $data = [
//                'first' => '双12狂欢开启，大额加息券限时返场，充值就送！送！送！',
//                'keyword1' => '双12回馈',
//                'keyword2' => '温都金服',
//                'remark' => "\n由于手机端快捷充值限额，大额充值请使用电脑登录温都金服官网(www.wenjf.com)进行网银充值，有问题请立即与我们联系。点击立即参与活动",
//            ];
            $n = 0; //发送的数量,每五次歇一秒
            foreach ($users as $user) {
                $n ++;
                if ($n > 5) {
                    usleep(1000000);
                    $n = 0;
                }
                try {
                    $app->notice->to($user)->uses($template_id)->andUrl($url)->data($data)->send();
                } catch (\Exception $ex) {
                    continue;
                }
            }
        }
        $lastOpenId = $users[count($users) -1];   //最后一个发送的用户
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $data2 = $userService->lists($lastOpenId);
        if ($data2['count'] > 0) {
            $users2 = $data2['data']['openid'];
            self::send($users2, $model);
        }
    }

    //上传素材
    public function actionUpload()
    {
        $app = Yii::$container->get('weixin_wdjf');
        // 永久素材->图片
        $material = $app->material;
        //删除
//        $mediaId = 'FEIlP-GGH2nXsrGXUcQq2619ZEfn_8in86q6I-IGGVc';
//        $result = $material->delete($mediaId);
        //上传
        $result = $material->uploadImage(Yii::getAlias('@backend') . '/web/upload/wechat/toupiao.jpg');
        var_dump($result);
    }
}