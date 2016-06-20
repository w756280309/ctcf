<?php

namespace frontend\controllers;

use common\models\bank\EbankConfig;
use common\models\bank\QpayConfig;
use common\models\bank\Bank;
use common\controllers\HelpersTrait;
use yii\web\Controller;

class HelpcenterController extends Controller
{
    use HelpersTrait;

    public function actionOperation($type = 0)
    {
        $e = EbankConfig::tableName();
        $q = QpayConfig::tableName();
        $b = Bank::tableName();

        $ebank = (new \yii\db\Query())
            ->select("$e.*, $b.bankName")
            ->from($e)
            ->leftJoin($b, "$e.bankId = $b.id")
            ->where(["$e.typePersonal" => 1, 'isDisabled' => 0])
            ->all();

        $qpay = (new \yii\db\Query())
            ->select("$q.*, $b.bankName")
            ->from($q)
            ->leftJoin($b, "$q.bankId = $b.id")
            ->where(['isDisabled' => 0])
            ->all();

        return $this->render('operation', ['type' => $type, 'ebank' => $ebank, 'qpay' => $qpay]);
    }

    public function actionBackground()
    {
        return $this->render("background");
    }

    public function actionProduct()
    {
        return $this->render("product");
    }

    public function actionSecurity()
    {
        return $this->render("security");
    }
}
