<?php

namespace app\modules\adminuser\controllers;

use Yii;
use yii\data\Pagination;
use common\models\adminuser\Test;

class TestController extends \backend\controllers\BaseController {

    public function init() {
        parent::init();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 商品展示页
     */
    public function actionList() {
        $data = Test::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('list', [
                    'model' => $model,
                    'pages' => $pages,
        ]);
    }

    /**
     * 显示添加数据的表单和修改的表单
     */
    public function actionEdit($id = null) {
        $model = !empty($id) ? Test::findOne($id) : new Test();

        // 设置status的默认值，若是修改（有id传来），就使用$model->status中的值;若是显示添加表单，就手动设置1；
        $model->status = !empty($id) ? $model->status : 1;

        //接受表单的数据，验证，并添加到数据库中
        if ($model->load(Yii::$app->request->post()) )
        {
            $model->validate();
            $model->getErrors();//这样可以打印出有些时候被屏蔽的错误信息
            $model->save();
            $this->alert=0;
            $this->msg='修改成功';
            $this->time=2;
            $this->toUrl='list';
        }

        return $this->render('edit', ['model' => $model]);
    }
}
