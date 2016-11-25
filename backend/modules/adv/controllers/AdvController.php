<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adv\Adv;
use common\models\adv\Share;
use Yii;
use yii\data\Pagination;
use yii\web\Response;

class AdvController extends BaseController
{
    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    public function actionIndex()
    {
        //页面的搜索功能
        $status = Yii::$app->request->get('status');
        $title = Yii::$app->request->get('title');
        $ad = Adv::tableName();

        $advInfo = Adv::find()
            ->joinWith('share')
            ->andWhere(['del_status' => Adv::DEL_STATUS_SHOW]);
        if (!empty($title)) {
            $advInfo->andFilterWhere(['like', title, $title]);
        }
        if ($status == 0 || $status == 1) {
            $advInfo->andFilterWhere(['status' => $status]);
        }
        $data = $advInfo->orderBy(["$ad.id" => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('sn desc,status asc')->all();

        return $this->render('index', ['model' => $model, 'pages' => $pages]);
    }

    /**
     * 首页轮播图的添加.
     */
    public function actionEdit($id = null)
    {
        $model = $id ? Adv::findOne($id) : new Adv();

        if ($id) {
            $model->scenario = 'update';
        } else {
            $model->scenario = 'create';
            $model->creator_id = $this->getAuthedUser()->id;
            $model->sn = Adv::create_code();
        }
        if (!$model->showOnPc && $model->share) {
            $model->canShare = true;
        } else {
            $model->canShare = false;
            $model->share_id = null;
        }

        $share = $model->share ?: new Share();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->showOnPc) {
                $model->isDisabledInApp = 0;
            }
            if (
            (
                ! $model->showOnPc
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
                $model->save();
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

        $model->scenario = 'update';
        $model->del_status = Adv::DEL_STATUS_DEL;
        $model->save();

        return $this->redirect('index');
    }

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

        Adv::updateAll(['status' => 1], ['in', 'id', explode(',', $ids)]);

        return ['result' => 1, 'message' => '操作成功'];
    }

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

        Adv::updateAll(['status' => 0], ['in', 'id', explode(',', $ids)]);

        return ['result' => 1, 'message' => '操作成功'];
    }

    public function actionMoreop($op = null, $id = null, $value = null)
    {
        $res = 0;
        if (!empty($id) && $op == 'status') {
            //项目状态
            $_model = $this->findOr404(Adv::class, $id);
            if (null !== $_model) {
                if ($value ==  Adv::STATUS_HIDDEN) {
                    $_model->status = Adv::STATUS_SHOW;
                } elseif ($value == Adv::STATUS_SHOW) {
                    $_model->status = Adv::STATUS_HIDDEN;
                }
                $_model->scenario = 'update';
                $res = $_model->save();
            }
        }

        echo json_encode(array('res' => $res));
    }

    /**
     * 点击edit视图页面中的新添加的图片后，显示是否删除，删除需要删除掉服务器上刚上传的图片
     */
    public function actionImgdel($id = null, $img = null)
    {
         if (!empty($id)) {
             Adv::deleteAll(['id' => $id]);
         }

         $dr = $_SERVER['DOCUMENT_ROOT'];
         $f = $dr.'/upload/adv/'.$img;
         if (file_exists($f)) {
             unlink($f);
         }

         echo json_encode(1);
         exit;
     }
}
