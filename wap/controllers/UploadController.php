<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\product\Issuer;
use common\models\Upload;
use yii;
use yii\web\Controller;

class UploadController extends Controller
{
    use HelpersTrait;

    public function actionShowpic($id)
    {
        $this->layout = false;
        if (empty($id) || is_int($id)) {
            throw $this->ex404();
        }
        $model = Upload::find()->where(['isDeleted' => 0, 'id'=>$id, 'allowHtml'=>1])->one();
        if (null === $model) {
            throw $this->ex404();
        }

        return $this->render("@wap/views/showpic.php", ['model'=>$model]);
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
