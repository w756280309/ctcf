<?php

namespace backend\modules\system\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use common\models\adminuser\Role;
use common\models\adminuser\Auth;
use common\models\adminuser\RoleAuth;

class RoleController extends \backend\controllers\BaseController {

    public function actionList() {
        $data = Role::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '8']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id asc')->asArray()->all();
        return $this->render('list', ['model' => $model, 'pages' => $pages]);
    }

    public function actionEdit($id = NULL) {
        $model = $id ? Role::findOne($id) : new Role();

        $power = Auth::find()->asArray()->all();
        $raval = "";
        if ($id) {
            $role_auths = RoleAuth::find()->where(['role_sn' => $model->sn])->select('role_sn,auth_sn,auth_name')->asArray()->all();
            foreach ($power as $key => $val) {
                foreach ($role_auths as $k => $v) {
                    if ($val['sn'] == $v['auth_sn']) {
                        $raval.=$v['auth_sn'] . '-' . $v['auth_name'] . ',';
                        $power[$key]['checked'] = 1;
                        break;
                    } else {
                        $power[$key]['checked'] = 0;
                    }
                }
            }
        }
        //var_dump($power);exit;
        $model->scenario = 'edit';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $auths = $model->auths;
            $first_explode = explode(',', $auths);
            $ra = new RoleAuth();
            if ($id) {
                RoleAuth::deleteAll(['role_sn' => $model->sn]);
            }
            foreach ($first_explode as $val) {
                $sec_explode = explode('-', $val);
                $_model = clone $ra;
                $_model->role_sn = $model->sn;
                $_model->auth_sn = $sec_explode[0];
                $_model->auth_name = $sec_explode[1];
                $_model->status = 1;
                $_model->setAttributes($_model);
                $_model->save();
            }
            $this->alert = 1;
            $this->toUrl = 'list';
            return $this->render('edit', ['model' => $model, 'power' => $power, 'raval' => $raval]);
        }
        return $this->render('edit', ['model' => $model, 'power' => $power, 'raval' => $raval]);
    }

    public function actionActivedo($op = null, $id = null, $value = null) {
        $res = 0;
        if ($op == 'status') {//项目状态
            $_model = Role::findOne($id);
            $_model->scenario = 'line';
            if ($value == Role::STATUS_HIDDEN) {
                $_model->status = Role::STATUS_SHOW;
            } else if ($value == Role::STATUS_SHOW) {
                $_model->status = Role::STATUS_HIDDEN;
            }
            $res = $_model->save();
        } else {
            
        }
        echo json_encode(array('res' => $res));
    }
    
    /**
     * 权限列表
     */
    public function actionAuthlist($id=null){
        $this->layout=false;
        $model = $id ? Role::findOne($id) : new Role();

        $power = Auth::find()->asArray()->all();
        $raval = "";
        if ($id) {
            $role_auths = RoleAuth::find()->where(['role_sn' => $model->sn])->select('role_sn,auth_sn,auth_name')->asArray()->all();
            foreach ($power as $key => $val) {
                foreach ($role_auths as $k => $v) {
                    if ($val['sn'] == $v['auth_sn']) {
                        $raval.=$v['auth_sn'] . '-' . $v['auth_name'] . ',';
                        $power[$key]['checked'] = 1;
                        break;
                    } else {
                        $power[$key]['checked'] = 0;
                    }
                }
            }
        }
        return $this->render('authlist', [ 'power' => $power, 'raval' => $raval]);
    }
    

}
