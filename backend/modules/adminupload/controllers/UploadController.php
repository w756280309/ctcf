<?php

namespace backend\modules\adminupload\controllers;

use Yii;
use backend\controllers\BaseController;
use common\models\Upload;
use yii\web\UploadedFile;
use yii\data\Pagination;

class UploadController extends BaseController
{
    private $extensions = ['jpg', 'png'];

    public function actionIndex()
    {
        $title = Yii::$app->request->get('title');
        $extension = Yii::$app->request->get('extension');

        $uploadInfo = Upload::find()->where(['isDeleted' => 0]);
        if (!empty($title)) {
            $uploadInfo->andFilterWhere(['like', 'title', $title]);
        }
        if (!empty($extension) && in_array($extension, $this->extensions)) {
            $uploadInfo->andFilterWhere(['like', 'link', $extension]);
        }

        $pages = new Pagination(['totalCount' => $uploadInfo->count(), 'pageSize' => '10']);
        $model = $uploadInfo->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render("index", ['model' => $model, 'pages' => $pages, 'title' => $title, 'extension'=>$this->extensions]);
    }

    public function actionEdit($id = null)
    {
        if (!empty($id)) {
            $model = Upload::findOne($id);
            if (null === $model) {
                throw $this->ex404();
            }
        } else {
            $model = new Upload();
            //去除首次显示错误
            if (!empty(Yii::$app->request->post())) {
                if (!isset($_FILES['upload']['tmp_name']['link']) || '' === $_FILES['upload']['tmp_name']['link']) {
                    $model->addError("link", "图片不能为空");
                }
            }
        }

        if (!$model->hasErrors()) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $this->uploadImage($model);
                if (null === $model->link) {
                    unset($model->link);
                }
                if ($model->save(false)) {
                    return $this->redirect("index");
                }
            }
        }
        return $this->render("edit", ['model'=>$model]);
    }

    public function actionDelete($id)
    {
        $model = Upload::findOne($id);
        if (null === $model || empty($id)) {
            throw $this->ex404();
        }

        $model->isDeleted = 1;
        $model->save();
        return $this->redirect('index');
    }

    /**
     * 检查上传图片
     */
    private function uploadImage(Upload $obj)
    {
        $obj->link = UploadedFile::getInstance($obj, 'link');

        $path = Yii::getAlias('@backend').'/web/upload/link';
        if (!file_exists($path)) {
            mkdir($path);
        }

        if ($obj->link) {
            $picPath = 'upload/link/link'.time().rand(100000, 999999).'.'.$obj->link->extension;

            $obj->link->saveAs($picPath);
            $obj->link = $picPath;
        }
    }
}


