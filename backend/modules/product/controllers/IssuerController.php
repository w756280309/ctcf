<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\media\Media;
use common\models\product\Issuer;
use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;

class IssuerController extends BaseController
{
    public $layout = '@backend/modules/coupon/views/layouts/frame';

    /**
     * 发行方列表页.
     *
     * 1.一页显示15条记录;
     */
    public function actionList()
    {
        $query = Issuer::find();
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $issuers = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();

        return $this->render('list', ['issuers' => $issuers, 'pages' => $pages]);
    }

    /**
     * 添加发行方.
     */
    public function actionAdd()
    {
        $issuer = new Issuer();

        if ($issuer->load(Yii::$app->request->post())
            && $issuer->validate()
            && $this->validateIssuer($issuer)
            && $this->issuer($issuer)
        ) {
            $this->redirect('/product/issuer/list');
        }

        return $this->render('edit', ['issuer' => $issuer]);
    }

    /**
     * 编辑发行方.
     */
    public function actionEdit($id)
    {
        $issuer = $this->findOr404(Issuer::class, $id);

        if (!empty($issuer->video_id)) {
            $video = Media::findOne($issuer->video_id);

            if (null !== $video) {
                $issuer->videoUrl = $video->uri;
            }
        }

        if ($issuer->load(Yii::$app->request->post())
            && $issuer->validate()
            && $this->validateIssuer($issuer)
            && $this->issuer($issuer)
        ) {
            $this->redirect('/product/issuer/list');
        }

        if (!empty($issuer->videoCover_id)) {
            $img = Media::findOne($issuer->videoCover_id);

            if (null !== $img) {
                $issuer->imgUrl = $img->uri;
            }
        }

        return $this->render('edit', ['issuer' => $issuer]);
    }

    /**
     * 验证发行方信息.
     */
    private function validateIssuer(Issuer $issuer)
    {
        $titleIsEmpty = empty($issuer->mediaTitle);
        $videoIsEmpty = empty($issuer->videoUrl);

        if ($titleIsEmpty && !$videoIsEmpty) {
            $issuer->addError('mediaTitle', '视频名称不能为空');
        } elseif ($videoIsEmpty && !$titleIsEmpty) {
            $issuer->addError('videoUrl', '视频地址不能为空');
        }

        return $issuer;
    }

    /**
     * 添加编辑发行方.
     */
    private function issuer(Issuer $issuer)
    {
        $mediaImg = $this->upload($issuer);

        if ($issuer->hasErrors() || ($mediaImg && $mediaImg->hasErrors())) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($mediaImg) {
                $mediaImg->save(false);
                $issuer->videoCover_id = $mediaImg->id;
            }

            if (!empty($issuer->video_id)) {     //如果存在已有视频地址记录,则删除
                $_img = Media::findOne($issuer->video_id);
                if (null !== $_img) {
                    $_img->delete();
                }
            }

            if (!empty($issuer->videoUrl)) {
                $mediaVideo = Media::initNew('video/mp4', $issuer->videoUrl);
                $mediaVideo->save(false);

                $issuer->video_id = $mediaVideo->id;
            } else {
                if (empty($issuer->mediaTitle)) {
                    $issuer->video_id = null;
                    $issuer->videoCover_id = null;
                }
            }

            $issuer->save(false);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }

        return true;
    }

    /**
     * 上传视频示例图.
     */
    private function upload(Issuer $issuer)
    {
        $uploadFile = UploadedFile::getInstance($issuer, 'imgUrl');

        if (empty($uploadFile) || (empty($issuer->mediaTitle) && empty($issuer->videoUrl))) {
            return false;
        }

        $path = Yii::getAlias('@backend').'/web/upload/media';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $imgPath = 'upload/media/m'.time().'.'.$uploadFile->extension;
        $uploadFile->saveAs($imgPath);

        $media = Media::initNew($uploadFile->type, $imgPath);

        return $media;
    }
}