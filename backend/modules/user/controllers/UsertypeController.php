<?php

namespace backend\modules\user\controllers;

use common\models\user\UserType;
use backend\controllers\BaseController;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * UserController implements the CRUD actions for User model.
 */
class UsertypeController extends \backend\controllers\BaseController
{

    /**
     * create a new user type
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex(){
        $data = UserType::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id asc')->all();
        return $this->render('index',['model'=>$model, 'pages' => $pages]);
    }

    /**
     * create a new user type
     * @return mixed
     */     
    public function actionEdit($id = null){
        $model = $id ? UserType::findOne($id) : new UserType();
        
        if($id){
            $model->scenario = 'update';
        }else{
            $model->scenario = 'create';
            $model->creator_id = Yii::$app->user->id;
            $model->status = 1;
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
//            return json_encode($model->save());
            $model->save();
            return $this->redirect(['index']);
        }
        
        return $this->render('edit',['model'=>$model]);
    }
    public function actionMoreop($op=null,$id=null,$value=null) {
        $res = 0;
        if($op=='status'){//项目状态
            $_model = UserType::findOne($id);
            if($value==  UserType::STATUS_HIDDEN){
                $_model->status = UserType::STATUS_SHOW;
            }else if($value==UserType::STATUS_SHOW){
                $_model->status = UserType::STATUS_HIDDEN;
            }
            $_model->scenario = 'update';
            $res = $_model->save();
        }else{

        }
        echo json_encode(array('res'=>$res));
    }

}
