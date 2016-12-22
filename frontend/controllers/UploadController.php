<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;
use common\models\Upload;

class UploadController extends Controller
{
    use HelpersTrait;

    /**
     * 纯图片活动页面.
     */
    public function actionShowpic($id)
    {
        $this->layout = '@frontend/views/layouts/fe';

        if (empty($id) || is_int($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(Upload::class, ['isDeleted' => 0, 'id' => $id, 'allowHtml' => 1]);

        return $this->render('showpic', [
            'model' => $model,
        ]);
    }
}