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
            $jxPagePost = $this->dealPic($model, $jxPagePost);
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
            $jxPagePost = $this->dealPic($model, $jxPagePost);
            if (!$model->hasErrors()) {
                if ($model->load($jxPagePost) && $model->validate()) {
                    if ($model->save()) {
                        return $this->redirect('list');
                    }
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

    /**
     * 处理页面顶部图片逻辑
     */
    private function dealPic($model, $jxPagePost)
    {
        $jxPagePost['JxPage']['content']['pic'] = null;
        $isEdit = null !== $model->id;
        if ($isEdit) {
            $content = unserialize($model->content);
            $jxPagePost['JxPage']['content']['pic'] = $content['pic'];
        }

        if (isset(UploadedFile::getInstance($model, 'pic')->name)) {
            $uploadedFile = UploadedFile::getInstance($model, 'pic');
            $imageInfo = getimagesize($uploadedFile->tempName);
            $validate = true;
            if (!in_array($imageInfo['mime'], ['image/png', 'image/jpeg'])) {
                $model->addError('content', '图片的格式应为jpg，png');
                $validate = false;
            }
            if (750 !== $imageInfo[0]) {
                $model->addError('content', '上传图片的宽度应为750px');
                $validate = false;
            }
            if ($validate) {
                $uploadPath = $this->upload($uploadedFile);
                if (false === $uploadPath) {
                    $model->addError('content', '图片上传失败');
                }

                $jxPagePost['JxPage']['content']['pic'] = $uploadPath;
            }
        }
        $jxPagePost['JxPage']['content'] = serialize($jxPagePost['JxPage']['content']);

        return $jxPagePost;
    }
}
