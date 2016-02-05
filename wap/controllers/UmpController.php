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
        $borrow = new Borrower(7601209, null, Borrower::MERCHAT);
        $resp = Loan::createLoan($deal, $borrow);
        var_dump($resp);
    }
    
    /**
     * 修改状态
     */
    public function actionUploanstate($id,$state){
        //建标状态修改为1失败
        //跨状态修改失败
        //建标状态修改状态值非法【00060700】请求的参数[project_state(123)]格式或值不正确
        //建标状态修改不存在的标的编号【00240200】标的不存在
        //92-0-1-2-3-4,顺利进行的步骤
        $resp = \Yii::$container->get('ump')->updateLoanState($id, $state);
        var_dump($resp);
    }
    
    /**
     * 修改信息
     */
    public function actionUploaninfo($id){
        //92建标状态测试修改成功
        //0开标可以修改
        //1，2，3，4失败
        $deal = Loan::findOne($id);
        $deal->title = time();
        $resp = \Yii::$container->get('ump')->updateLoanInfo($deal);
        var_dump($resp);
    }

    /**
     * 修改融资人
     */
    public function actionUploanborrower($id){
        //92建标状态测试修改成功
        //修改不存在借款人失败
        //如果还是此借款人不变，【00240221】标的中此用户关系已建立，请勿重复操作。
        $deal = Loan::findOne($id);
        $borrow = new Borrower(7601209, null, Borrower::MERCHAT);
        $resp = \Yii::$container->get('ump')->updateLoanBorrower($deal, $borrow);
        var_dump($resp);
    }
    
}
