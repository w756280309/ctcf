<?php
namespace api\modules\v1\controllers\adv;

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
        $shareKey = $this->getQueryParam('wx_share_key');
        if (empty($shareKey)) {
            throw $this->exBadParam('wx_share_key');
        }
        $share = Share::find()->where(['shareKey' => $shareKey])->one();
        if (empty($share)) {
            throw $this->ex404();
        }
        return [
            'key' => $share->shareKey,
            'title' => $share->title,
            'description' => $share->description,
            'img' => $share->imgUrl,
        ];
    }
}