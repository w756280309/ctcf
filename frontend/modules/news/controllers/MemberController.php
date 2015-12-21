<?php

namespace app\modules\news\controllers;

use common\models\news\News;
use common\models\user\User;
use yii\web\Controller;

class MemberController extends Controller
{
	public $layout = 'member';

    public function actionIndex($aid = null,$list = null)
    {
        $model=array();
        if(empty($list)){
            $model = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'id'=>$aid])->orderBy('news_time desc')->one();
        }else if(is_string($list)){
            $model = User::find()->andWhere(['type'=>2,'status'=> User::STATUS_ACTIVE,'cat_id'=>$aid,"examin_status"=>  User::EXAMIN_STATUS_PASS])->orderBy('id desc')->all();
        }
        //var_dump($model);
        return $this->render('index',['model'=>$model,'list'=>$list]);
    }
}
