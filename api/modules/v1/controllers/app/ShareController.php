<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\adv\Share;
use common\models\adv\ShareLog;
use common\models\app\AccessToken;
use Yii;

/**
 * Class ShareController
 * @package api\modules\v1\controllers\adv
 *
 * 分享接口
 */
class ShareController extends Controller
{
    /**
     * 根据分享模板key获取模板的内容
     * @return array
     * @throws \api\exceptions\InvalidParamException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionTemplate()
    {
        $data = [];
        $shareKey = $this->getQueryParam('wx_share_key');
        if (!empty($shareKey)) {
            $share = Share::find()->where(['shareKey' => $shareKey])->one();
            if (!empty($share)) {
                $data = [
                    'result' => 'success',
                    'msg' => '成功',
                    'content' => [
                        'key' => $share->shareKey,
                        'title' => $share->title,
                        'desc' => $share->description,
                        'img' => $share->imgUrl,
                        'url' => $share->url,
                    ],
                ];
                $message = '成功';
            } else {
                $message = '找不到share信息';
            }
        } else {
            $message = '参数错误';
        }

        return [
            'status' => empty($data) ? 'fail' : 'success',
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * App分享到微信朋友圈/微信好友回调接口
     * @param $shareUrl   分享url
     * @param $scene    分享场景 session/聊天   timeline/朋友圈
     * @param null $idempotencyKey  用于排重的幂等键，同值请求只会被记为一次分享，建议UUID值
     * @return array  回传给APP的分享结果
     */
    public function actionLog($shareUrl, $scene, $idempotencyKey = null)
    {
        $user = Yii::$app->user;
        if (!$user) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '找不到用户',
                'data' => null,
            ];
        }
        $now = date("Y-m-d", time());
        $ipAddress = Yii::$app->request->getUserIP();
        //todo 此位置需要根据活动添加判断逻辑
        $newShareLog = new ShareLog();
        $newShareLog->shareUrl = $shareUrl;
        $newShareLog->scene = $scene;
        $newShareLog->userId = $user->id;
        $newShareLog->ipAddress = $ipAddress;
        $newShareLog->createdAt = $now;
        $newShareLog->save(false);
        return [
            'status' => 'success',
            'message' => '成功',
            'data' => [
                'result' => 'success',
                'msg' => '成功'      //业务级别成功失败
            ],
        ];
    }
}