<?php

namespace backend\modules\order\controllers;

use Yii;
use common\models\user\User;
use yii\data\Pagination;
use common\models\product\OnlineProduct;
use backend\controllers\BaseController;
use common\models\order\OnlineFangkuan;
use common\models\adminuser\Admin;
use backend\modules\order\service\FkService;
use backend\modules\order\core\FkCore;
use yii\web\Response;

/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class OnlinefangkuanController extends BaseController
{
    public function actionList($uid = 1, $status = null, $time = null)
    {
        //联表查出对应的放款用户名username
        $adminInfo = Admin::find($uid)->select('username')->where("id=$uid")->asArray()->one();
        //联表查出对应的借款用户的username
        $jiekuanInfo = User::find()->select('username')->where("id=$uid")->asArray()->one();
        //搜索数据
       if ($status !== '' && !empty($time)) {
           $time = strtotime($time);
           $query = "status='$status' and created_at<=$time and uid=$uid";
       } elseif (isset($status) && $status !== '' && empty($time)) {
           $query = "status='$status' and uid=$uid";
       } elseif ($status === '' && !empty($time)) {
           $time = strtotime($time);
           $query = "created_at<=$time and uid=$uid";
       } else {
           $query = "uid=$uid";
       }

        $data = OnlineFangkuan::find()->where($query);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render('list', [
            'userUsername' => $jiekuanInfo['username'],
            'adminUsername' => $adminInfo['username'],
            'uid' => $uid,
            'model' => $model,
            'pages' => $pages,
        ]);
    }

    /**
     * 放款审核界面.
     *
     * @param type $pid
     */
    public function actionExaminfk($pid = null)
    {
        $this->layout = false;
        $deal = OnlineProduct::findOne($pid);
        $financing_user = User::findOne(['type' => 2, 'id' => $deal->borrow_uid]);

        return $this->render('examinfk', ['deal' => $deal, 'borrow_user' => $financing_user]);
    }

    public function actionCheckfk()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        bcscale(14);
        $fkservice = new FkService();
        $pid = Yii::$app->request->post('pid');
        $status = Yii::$app->request->post('status');
        $fs = $fkservice->examinFk($pid, Yii::$app->user->id);
        if ($fs !== true) {
            return $fs;
        }
        $fkcore = new FkCore();
        $ret = $fkcore->createFk(Yii::$app->user->id, $pid, $status);

        return $ret;
    }
}
