<?php
namespace frontend\models;

use Yii;
use common\models\news\NewsCategory;
use common\models\news\News;
use common\models\user\UserType;

/**
 * Description of ProductCategoryData
 *
 * @author zhy-pc
 */
class NewsCategoryData {
    
    const NEWS_CAT_MEMBER_JIAOYI=7;
    const NEWS_CAT_MEMBER_JINGJI=8;
    const NEWS_CAT_MEMBER_SERVICE=9;
    const NEWS_CAT_MEMBER_CHENGXIAO=10;
    
    const NEWS_CAT_MEMBER_COMING_IN=11;
    const NEWS_CAT_MEMBER_ZHANGCHENG=12;
    
    public function category($conditon){
        $data = NewsCategory::getCategoryTree($conditon);
        return $data;
    }
    
    public function getSubCat($conditon){
        return NewsCategory::findAll($conditon);
    }
    
    public function getNewsCat($cond){
        return NewsCategory::findOne($cond);
    }
    
    public static function getNews($cond){
        return News::findOne($cond);
    }
    
    public static function getMemberNews($cid=null,$order = 'news_time desc'){
        $model = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>$cid])->orderBy($order)->all();
        return $model;
    }
    
    public static function getMemberType(){
        $model = UserType::find()->andWhere(['status'=> UserType::STATUS_SHOW])->limit(4)->all();
        return $model;
    }    
}
