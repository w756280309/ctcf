<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\media\Media;
use common\models\product\Issuer;
use Yii;
use yii\web\UploadedFile;

class ChoiceController extends BaseController
{
    /**
     *首页精选项目管理
     */
    public function actionEdit($id)
    {
        $issuer = $this->findOr404(Issuer::class, $id);
        $issuer->scenario = Issuer::SCENARIO_KUOZHAN;

        $tuPian = array();
        if ($issuer->validate() && !empty(Yii::$app->request->post()))
        {
            //将未上传图片的且原先存在图片的保存到数组中
            array_filter(['big_pic', 'mid_pic', 'small_pic'], function ($name) use ($issuer, &$tuPian) {
                if (!isset(UploadedFile::getInstance($issuer, $name)->name) && null !== $issuer->oldAttributes[$name]) {
                    $tuPian[$name] = $issuer->$name;
                }
            });
            $issuer->load(Yii::$app->request->post());
            //上传并保存
            if ($this->choice($issuer, $tuPian)) {
                $this->redirect('/product/issuer/list');
            }
        }
        return $this->render('edit', ['issuer' => $issuer]);
    }


    /**
     * 添加编辑精选项目图片.
     */
    private function choice(Issuer $issuer, $tuPian)
    {
        $picArray = array('big_pic', 'mid_pic', 'small_pic');
        $mediaImg = array();
        foreach ($picArray as $key => $value) {
            $mediaImg[$key] = $this->upload($issuer, $value);   //没上传 返回的是false
        }

        $this->uploadPic($issuer, $mediaImg, $picArray, $tuPian);

        return true;
    }

    /**
     * 本地上传图片
     */
    private function upload(Issuer $issuer, $picType)
    {
        $uploadFile = UploadedFile::getInstance($issuer, $picType);

        if (empty($uploadFile)) {
            return false;
        }

        $path = Yii::getAlias('@backend').'/web/upload/media';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $imgPath = 'upload/media/m'.time().'_'.$picType.'.'.$uploadFile->extension;
        $uploadFile->saveAs($imgPath);
        $media = Media::initNew($uploadFile->type, $imgPath);

        return $media;
    }

    /**
     * 数据库操作
     */
    private function uploadPic(Issuer $issuer, $mediaImg, $picArray, $tuPian)
    {
        if ($issuer->hasErrors()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($mediaImg as $key => $value) {
                if ($mediaImg[$key]) {
                    if (!empty($issuer->oldAttributes[$picArray[$key]])) {
                        $_img = Media::findOne($issuer->oldAttributes[$picArray[$key]]);
                        if (null !== $_img) {
                            $_img->delete();
                        }
                    }

                    $mediaImg[$key]->save(false);
                }
            }

            $issuer->isShow = $_POST['Issuer']['isShow'];
            $issuer->sort = $_POST['Issuer']['sort'];
            $issuer->path = $_POST['Issuer']['path'];
            $issuer->big_pic = !isset(UploadedFile::getInstance($issuer, 'big_pic')->name) ? $tuPian['big_pic'] : $mediaImg[0]->id;
            $issuer->mid_pic = !isset(UploadedFile::getInstance($issuer, 'mid_pic')->name) ? $tuPian['mid_pic'] : $mediaImg[1]->id;
            $issuer->small_pic = !isset(UploadedFile::getInstance($issuer, 'small_pic')->name) ? $tuPian['small_pic'] : $mediaImg[2]->id;

            $issuer->update(false);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
}