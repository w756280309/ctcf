<?php

namespace backend\modules\system\controllers;

use Yii;
use yii\data\Pagination;
use common\models\adminuser\Auth;

class AuthController extends \backend\controllers\BaseController
{
    /**
     * 权限列表.
     *
     * @return type
     */
    public function actionList()
    {
        $data = Auth::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '15']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('order_code asc')->asArray()->all();

        return $this->render('list', ['model' => $model, 'pages' => $pages]);
    }

    /**
     * 编辑新增.
     *
     * @param type $id
     *
     * @return type
     */
    public function actionEdit($id = null)
    {
        $model = $id ? Auth::findOne($id) : new Auth();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $this->alert = 1;
            $this->toUrl = 'list';

            return $this->render('edit', ['model' => $model]);
        }

        $id ? $model->status : $model->status = 1;

        return $this->render('edit', ['model' => $model]);
    }

    public function actionActivedo($op = null, $id = null, $value = null)
    {
        $res = 0;
        if (!empty($id) && $op == 'status') {
            //项目状态
            $_model = Auth::findOne($id);
            if (null !== $_model) {
                if ($value == Auth::STATUS_HIDDEN) {
                    $_model->status = Auth::STATUS_SHOW;
                } elseif ($value == Auth::STATUS_SHOW) {
                    $_model->status = Auth::STATUS_HIDDEN;
                }
                $res = $_model->save();
            }
        }

        echo json_encode(array('res' => $res));
    }
}