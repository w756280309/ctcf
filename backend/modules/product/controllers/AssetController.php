<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-5-29
 * Time: 上午9:17
 */
namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\product\Asset;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\user\OriginalBorrower;
use common\models\user\User;
use Yii;
use yii\data\Pagination;

class AssetController extends BaseController
{
    /**
     * 资产包列表页
     * @return mixed
     */
    public function actionIndex()
    {
        //获取搜索相关信息
        $request = Yii::$app->request->get();
        //查询所有资产包数据
        $query = Asset::find();
        //加入搜索条件
        if (!empty($request['sn'])) {
            $query->andWhere(['sn' => trim($request['sn'])]);
        }
        if (!empty($request['borrowerName'])) {
            $query->andWhere(['like', 'borrowerName', $request['borrowerName']]);
        }
        $query->orderBy(['createTime' => SORT_DESC]);
        $totalCount = $query->count();
        //分页
        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => '20']);
        $models = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', ['models' => $models, 'pages' => $pages]);
    }
    /**
     * 发布标的
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreateProduct($id)
    {
        $asset = Asset::findOne([
            'id' => $id,
            'status' => Asset::STATUS_INIT, //初始状态
            'issue' => 1    //可以发标
        ]);
        //资产包不存在
        if (is_null($asset)) {
            throw $this->ex404('[' . $id . ']资产包不存在或当前资产包不能发标');
        }
        //判断是否发过标
        if (!is_null($asset->getProduct())) {
            throw $this->ex404('[' . $id . ']资产包已经发过标的');
        }
        $model = new OnlineProduct();
        $model->asset_id = $asset->id;
        $model->cid = 3;    //类型-网贷
        $model->refund_method = $asset->exchangeRepaymentType();   //还款方式
        $model->expires = $asset->expires;  //期限
        $model->borrowerRate = $asset->rate;
        $model->money = $asset->amount;
        $model->borrow_uid = $asset->borrower->id;

        $ob = OriginalBorrower::find()->orderBy(['id' => SORT_ASC])->all();

        return $this->render('/productonline/edit', [
            'model' => $model,
            'ctmodel' => null,
            'rongziInfo' => $this->orgUserInfo([1,2]),
            'fundReceiver' => $this->orgUserInfo([3]),
            'alternativeRepayer' => $this->orgUserInfo([4]),
            'guarantee' => $this->orgUserInfo([5]),
            'con_name_arr' => null,
            'con_content_arr' => null,
            'issuer' => Issuer::find()->all(),
            'ob' => $ob,
        ]);

    }

}