<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;
use common\models\Upload;

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

        return $this->render("@frontend/views/showpic.php", ['model'=>$model]);
    }
}