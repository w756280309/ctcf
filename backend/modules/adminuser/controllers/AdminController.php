<?php

namespace app\modules\adminuser\controllers;

use Yii;
use backend\controllers\BaseController;
use yii\web\Response;
use yii\data\Pagination;
use common\models\adminuser\AdminAuth;
use common\models\adminuser\EditpassForm;
use common\models\adminuser\Admin;
use common\models\adminuser\Auth;
use common\models\adminuser\RoleAuth;
use common\models\adminuser\Role;

class AdminController extends BaseController
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
        return $this->render('index');
    }

    public function actionList()
    {
        $data = Admin::find();
        $username = trim(Yii::$app->request->get('username'));
        $real_name = trim(Yii::$app->request->get('real_name'));
        if ($username) {
            $data->andWhere(['like', 'username', $username]);
        }
        if ($real_name) {
            $data->andWhere(['like', 'real_name', $real_name]);
        }
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('list', [
                    'model' => $model,
                    'pages' => $pages,
        ]);
    }

    public function actionEdit($id = null, $act = null)
    {
        $totalCategories = [];
        $_rawCategories = Role::find()->where(['status' => 1])->asArray()->all();
        foreach ($_rawCategories as $c) {
            $totalCategories[$c['sn']] = $c['role_name'];
        }

        $aus = '';
        $model = new Admin();
        if ($id) {
            $adminauths = AdminAuth::find()->where(['admin_id' => $id])->asArray()->all();
            foreach ($adminauths as $val) {
                $aus .= $val['auth_sn'].'-'.$val['auth_name'].',';
            }
            $model = Admin::findIdentity($id);
            if (!$model) {
                return $this->redirect('/adminuser/admin/list');
            }
        }
        $model->scenario = 'register';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auths = $model->auths;
            $first_explode = explode(',', $auths);
            $ra = new AdminAuth();
            if ($id) {
                AdminAuth::deleteAll(['admin_id' => $id]);
            }
            foreach ($first_explode as $val) {
                $sec_explode = explode('-', $val);
                $_model = clone $ra;
                $_model->admin_id = $id;
                $_model->role_sn = $model->role_sn;
                $_model->auth_sn = $sec_explode[0];
                $_model->auth_name = $sec_explode[1];
                $_model->status = 1;
                $_model->setAttributes($_model);
                $_model->save();
            }

            if ($id) {
                if ($model->user_pass) {
                    $model->setPassword($model->user_pass);
                }
                $model->save();
            } else {
                $old_model = Admin::findByUsername($model->username);
                if ($old_model) {
                    $model->addError('username', '用户已经存在！请重新确认您的身份信息是否正确。');
                } else {
                    $model->setPassword($model->user_pass);
                    $model->save();
                }
            }
            $this->alert = 1;
            $this->toUrl = 'list';

            return $this->render('edit', ['model' => $model, 'roles' => $totalCategories, 'authsval' => $aus]);
        }

        return $this->render('edit', ['model' => $model, 'roles' => $totalCategories, 'authsval' => $aus]);
    }

    public function actionRoles($aid = null, $rid = null)
    {
        if (empty($aid) && empty($rid)) {
            return ['res' => 0, 'msg' => '数据读取错误'];
        }
        $auth = Auth::find()->asArray()->select('sn,psn,auth_name')->all();
        if (empty($aid)) {
            $role = RoleAuth::find()->where(['role_sn' => $rid])->select('auth_sn')->asArray()->all();
            foreach ($auth as $key => $val) {
                foreach ($role as $k => $v) {
                    if ($val['sn'] == $v['auth_sn']) {
                        $auth[$key]['checked'] = 1;
                        break;
                    } else {
                        $auth[$key]['checked'] = 0;
                    }
                }
            }
        } else {
            $adminauth = AdminAuth::find()->where(['admin_id' => $aid])->select('auth_sn')->asArray()->all();
            foreach ($auth as $key => $val) {
                foreach ($adminauth as $k => $v) {
                    if ($val['sn'] == $v['auth_sn']) {
                        $auth[$key]['checked'] = 1;
                        break;
                    } else {
                        $auth[$key]['checked'] = 0;
                    }
                }
            }
        }

        return $auth;
    }

    /**
     * 权限列表.
     */
    public function actionAuthlist($id = null)
    {
        $this->layout = false;
        $model = $id ? Admin::findOne($id) : new Admin();

        return $this->render('authlist', ['model' => $model]);
    }

    /**
     * 根据后台超级管理员点击的ajax请求改变被点击用户的状态是可用还是禁用
     * 执行效果时，点击“可用”变为“禁用”，点击“禁用”变为“可用”。
     */
    public function actionActivedo($op = null, $id = null, $value = null)
    {
        $res = 0;
        if ($op == 'status' && !empty($id)) {
            //项目状态
            $_model = Admin::findOne($id);
            if (null !== $_model) {
                //这儿会用到场景
                $_model->scenario = 'active';
                if ($value == Admin::STATUS_DELETED) {
                    $_model->status = Admin::STATUS_ACTIVE;
                } elseif ($value == Admin::STATUS_ACTIVE) {
                    $_model->status = Admin::STATUS_DELETED;
                }
                $res = $_model->save();
            }
        }

        echo json_encode(array('res' => $res));
    }

    public function actionEditpass()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = false;
        $model = new EditpassForm();
        if ($model->load(Yii::$app->request->post()) && $model->editpass()) {
            $this->alert = 1;
            $this->toUrl = '/adminuser/admin/editpass?flag=1';
        }

        return $this->render('editpass', ['model' => $model]);
    }
}
