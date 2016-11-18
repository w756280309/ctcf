<?php

namespace common\controllers;

use common\models\contract\ContractTemplate;
use common\models\order\BaoQuanQueue;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use common\utils\StringUtils;
use common\view\LoanHelper;
use EBaoQuan\Client;
use Tx\TxClient;
use Yii;

trait ContractTrait
{
    /**
     * 获取用户合同
     * @param $asset_id
     * @param string $userType  seller:获取卖方合同;buyer:买方合同
     */
    private function getUserContract(array $asset)
    {
        //获取用户资产信息
        $txClient = \Yii::$container->get('txClient');
        if (empty($asset) || !isset($asset['loan_id']) || !isset($asset['order_id'])) {
            throw new \Exception('没有找到合适资产信息');
        }
        $user = User::findOne($asset['user_id']);
        $loan = OnlineProduct::findOne($asset['loan_id']);
        $loanOrder = OnlineOrder::findOne($asset['order_id']);
        if (empty($loan) || empty($loanOrder)) {
            throw new \Exception('没有找到合适资产信息');
        }
        //获取标的合同
        $loanContracts = $this->getUserLoanContract($asset, $txClient, $loanOrder, $user);
        $loanContract = $loanContracts['loanContract'];
        $loanAmount = $loanContracts['loanAmount'];
        $bqLoan = $loanContracts['bqLoan'];

        $creditContract = [];
        $key = 1;
        $bqCreditOrder = [];
        //获取该资产的转协议
        if ($asset['note_id'] && $asset['credit_order_id']) {
            //购买该转让生成的转让合同
            $creditTemplate = $this->loadCreditContractByAsset($asset, $txClient, $loan);

            //产看购买债权保全
            $bq = EbaoQuan::find()->where([
                'itemType' => EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
                'type' => EbaoQuan::TYPE_CREDIT,
                'success' => 1,
                'uid' => $user->id,
                'itemId' => $asset['credit_order_id'],
            ])->one();
            if (null !== $bq) {
                try {
                    $bqCreditOrder['downUrl'] = Client::contractFileDownload($bq);
                    $bqCreditOrder['linkUrl'] = Client::certificateLinkGet($bq);
                } catch (\Exception $ex) {

                }

            }

            $creditContract[] = ['title' => '产品转让协议'. str_pad($key, 2, '0', STR_PAD_LEFT), 'content' => $creditTemplate['content'], 'amount' => $creditTemplate['amount'], 'type' => 'credit_order', 'bqCredit' => $bqCreditOrder];
            $key++;
        }

        //获取销售转让相关合同
        $sellerContract = $this->getUserSellerContract($txClient, $asset, $loan, $key);
        $creditContract = array_merge($creditContract, $sellerContract);

        return ['loanContract' => $loanContract, 'creditContract' => $creditContract, 'loanAmount' => $loanAmount, 'loanId' => $loan->id, 'bqLoan' => $bqLoan];
    }

    //获取债权销售的相关转让协议
    public function getUserSellerContract(TxClient $txClient, $asset, OnlineProduct $loan, $key)
    {
        $creditContract = [];
        //获取该资产被转让的记录
        $soldRes = $txClient->get('assets/sold-res', ['asset_id' => $asset['id']]);
        if (count($soldRes) > 0) {
            foreach ($soldRes as $noteId => $assetLists) {
                $noteTemplate = [];
                $amount = 0;
                if (count($assetLists) > 0) {
                    foreach ($assetLists as $val) {
                        $template = $this->loadCreditContractByAsset($val, $txClient, $loan);
                        $noteTemplate[] = $template['content'];
                        $amount = bcadd($amount, $template['amount'], 2);
                    }
                }
                if (count($noteTemplate) > 0) {
                    $contentRes = implode(' <br><hr><br> ', $noteTemplate);

                    //产看债权卖家保全
                    $bq = EbaoQuan::find()->where([
                        'itemType' => EbaoQuan::ITEM_TYPE_CREDIT_NOTE,
                        'type' => EbaoQuan::TYPE_CREDIT,
                        'success' => 1,
                        'uid' => $asset['user_id'],
                        'itemId' => $noteId,
                    ])->one();
                    $bqCreditNote = [];
                    if (null !== $bq) {
                        try {
                            $bqCreditNote['downUrl'] = Client::contractFileDownload($bq);
                            $bqCreditNote['linkUrl'] = Client::certificateLinkGet($bq);
                        } catch (\Exception $ex) {

                        }
                    }

                    $creditContract[] = ['title' => '产品转让协议'. str_pad($key, 2, '0', STR_PAD_LEFT), 'content' => $contentRes, 'amount' => $amount, 'type' => 'credit_note', 'bqCredit' => $bqCreditNote];
                    $key++;
                }
            }
        }
        return $creditContract;
    }

    //获取标的相关合同
    public function getUserLoanContract(array $asset, TxClient $txClient, OnlineOrder $loanOrder, User $user)
    {
        //获取该资产订单信息
        if ($asset['credit_order_id']) {
            $order =  $txClient->get('credit-order/detail', ['id' => $asset['credit_order_id']]);
            $orderTime = strtotime($order['createTime']);
            $amount = bcdiv($order['principal'], 100, 2);
        } else {
            $orderTime = $loanOrder->order_time;
            $amount = $loanOrder->order_money;
        }
        //获取原标的协议
        $loanTemplates = ContractTemplate::findAll(['pid' => $asset['loan_id']]);
        $loanContract = [];
        foreach ($loanTemplates as $key => $loanTemplate) {
            if ($key === 0) {
                $title = '认购合同';
            } elseif ($key === 1) {
                $title = '风险提示书';
            } else {
                $title = Yii::$app->functions->cut_str($loanTemplate->name, 5, 0, '**');
            }
            $content = $this->loadLoanContract($loanTemplate->content, $user['real_name'], $user['idcard'], $orderTime, $amount);

            $loanContract[$key]  = ['title' => $title, 'content' => $content, 'type' => 'loan'];
        }
        $loanAmount = $amount;
        //产看标的合同的保全
        $bqLoan = [];
        if ($asset['note_id'] && $asset['credit_order_id']) {
            $bq = EbaoQuan::find()->where([
                'itemType' => EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
                'type' => EbaoQuan::TYPE_LOAN,
                'success' => 1,
                'uid' => $user->id,
                'itemId' => $asset['credit_order_id'],
            ])->one();
        } else {
            $bq = EbaoQuan::find()->where([
                'itemType' => EbaoQuan::ITEM_TYPE_LOAN_ORDER,
                'type' => EbaoQuan::TYPE_LOAN,
                'success' => 1,
                'uid' => $user->id,
                'itemId' => $loanOrder->id,
            ])->one();
        }
        if (null !== $bq) {
            try {
                $bqLoan['downUrl'] = Client::contractFileDownload($bq);
                $bqLoan['linkUrl'] = Client::certificateLinkGet($bq);
            } catch (\Exception $ex) {

            }
        } else {
            $bqLoan = [];
        }
        return ['loanContract' => $loanContract, 'loanAmount' => $loanAmount, 'bqLoan' => $bqLoan];
    }

    private function loadLoanContract($content ,$userName, $idCard, $orderTime, $amount)
    {
        $content = preg_replace("/{{投资人}}/is", $userName, $content);
        $content = preg_replace("/{{身份证号}}/is", $idCard, $content);
        $content = preg_replace("/{{认购日期}}/is", date("Y年m月d日", $orderTime), $content);
        $content = preg_replace("/{{认购金额}}/is", $amount, $content);
        $content = preg_replace("/｛｛投资人｝｝/is", $userName, $content);
        $content = preg_replace("/｛｛身份证号｝｝/is", $idCard, $content);
        $content = preg_replace("/｛｛认购日期｝｝/is", date("Y年m月d日", $orderTime), $content);
        $content = preg_replace("/｛｛认购金额｝｝/is", $amount, $content);
        return $content;
    }

    //根据债权资产、用户、标的订单、标的等信息填充债权转让合同模板
    private function loadCreditContractByAsset($asset, TxClient $txClient, OnlineProduct $loan)
    {
        $orderAmount = 0;
        if ($asset['note_id'] && $asset['credit_order_id']) {
            $queue = BaoQuanQueue::find()->where(['itemType' => BaoQuanQueue::TYPE_CREDIT_ORDER, 'itemId' => $asset['credit_order_id']])->one();
            $creditOrder = $txClient->get('credit-order/detail', ['id' => $asset['credit_order_id']]);
            $creditNote = $txClient->get('credit-note/detail', ['id' => $asset['note_id']]);
            $newPlans = $txClient->get('order/repayment', ['id' => $asset['order_id'], 'amount' => $creditOrder['principal']]);
            $prevAsset = $txClient->get('assets/detail', ['id' => $asset['asset_id']]);
            if (empty($prevAsset)) {
                throw new \Exception('没有找到资产');
            }
            if ($prevAsset['credit_order_id']) {
                $prevOrder = $txClient->get('credit-order/detail', ['id' => $prevAsset['credit_order_id']]);
                $prevOrderTime = strtotime($prevOrder['createTime']);
                $prevOrderAmount = bcdiv($prevOrder['principal'], 100, 2);
            } else {
                $prevOrder = OnlineOrder::findOne($prevAsset['order_id']);
                $prevOrderTime = $prevOrder->order_time;
                $prevOrderAmount = $prevOrder->order_money;
            }
            $user = User::findOne($prevAsset['user_id']);
            $buyer = User::findOne($creditOrder['user_id']);
            if (
                !empty($queue)
                && !empty($creditOrder)
                && !empty($creditNote)
                && !empty($newPlans)
                && count($newPlans) > 0
                && isset($creditOrder['user_id'])
                && !empty($buyer)
            ) {
                //生成标的利率
                $loanRate = LoanHelper::getDealRate($loan).'%';
                if (!empty($loan->jiaxi)) {
                    $loanRate = $loanRate.'+'.StringUtils::amountFormat2($loan->jiaxi).'%';
                }
                //生成标的还款方式
                $refund_methods = Yii::$app->params['refund_method'];
                if (isset($refund_methods[$loan->refund_method])) {
                    $refund_method = $refund_methods[$loan->refund_method];
                } else {
                    $refund_method = null;
                }
                $payedInterest = 0;//按照还款日理论已经支付的利息
                $remainingInterest = 0;//按照还款计划理论未还款利息
                foreach ($newPlans as $plan) {
                    if ($plan['date'] < $creditOrder['createTime']) {
                        $payedInterest = bcadd($payedInterest, $plan['interest'], 2);
                    } else {
                        $remainingInterest = bcadd($remainingInterest, $plan['interest'], 2);
                    }
                }

                $creditTemplate = $this->renderFile('@common/views/credit_contract_template.php', [
                    'contractNum' => $queue->getNum(),
                    'sellerName' => $user->real_name,
                    'sellerIdCard' => $user->idcard,
                    'buyerName' => $buyer->real_name,
                    'buyerIdCard' => $buyer->idcard,
                    'loanOrderCreateDate' => date('Y-m-d', $prevOrderTime),
                    'loanTitle' => $loan->title,
                    'loanOrderPrincipal' => $prevOrderAmount,
                    'creditOrderPrincipal' => bcdiv($creditOrder['principal'], 100, 2),
                    'loanIssuer' => $loan->getIssuerName(),
                    'affiliator' => $loan->getAffiliatorName(),
                    'exceptRaisedAmount' => $loan->money,
                    'incrAmount' => $loan->start_money,
                    'interestDate' => date('Y-m-d', $loan->jixi_time),
                    'finishDate' => date('Y-m-d', $loan->finish_date),
                    'yieldRate' => $loanRate,
                    'refundMethod' =>$refund_method,
                    'sellerInterest' => bcadd($payedInterest, bcdiv($creditOrder['interest'], 100, 2), 2),
                    'buyerInterest' => bcsub($remainingInterest, bcdiv($creditOrder['interest'], 100, 2), 2),
                    'discountRate' => $creditNote['discountRate'],
                    'refundedInterest' => $payedInterest,
                    'creditOrderPayAmount' => bcdiv($creditOrder['amount'], 100, 2),
                    'feeRate' => 3,
                    'feeAmount' => bcdiv($creditOrder['fee'], 100, 2),
                ]);
                $orderAmount = bcdiv($creditOrder['principal'], 100, 2);
            } else {
                $creditTemplate = '';
            }
        } else {
            $creditTemplate = '';
        }
        return ['content' => $creditTemplate, 'amount' => $orderAmount];
    }

    //获取未被加载的转让协议模板
    private function getCreditContractTemplate()
    {
        $content = $this->renderFile('@common/views/credit_contract_template.php', [
            'contractNum' => '',
            'sellerName' => '',
            'sellerIdCard' => '',
            'buyerName' => '',
            'buyerIdCard' => '',
            'loanOrderCreateDate' => '',
            'loanTitle' => '',
            'loanOrderPrincipal' => '',
            'creditOrderPrincipal' => '',
            'loanIssuer' => '',
            'affiliator' => '',
            'exceptRaisedAmount' => '',
            'incrAmount' => '',
            'interestDate' => '',
            'finishDate' => '',
            'yieldRate' => '',
            'refundMethod' => '',
            'sellerInterest' => '',
            'buyerInterest' => '',
            'discountRate' => '',
            'refundedInterest' => '',
            'creditOrderPayAmount' => '',
            'feeRate' => '',
            'feeAmount' => '',
        ]);

        return $content;
    }

    //获取所有合同模板
    private function getContractTemplate($loan_id, $note_id = 0)
    {
        $contracts = [];
        $model = ContractTemplate::findAll(['pid' => $loan_id]);
        if (empty($model)) {
            throw $this->ex404();  //当对象为空时,抛出异常
        }
        foreach ($model as $k => $val) {
            if ($k === 0) {
                $title = '认购合同';
            } elseif ($k === 1) {
                $title = '风险提示书';
            } else {
                $title = Yii::$app->functions->cut_str($val['name'], 5, 0, '**');
            }
            $contracts[]  = ['title' => $title, 'content' => $val['content']];
        }
        if ($note_id > 0) {
            $content = $this->getCreditContractTemplate();
            $contracts[] = ['title' => '产品转让协议', 'content' => $content];
        }
        return $contracts;
    }
}