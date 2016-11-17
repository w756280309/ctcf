<?php
namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\adv\Share;

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
                        'description' => $share->description,
                        'img' => $share->imgUrl,
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
}