<?php

namespace console\controllers;

use common\models\thirdparty\SocialConnect;
use Yii;
use yii\console\Controller;

/**
 * 微信公众号发送模板消息
 * ＠author ZouJianShuang
 * ＠date 2017-11-01
 */
class WechatSendTemplateController extends Controller
{
    public function actionIndex()
    {
        //获取关注公众号的用户
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $data = $userService->lists();
        if ($data['count'] > 0) {
            $users = $data['data']['openid'];
        }
        if (isset($users) && count($users) > 0) {
            $app = Yii::$container->get('weixin_wdjf');
            $template_id = '4uRGKS0QHt28d7-Hka6SVPFk3uIv8Ddn1qJ5LG9rvH0';
            $url = 'https://m.wenjf.com/promotion/p171111/second';
            $data = [
                'first' => '温都金服双十一理财节开幕在即，预约享全场加息！',
                'keyword1' => '预约加息',
                'keyword2' => '温都金服平台',
                'remark' => '点击完成理财预约，领专属大额加息券！',
            ];
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
    }
}