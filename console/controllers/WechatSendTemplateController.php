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
            self::send($users);
        }

    }

    public static function send($users)
    {
        if (isset($users) && count($users) > 0) {
            $app = Yii::$container->get('weixin_wdjf');
            $template_id = 'SG4yRtRnTSQ6pnBPjPjz762_xcLeTA3oyXjJxIdc2vc';
            $url = 'https://m.wenjf.com/promotion/promo-points/p171123';
            $data = [
                'first' => '寒冬来临，气温骤降，温都金服提醒您及时保暖添衣，温暖出行。祝您感恩节快乐！',
                'keyword1' => '预约加息',
                'keyword2' => '温都金服平台',
                'remark' => '点击详情，立即参与感恩节回馈活动，海量积分等你领！',
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
            echo count($users) . '个微信用户发送成功';
        }
        $lastOpenId = $users[count($users) -1];   //最后一个发送的用户
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $data2 = $userService->lists($lastOpenId);
        if ($data2['count'] > 0) {
            $users2 = $data2['data']['openid'];
            self::send($users2);
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