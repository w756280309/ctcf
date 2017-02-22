<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adv\Adv;
use common\models\adv\Share;
use common\models\media\Media;
use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;

class AdvController extends BaseController
{
    /**
     * 轮播图列表.
     */
    public function actionIndex()
    {
        //页面的搜索功能
        $status = Yii::$app->request->get('status');
        $title = Yii::$app->request->get('title');
        $ad = Adv::tableName();

        $advInfo = Adv::find()
            ->joinWith('share')
            ->where(['type' => Adv::TYPE_LUNBO])
            ->andWhere(['del_status' => Adv::DEL_STATUS_SHOW]);

        if (!empty($title)) {
            $advInfo->andFilterWhere(["like", "$ad.title", $title]);
        }

        if ($status == 0 || $status == 1) {
            $advInfo->andFilterWhere(['status' => $status]);
        }

        $pages = new Pagination([
            'totalCount' => $advInfo->count(),
            'pageSize' => 10,
        ]);

        $model = $advInfo
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['sn' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'model' => $model,
            'pages' => $pages,
        ]);
    }

    /**
     * 首页轮播图的添加.
     */
    public function actionEdit($id = null)
    {
        $model = $id ? $this->findOr404(Adv::class, $id) : Adv::initNew($this->admin_id, Adv::TYPE_LUNBO);

        if (!$model->showOnPc && $model->share) {
            $model->canShare = true;
        } else {
            $model->canShare = false;
            $model->share_id = null;
        }

        $share = $model->share ?: new Share();
        $showOnPc = $model->showOnPc;

        if (
            $model->load(Yii::$app->request->post())
            && $model->validate()
            && $this->adv($model, $showOnPc)
        ) {
            if ($model->showOnPc) {
                $model->isDisabledInApp = 0;
            }

            if (
                (
                    !$model->showOnPc
                    && $model->canShare
                    && $share->load(Yii::$app->request->post())
                    && $share->validate()
                )
                || !$model->canShare
                || $model->showOnPc
            ) {
                if ($model->canShare && !$model->showOnPc) {
                    if (false === strpos($share->url, 'wx_share_key')) {      //后台自动添加分享key
                        $share->url = rtrim($share->url, '/');
                        if (false === strpos($share->url, '?')) {
                            $share->url .= '?wx_share_key='.$share->shareKey;
                        } else {
                            $share->url .= '&wx_share_key='.$share->shareKey;
                        }
                    }

                    $share->save();
                    $model->share_id = $share->id;
                } else {
                    $model->share_id = null;
                }

                $model->save(false);

                $this->alert = 1;
                $this->toUrl = 'index';
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'share' => $share,
        ]);
    }

    /**
     * 删除banner图.
     */
    public function actionDelete($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(Adv::class, $id);
        $model->del_status = Adv::DEL_STATUS_DEL;
        $model->save();

        return $this->redirect('index');
    }

    /**
     * 上线操作.
     */
    public function actionLineon()
    {
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }

        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return ['result' => 0, 'message' => '操作失败'];
        }

        Adv::updateAll(['status' => Adv::STATUS_SHOW], ['in', 'id', explode(',', $ids)]);

        return ['result' => 1, 'message' => '操作成功'];
    }

    /**
     * 下线操作.
     */
    public function actionLineoff()
    {
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }

        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return ['result' => 0, 'message' => '操作失败'];
        }

        Adv::updateAll(['status' => Adv::STATUS_HIDDEN], ['in', 'id', explode(',', $ids)]);

        return ['result' => 1, 'message' => '操作成功'];
    }

    /**
     * 开屏图列表.
     */
    public function actionKaipingList()
    {
        $query = Adv::find()
            ->where(['type' => Adv::TYPE_KAIPING])
            ->andWhere(['del_status' => Adv::DEL_STATUS_SHOW]);

        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 10,
        ]);

        $model = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy('id desc')
            ->all();

        return $this->render('kaiping-list', [
            'model' => $model,
            'pages' => $pages,
        ]);
    }

    /**
     * 开屏图添加，编辑.
     */
    public function actionKaipingEdit($id = null)
    {
        $adv = $id ? $this->findOr404(Adv::class, $id) : Adv::initNew($this->admin_id, Adv::TYPE_KAIPING);

        if ($adv->load(Yii::$app->request->post())
            && $adv->validate()
            && $this->adv($adv)
        ) {
            $this->alert = 1;
            $this->toUrl = 'kaiping-list';
        }

        return $this->render('kaiping-edit', ['adv' => $adv]);
    }

    /**
     * 记录数据库数据.
     */
    private function adv(Adv $adv, $showOnPc = null)
    {
        $mediaImg = $this->uploadImage($adv);

        if (false === $mediaImg && null !== $showOnPc && intval($adv->showOnPc) !== $showOnPc) {
            $adv->addError('imageUri', '上传图片不能为空');
        }

        if ($adv->hasErrors()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($mediaImg) {
                $mediaImg->save(false);
                $adv->media_id = $mediaImg->id;
            }

            $adv->updated_at = time();
            $adv->save(false);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }

        return true;
    }

    /**
     * 本地上传图片.
     */
    private function uploadImage(Adv $adv)
    {
        $uploadFile = UploadedFile::getInstance($adv, 'imageUri');

        if (empty($uploadFile)) {
            return false;
        }

        $path = Yii::getAlias('@backend').'/web/upload/adv';

        if (!file_exists($path)) {
            mkdir($path);
        }

        $prefix = Adv::TYPE_KAIPING === $adv->type ? 'kp' : 'ad';
        $picPath = 'upload/adv/'.$prefix.time().rand(100000, 999999).'.'.$uploadFile->extension;
        $uploadFile->saveAs($picPath);

        $media = Media::initNew($uploadFile->type, $picPath);

        return $media;
    }
}
