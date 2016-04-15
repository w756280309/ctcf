<?php

namespace backend\modules\adv\controllers;

use Yii;
use common\models\adv\AdvPos;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\adv\Adv;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class PosController extends BaseController
{
    public $layout = 'main';

    /**
     * Lists all OfflineOrder models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = AdvPos::find()->andWhere(['del_status' => AdvPos::DEL_STATUS_SHOW])->all();

        return $this->render('index', ['model' => $model]);
    }

    public function actionEdit($id = null)
    {
        $model = $id ? AdvPos::findOne($id) : new AdvPos();

        if ($id) {
            $model->scenario = 'update';
        } else {
            $model->scenario = 'create';
            $model->creator_id = Yii::$app->user->id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($id)) {
                $pos = AdvPos::findOne(['del_status' => AdvPos::DEL_STATUS_SHOW, 'code' => $model->code]);
                if ($pos) {
                    $model->addError('code', '位置编码已存在！请重新确认。');
                }
            }
            $model->save();

            return $this->redirect(['index']);
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * Deletes an existing OfflineOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (empty($id)) {
            throw new NotFoundHttpException();  //当参数异常时,抛出404错误
        }
        $count = Adv::find()->andWhere(['pos_id' => $id, 'del_status' => AdvPos::DEL_STATUS_SHOW])->count();
        if ($count) {
            echo "<script>alert('广告位下还有其它广告。请注意操作');location.href='/adv/pos';</script>";
            exit;
        }
        $model = $this->findModel($id);
        $model->del_status = AdvPos::DEL_STATUS_DEL;
        $model->scenario = 'update';
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionMoreop($op = null, $id = null, $value = null)
    {
        $res = 0;
        if (!empty($id) && $op == 'status') {
            //项目状态
            $_model = $this->findModel($id);
            if ($value ==  AdvPos::STATUS_HIDDEN) {
                $_model->status = AdvPos::STATUS_SHOW;
            } elseif ($value == AdvPos::STATUS_SHOW) {
                $_model->status = AdvPos::STATUS_HIDDEN;
            }
            $_model->scenario = 'update';
            $res = $_model->save();
        }

        echo json_encode(array('res' => $res));
    }

    /**
     * Finds the OfflineOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return OfflineOrder the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (!empty($id) && ($model = AdvPos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
