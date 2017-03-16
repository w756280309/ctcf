<?php
namespace backend\modules\o2o\controllers;

use backend\controllers\BaseController;
use common\models\affiliation\Affiliator;
use common\models\code\VirtualCard;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class AffiliatorController extends BaseController
{
    public function actionList()
    {
        $name = \Yii::$app->request->get('name');
        $a = Affiliator::tableName();
        $v = VirtualCard::tableName();
        $query = Affiliator::find()
            ->select("$a.id, $a.name")
            ->addSelect(['total' => 'sum(if(' . $v . '.id > 0, 1, 0))'])
            ->addSelect(['usedTotal' => 'sum(if(' . $v . '.isPull = 1, 1, 0))'])
            ->leftJoin($v, "$v.affiliator_id = $a.id")
            ->where(["$a.isO2O" => true]);
        if (!empty($name)) {
            $query->andWhere(['like', "$a.name", $name]);
        }
        $affs = $query->groupBy("$a.id")
            ->asArray()
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $affs,
        ]);
        $pages = new Pagination([
            'totalCount' => count($affs),
            'pageSize' => 10,
        ]);
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }
}
