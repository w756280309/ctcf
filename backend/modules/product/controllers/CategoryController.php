<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-2-3
 * Time: 下午5:14
 */

namespace backend\modules\product\controllers;

use Yii;
use common\models\product\ProductCategory;
use \common\models\product\OfflineProduct;
use backend\controllers\BaseController;

class CategoryController extends BaseController   //该文件暂时没有地方在使用,先不改动
{
    public $layout = 'main';

    public function actionIndex()
    {
        //$data = ProductCategory::find();
       // $pages = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '100']);
        //$model = $data->all();
        //$model = ProductCategory::treeCategory();
        //echo "<pre>";
        //print_r(ProductCategory::treeCategory());exit;
        $model = ProductCategory::getCategoryTree();
        //var_dump($model);exit;
        return $this->render('index',[
             'model' => $model
       ]);
    }



    public function actionAdd($id = null) {
        $totalCategories = [];
        $_rawCategories = ProductCategory::getCategoryTree();
        foreach($_rawCategories as $key=>$c){
            $totalCategories[$key] = $c['name'];
        }
        $model = $id ? ProductCategory::findOne($id) : new ProductCategory();

        if ($model->load(Yii::$app->request->post()))
        {
            if (Yii::$app->request->isAjax)
            {
                return ActiveForm::validate($model);
            }
            $all = Yii::$app->request->post();
            $now = time();
            $model->id = $all['ProductCategory']['id'];
            $model->name = $all['ProductCategory']['name'];
            $model->sort = $all['ProductCategory']['sort'];
            $model->parent_id = $all['ProductCategory']['parent_id'];
            $model->status = $all['ProductCategory']['status'];
            $model->home_status = $all['ProductCategory']['home_status'];
            $model->updated_at = $now;
            if($all['ProductCategory']['id']){
                $model->update();
            }else{
                $model->created_at = $now;
                $model->save();
            }
            return $this->redirect(['index']);
        }
        else{
            $model->loadDefaultValues();
        }
        return $this->render('edit', ['model' => $model, 'categories'=>$totalCategories]);
    }

    public function actionDelete($id = null) {
        $count = OfflineProduct::find()->andWhere(['category_id'=>$id,'del_status'=>  OfflineProduct::DEL_STATUS_SHOW])->count();
        if($count){
            echo "<script>alert('分类下还有产品，勿删除。请注意操作');location.href='/product/category/index';</script>";exit;
        }

        $cat_count = ProductCategory::find()->andWhere(['parent_id'=>$id,'del_status'=>  0])->count();
        if($cat_count){
            echo "<script>alert('分类下还子分类。请注意操作');location.href='/product/category/index';</script>";exit;
        }

        $model = new ProductCategory();
        $model->deleteAll('id='.$id);
        return $this->redirect(['index']);
    }



}