<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\product\JxPage;
use common\models\product\Issuer;
use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;

class JingXuanController extends BaseController
{
    /**
     * 精选项目介绍页列表
     */
    public function actionList()
    {
        $query = JxPage::find();
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $jxPage = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['createTime' => SORT_DESC])
            ->all();

        return $this->render('list', ['jxPage' => $jxPage, 'pages' => $pages]);
    }

    /**
     * 添加精选项目介绍页
     */
    public function actionAdd()
    {
        $issuerIds = JxPage::find()
            ->select('issuerId')
            ->column();
        $issuers = Issuer::find()
            ->where(['not in', 'id', $issuerIds])
            ->all();
        $model = new JxPage();
        $jxPagePost = Yii::$app->request->post();
        if (!empty($jxPagePost)) {
            //针对于顶部图片的处理
            $jxPagePost['JxPage']['content']['pic'] = null;
            if (isset(UploadedFile::getInstance($model, 'pic')->name)) {
                $uploadedFile = UploadedFile::getInstance($model, 'pic');
                $uploadPath = $this->upload($uploadedFile);
                if (false !== $uploadPath) {
                    $jxPagePost['JxPage']['content']['pic'] = $uploadPath;
                }
            }
            $jxPagePost['JxPage']['content'] = serialize($jxPagePost['JxPage']['content']);
            if ($model->load($jxPagePost) && $model->validate()) {
                $model->createTime = date('Y-m-d H:i:s');
                $model->admin_id = Yii::$app->user->id;
                if ($model->save()) {
                    return $this->redirect('list');
                }
            }
        }

        return $this->render('edit', [
            'issuers' => $issuers,
            'model' => $model,
        ]);
    }

    /**
     * 编辑精选项目介绍页
     *
     * @param int    $id 精选项目介绍页列表主键
     *
     * return string 精选项目介绍页编辑页面
     */
    public function actionEdit($id)
    {
        $model = $this->findOr404(JxPage::class, $id);
        $content = unserialize($model->content);
        $jxPagePost = Yii::$app->request->post();
        if (!empty($jxPagePost)) {
            //针对于顶部图片的处理
            $jxPagePost['JxPage']['content']['pic'] = null;
            if (isset(UploadedFile::getInstance($model, 'pic')->name)) {
                $uploadedFile = UploadedFile::getInstance($model, 'pic');
                $uploadPath = $this->upload($uploadedFile);
                if (false !== $uploadPath) {
                    $jxPagePost['JxPage']['content']['pic'] = $uploadPath;
                }
            } else {
                $jxPagePost['JxPage']['content']['pic'] = $content['pic'];
            }
            $jxPagePost['JxPage']['content'] = serialize($jxPagePost['JxPage']['content']);
            if ($model->load($jxPagePost) && $model->validate()) {
                if ($model->save()) {
                    return $this->redirect('list');
                }
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'content' => $content,
        ]);
    }

    /**
     * 上传
     * @param UploadedFile $uploadedFile
     *
     * @return bool/string
     */
    private function upload(UploadedFile $uploadedFile)
    {
        if (empty($uploadedFile)) {
            return false;
        }
        $path = Yii::getAlias('@backend').'/web/upload/media';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $imgPath = 'upload/media/issuer'. time() . rand(100000, 999999). '.' .$uploadedFile->extension;
        if ($uploadedFile->saveAs($imgPath)) {
            return $imgPath;
        }

        return false;
    }
}
