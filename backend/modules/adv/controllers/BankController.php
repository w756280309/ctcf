<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2016/4/8
 * Time: 17:34
 */

namespace backend\modules\adv\controllers;


use backend\controllers\BaseController;
use common\models\bank\Bank;
use Yii;
use yii\data\Pagination;
use yii\web\Response;

class BankController extends BaseController
{

    /**
     * 银行信息列表
     * @return string
     */
    public function actionIndex()
    {
        $query = Bank::find();
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 10]);
        $lists = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();
        return $this->render('index', [
            'lists' => $lists,
            'pages' => $pages,
        ]);
    }

    /**
     * 编辑银行信息
     * @param $id
     * @return array|string
     */
    public function actionEdit($id)
    {
        $this->layout = false;
        $model = Bank::find()->where(['id' => htmlspecialchars($id)])->one();
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = Yii::$app->request->post();
            $res = $model->saveBank($data);
            if (true === $res) {
                return ['code' => true, 'msg' => '更新成功'];
            } else {
                return ['code' => false, 'msg' => '更新失败'];
            }
        }
        return $this->render('edit', [
            'model' => $model,
            'id' => $id
        ]);
    }
}