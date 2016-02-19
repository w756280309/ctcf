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
use common\models\order\OnlineOrder as OrdTx;

class UmpController extends Controller
{

    public function actionReguser(){
        //account_id => 02000000087365 'user_id' => 'UB201602151131580000000000043823'
        $resp = \Yii::$container->get('ump')->register(100, '胡绍和', 'IDENTITY_CARD', '13252719641001003X', '15810036547');
        var_dump($resp);
    }

    public function actionGetuser($epayUserId)
    {
        var_dump(\Yii::$container->get('ump')->getUserInfo($epayUserId));
    }

    public function actionGetloan($loanid = 1454555878)
    {
        var_dump(\Yii::$container->get('ump')->getLoanInfo($loanid));
    }
    
    public function actionQpaybinding()
    {
        $bind = new \common\models\user\QpayBinding([
            'binding_sn' => '1601141335024304205283',
            'epayUserId' => 'UB201602151131580000000000043823',
            'uid' => 2,
            'bank_id' => '1',
            'account' => '胡绍和',
            'card_number' => '6222020200000000000',
            'account_type' => 1,
            'mobile' => '15810036547',
            'created_at' => time(),
        ]);
        $resp = \Yii::$container->get('ump')->enableQpay($bind);
        return $this->redirect($resp);
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
    public function actionUploanstate($id, $state)
    {
        $resp = \Yii::$container->get('ump')->updateLoanState($id, $state);
        var_dump($resp);
    }

    /**
     * 修改信息
     */
    public function actionUploaninfo($id)
    {
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
    public function actionUploanborrower($id)
    {
        //92建标状态测试修改成功
        //修改不存在借款人失败
        //如果还是此借款人不变，【00240221】标的中此用户关系已建立，请勿重复操作。
        $deal = Loan::findOne($id);
        $borrow = new Borrower(7601209, null, Borrower::MERCHAT);
        $resp = \Yii::$container->get('ump')->updateLoanBorrower($deal, $borrow);
        var_dump($resp);
    }

    /**
     * 个人充值申请
     */
    public function actionQpay()
    {
        $rr = new \common\models\user\RechargeRecord([
            'sn' => time(),
            'fund' => 20000000,
            'uid' => '1',
            'bank_id' => 'icbc',
            'pay_type' => 1,
            'clientIp' => ip2long('192.168.1.38'),
            'epayUserId' => 'UB201602151131580000000000043823',
            'created_at' => time(),
        ]);
        $resp = \Yii::$container->get('ump')->rechargeViaQpay($rr);
        if ($resp->isRedirection()) {
            return $this->redirect($resp->getLocation());
        } else {
            echo 'error';
        }
    }
    
    /**
     * 投标
     */
    public function actionRegord()
    {
        $ord = new OrdTx([
            'sn' => time().'',
            'online_pid' => '60',
            'uid' => 2,
            'order_money' => 100,
            'created_at' => time(),
        ]);
        $resp = \Yii::$container->get('ump')->registerOrder($ord);
        return $this->redirect($resp);
        //var_dump($resp);
    }
    
    public function actionMerinfo()
    {
        $resp = \Yii::$container->get('ump')->getMerchantInfo(7601209);
        var_dump($resp);
    }

    public function actionFk()
    {
        $fk = \common\models\order\OnlineFangkuan::findOne(1);
        $resp = \Yii::$container->get('ump')->loanTransferToMer($fk);
        var_dump($resp);
    }

    //////////////////
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    public function actionQpayreturl(){
        var_dump($_POST,$_GET);
    }

    public function actionQpaynotifyurl(){
        var_dump($_POST,$_GET);
    }
}
