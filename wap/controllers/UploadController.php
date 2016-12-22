<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\product\Issuer;
use common\models\Upload;
use yii\web\Controller;

class UploadController extends Controller
{
    use HelpersTrait;

    /**
     * 纯图片活动页面.
     */
    public function actionShowpic($id, $wx_share_key = null)
    {
        $this->layout = '@app/views/layouts/fe';

        if (empty($id) || is_int($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(Upload::class, ['isDeleted' => 0, 'id' => $id, 'allowHtml' => 1]);

        $share = null;
        if (null !== $wx_share_key) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }


        return $this->render('showpic', [
            'model' => $model,
            'share' => $share,
        ]);
    }

    /**
     * 发行商视频展示.
     */
    public function actionIssuerVideo()
    {
        $issuers = Issuer::find()
            ->where(['is not', 'video_id', null])
            ->all();

        return $this->render('issuer_video', ['issuers' => $issuers]);
    }
}
