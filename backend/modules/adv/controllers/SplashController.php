<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adv\Splash;
use common\models\media\Media;
use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;

class SplashController extends BaseController
{
    /**
     * 闪屏图列表.
     */
    public function actionIndex()
    {
        //页面的搜索功能
        $title = Yii::$app->request->get('title');
        $splashInfo = Splash::find();
        if (!empty($title)) {
            $splashInfo->andFilterWhere(["like", "title", $title]);
        }
        $pages = new Pagination([
            'totalCount' => $splashInfo->count(),
            'pageSize' => 10,
        ]);
        $model = $splashInfo
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['publishTime' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'model' => $model,
            'pages' => $pages,
        ]);
    }
    /**
     * 闪屏图添加/编辑.
     */
    public function actionEdit($id = null)
    {
        $model = $id ? $this->findOr404(Splash::className(), $id) : Splash::initNew($this->admin_id);
        $assignedImage = [];
        //编辑状态时获取以上传图片的meidia_id,避免load时清空
        if ($id) {
           $imageNames = Splash::getSplashImageName();
           foreach ($imageNames as $imageName) {
               if (!is_null($model[$imageName])) {
                    $assignedImage[$imageName] = $model[$imageName];
               }
           }
        }
        $images = Splash::getSplashImages();
        if (
            $model->load(Yii::$app->request->post())
            && $model->validate()
            && $this->splash($model, $assignedImage)
        ) {
            $model->save(false);
            $this->alert = 1;
            $this->toUrl = 'index';
        }

        return $this->render('edit', [
            'model' => $model,
            'images' => $images,
        ]);
    }
    /**
     * 添加或修改上传的图片地址到数据库
     */
    private function splash(Splash $splash, $assignedImage)
    {
        foreach ($assignedImage as $k => $v) {
            $splash->$k = $v;
        }
        $mediaImg = $this->uploadImage($splash);
        if (!$splash->id && empty($mediaImg)) {
                $splash->addError('img640x960', '至少上传一张图片');
        }
        if ($splash->hasErrors()) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($mediaImg as $key => $value) {
                if ($value) {
                    $value->save(false);
                    $splash->$key = $value->id;
                }
            }
            if ($splash->id) {
                $splash->updateTime = time();
                $splash->sn = Splash::create_code();
            }

            $splash->save(false);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        return true;
    }
    //闪屏图自动发布/取消自动发布
    public function actionPublish($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }
        $model = $this->findOr404(Splash::class, $id);
        if ($model->auto_publish == Splash::AUTO_PUBLISH_ON) {
            $model->auto_publish = Splash::AUTO_PUBLISH_OFF;
        } else {
            $model->auto_publish = Splash::AUTO_PUBLISH_ON;
        }

        $model->save(false);
        $this->redirect('index');
    }
    /**
     * 本地上传图片.
     */
    private function uploadImage(Splash $splash)
    {
        $media = [];
        $imageNames = Splash::getSplashImageName();
        foreach ($imageNames as $imageName) {
            $uploadFile = UploadedFile::getInstance($splash, $imageName);
            if (empty($uploadFile)) {
                continue;
            }
            $path = Yii::getAlias('@backend') . '/web/';
            $subPath = 'upload/splash/' . date('ymd', time()) . '/';
            $path .= $subPath;
            if (!file_exists($path)) {
                mkdir($path);
            }
            $picPath = $subPath . $imageName . '_' . time() . '.' . $uploadFile->extension;
            $uploadFile->saveAs($picPath);
            $media[$imageName] = Media::initNew($uploadFile->type, $picPath);
        }

        return $media;
    }
}
