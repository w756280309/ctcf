<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\product\Issuer;
use Yii;
use yii\data\Pagination;

class IssuerController extends BaseController
{
    /**
     * 发行方列表页.
     *
     * 1.一页显示15条记录;
     */
    public function actionList()
    {
        $this->layout = '@backend/modules/coupon/views/layouts/frame';

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
        $this->layout = false;
        $issuer = new Issuer();

        $res = false;
        if ($issuer->load(Yii::$app->request->post()) && $issuer->validate()) {
            $res = $issuer->save(false);
        }

        return $this->render('edit', ['issuer' => $issuer, 'res' => $res]);
    }

    /**
     * 编辑发行方.
     */
    public function actionEdit($id)
    {
        $this->layout = false;
        $issuer = $this->findOr404(Issuer::class, $id);

        $res = false;
        if ($issuer->load(Yii::$app->request->post()) && $issuer->validate()) {
            $res = $issuer->save(false);
        }

        return $this->render('edit', ['issuer' => $issuer, 'res' => $res]);
    }

    /**
     * 添加发行方视频.
     */
    public function actionMediaEdit($id)
    {
        $this->layout = false;
        $issuer = $this->findOr404(Issuer::class, $id);

        $res = false;
        if ($issuer->load(Yii::$app->request->post()) && $issuer->validate()) {
            $titleIsEmpty = empty($issuer->mediaTitle);
            $uriIsEmpty = empty($issuer->mediaUri);

            if ($titleIsEmpty && !$uriIsEmpty) {
                $issuer->addError('mediaTitle', '视频名称不能为空');
            } elseif ($uriIsEmpty && !$titleIsEmpty) {
                $issuer->addError('mediaUri', '视频地址不能为空');
            } else {
                $res = $issuer->save(false);
            }
        }

        return $this->render('media_edit', ['issuer' => $issuer, 'res' => $res]);
    }
}