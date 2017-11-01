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
        $users = SocialConnect::find()->all();
        if (!is_null($users)) {
            $app = Yii::$container->get('weixin_wdjf');
            $template_id = 'SG4yRtRnTSQ6pnBPjPjz762_xcLeTA3oyXjJxIdc2vc';
            $data = [
                'first' => '您好，欢迎来到温都金服双十一理财节！',
                'keyword1' => '喜卡大作战',
                'keyword2' => '温都金服平台',
                'remark' => '活动期间收集喜卡，兑最高1111元现金红包！',
            ];
            foreach ($users as $user) {
                $user = $user->resourceOwner_id;
                $res = $app->notice->to($user)->uses($template_id)->andUrl('')->data($data)->send();
            }
        }

    }
}