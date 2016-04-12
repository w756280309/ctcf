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
use common\models\bank\EbankConfig;
use common\models\bank\QpayConfig;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
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
        $id = htmlspecialchars($id);
        $eBank = EbankConfig::find()->where(['bankId' => $id])->one();
        $qPay = QpayConfig::find()->where(['bankId' => $id])->one();
        if (!$eBank || !$qPay) {
            throw new NotFoundHttpException('信息未找到');
        }
        $qPay->singleLimit = number_format($qPay->singleLimit/10000,0);
        $qPay->dailyLimit = number_format($qPay->dailyLimit/10000,0);
        if ($eBank->load(Yii::$app->request->post()) && $qPay->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $qPay->singleLimit = 10000*$qPay->singleLimit;
            $qPay->dailyLimit = 10000*$qPay->dailyLimit;
            if ($eBank->save(false) && $qPay->save(false)) {
                return ['code' => true, 'msg' => '更新成功'];
            } else {
                return ['code' => false, 'msg' => '更新失败'];
            }
        }
        return $this->render('edit', [
            'eBank' => $eBank,
            'qPay' => $qPay,
            'id' => $id,
        ]);
    }
}