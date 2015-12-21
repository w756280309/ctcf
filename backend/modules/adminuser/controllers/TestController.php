<?php

namespace app\modules\adminuser\controllers;

use common\models\adminuser\Test;
use Yii;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\data\Pagination;

class TestController extends \backend\controllers\BaseController {

    public function init() {
        parent::init();
        if (Yii::$app->request->isAjax)
            Yii::$app->response->format = Response::FORMAT_JSON;
    }

    //商品展示页
    public function actionList() {
        $data = Test::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('list', [
                    'model' => $model,
                    'pages' => $pages,
        ]);
    }
    //显示添加数据的表单和修改的表单
    public function actionEdit($id = null) {
        $model = $id ? Test::findOne($id) : new Test();
        
        // 设置status的默认值，若是修改（有id传来），就使用$model->status中的值;若是显示添加表单，就手动设置1；
        $id ? $model->status : $model->status = 1;
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
//            $data = Test::find();
//            $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
//            $model = $data->offset($pages->offset)->limit($pages->limit)->all();
//            return $this->render('list', [
//                    'model' => $model,
//                    'pages' => $pages,
//            ]);
//            $model->save();
//            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $model, 'roles' => $totalCategories, 'authsval' => $aus]);
    }
    
    //商品删除,还没有利用ajax实现
//    public function actionActivedo(){
//        $result = Yii::$app->request->get();
//        var_dump($result);die;
//    }
}
