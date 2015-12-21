<?php

namespace backend\modules\adv\controllers;

use Yii;
use common\models\adv\Adv;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use backend\controllers\BaseController;
use yii\web\Response;

/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class AdvController extends BaseController
{
     public function init() {
        parent::init();
        if (Yii::$app->request->isAjax)
            Yii::$app->response->format = Response::FORMAT_JSON;
    }
    
    public function actionIndex()
    {
        //页面的搜索功能
        $status = Yii::$app->request->get('status');
        $title = Yii::$app->request->get('title');
        
        $advInfo = Adv::find()->select('id,sn,title,status,show_order')->andWhere(['del_status'=>  Adv::DEL_STATUS_SHOW]);
        if(!empty($title)) {
            $advInfo->andFilterWhere(['like',title,$title]);
        }
        if($status == 0 || $status == 1) {
            $advInfo->andFilterWhere(['status' => $status]);
        }
        $data = $advInfo->orderBy('id desc');
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('sn desc,status asc')->all();
        return $this->render('index',['model'=>$model, 'pages' => $pages]);
    }
    
    public function actionEdit($id = null){
        
        $model = $id ? Adv::findOne($id) : new Adv();
        
       
        if($id){
            $model->scenario = 'update';
        }else{    
            $model->scenario = 'create';
            $model->creator_id = Yii::$app->user->id;
            $model->sn = $createUsercode = Adv::create_code();
        }
        if ($model->load(Yii::$app->request->post())&& $model->validate())
        { 
            $model->save();
            $this->alert = 1;
            $this->toUrl = 'index';
        }
        return $this->render('edit',['model'=>$model]);
    }

    /**
     * Deletes an existing OfflineOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $model->del_status = Adv::DEL_STATUS_DEL;
        $result =  $model->save();
        return $this->redirect('index');
    }

    /**
     * Finds the OfflineOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return OfflineOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Adv::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    public function actionLineon(){
        if(!Yii::$app->request->isPost){
            return [
                'result'=>0,
                'message'=>'非法请求'
            ];
        }
        $ids = Yii::$app->request->post('ids');
        Adv::updateAll(['status'=>1], ['in', 'id', explode(',', $ids)]);
        return ['result'=>1, 'message'=>'操作成功'];
    }
 
    public function actionLineoff(){
        if(!Yii::$app->request->isPost){
            return [
                'result'=>0,
                'message'=>'非法请求'
            ];
        }
        $ids = Yii::$app->request->post('ids');
        Adv::updateAll(['status'=>0], ['in', 'id', explode(',', $ids)]);
        return ['result'=>1, 'message'=>'操作成功'];
    }
    
    
    public function actionMoreop($op=null,$id=null,$value=null) {
        $res = 0;
        if($op=='status'){//项目状态
            $_model = $this->findModel($id);
            if($value==  Adv::STATUS_HIDDEN){
                $_model->status = Adv::STATUS_SHOW;
            }else if($value==Adv::STATUS_SHOW){
                $_model->status = Adv::STATUS_HIDDEN;
            }
            $_model->scenario = 'update';
            $res = $_model->save();
        }else{
            
        }
        echo json_encode(array('res'=>$res));
    }
    
    //点击edit视图页面中的新添加的图片后，显示是否删除，删除需要删除掉服务器上刚上传的图片
     public function actionImgdel($id = null, $img = null) {
        if ($id) {
            Adv::deleteAll(['id' => $id]);
        }
        $dr = $_SERVER['DOCUMENT_ROOT'];
        
        $f = $dr.'/upload/adv/'.$img;
        if(file_exists($f)){
            unlink($f);
        }
        echo json_encode(1);
        exit;
    }
    
    
}
