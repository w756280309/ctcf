<?php
/**
 * Created by PhpStorm.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace app\controllers;

use yii\web\Controller;
use common\models\product\OnlineProduct as Loan;
use P2pl\Borrower;

class UmpController extends Controller
{

    public function actionGetuser($epayUserId)
    {
        var_dump(\Yii::$container->get('ump')->getUserInfo($epayUserId));
    }

    public function actionGetloan($loanid = 1454555878)
    {
        var_dump(\Yii::$container->get('ump')->getLoanInfo($loanid));
    }

    public function actionRegloan($id)
    {
        $deal = Loan::findOne($id);
        $borrow = new Borrower(2, null, Borrower::MERCHAT);
        $resp = Loan::createLoan($deal, $borrow);
        var_dump($resp);
    }

}
