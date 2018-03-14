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
    public function actionIndex($id, $action = null)
    {
        $model = Reply::findOne(['id' =>$id, 'style' => 'whole_message', 'isDel' => false]);
        if (is_null($model)) {
            echo '相关模板消息不存在或已禁用' . PHP_EOL;
            die;
        }
        //获取关注公众号的用户
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $data = $userService->lists();
        if ($action) {
            if ($data['count'] > 0) {
                $users = $data['data']['openid'];
                self::send($users, $model, $data['next_openid']);
            }
        } else {
            echo '发送消息给'. $data['total'] .'个用户' . PHP_EOL;
        }

    }

    public static function send($users, $model, $next)
    {
        if (isset($users) && count($users) > 0) {
            $app = Yii::$container->get('weixin_wdjf');
            $array = json_decode($model->content);
            $template_id = $array->template_id;
            $url = $array->url;
            $data = $array->data;
            $n = 0; //发送的数量,每10次歇0.5秒
            foreach ($users as $user) {
                $n ++;
                if ($n > 10) {
                    usleep(500000);
                    $n = 0;
                }
                try {
                    $app->notice->to($user)->uses($template_id)->andUrl($url)->data($data)->send();
                } catch (\Exception $ex) {
                    continue;
                }
            }
        }
        $app = Yii::$container->get('weixin_wdjf');
        $userService = $app->user;
        $newData = $userService->lists($next);
        if ($newData['count'] > 0) {
            $newUsers = $newData['data']['openid'];
            self::send($newUsers, $model, $newData['next_openid']);
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

    /**
     * 用来生成全体消息内容的方法（格式：）
     * 调试使用
     */
    public function actionCreateTemplateMessage()
    {
        $data = [
            'template_id' => 'YCe17Ta3LbwhwQWe3aBOuTLE2EgYN_Tlho6mRz7aUC0',     //活动模板id
            'url' => 'https://m.wenjf.com/promotion/p180312/?utm_source=wdjfmb&utm_medium=zsj',     //活动地址（注：加标识）
            'data' => [
                'first' => "红包、超市卡又来啦！把树种起来，奖品全拿走！\n",
                'keyword1' => '植树节种好礼',
                'keyword2' => '温都金服',
                'remark' => "\n点击详情，快去种树拿大奖！",
            ],
        ];
        echo json_encode($data);
    }
}