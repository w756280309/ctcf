<?php
/**
 * 保全定时任务
 */
namespace console\controllers;

use common\controllers\ContractTrait;
use common\models\order\BaoQuanQueue;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use EBaoQuan\Client;
use Wcg\Lock\FileLock;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class BaoQuanController extends Controller
{
    use ContractTrait;

    //根据保全队列批量添加保全[确认计息时候将普通标的的订单进行保全]
    public function actionIndex()
    {
        //保全开关
        $toggle = \Yii::$app->params['enable_ebaoquan'];
        if (!$toggle) {
            $this->stdout("缺少配置参数：enable_ebaoquan；或者enable_ebaoquan 被配置为false 。\n", Console::BOLD);
            return 0;
        }

        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_LOAN])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (count($queues) > 0) {
            $this->dealLoanOrderBaoQuan($queues);
        } else {
            sleep(3);
        }
    }

    private function dealLoanOrderBaoQuan($queues)
    {
        foreach ($queues as $queue) {
            $proId = $queue['itemId'];
            $product = OnlineProduct::findOne($proId);
            if (null !== $product) {
                try {
                    Client::createBq($product);
                    $queue->status = BaoQuanQueue::STATUS_SUCCESS;//处理成功
                    $queue->save(false);
                } catch (Exception $e) {
                    $queue->status = BaoQuanQueue::STATUS_FAILED;//处理失败
                    $queue->save(false);
                }
            }
        }
    }

    //测试保全是否联通
    public function actionPing()
    {
        Client::ping();
    }

    /**
     * 保全买方债权合同（买方成功订单进行保全，包括原始标的合同，三份合成一份，包括一份新的转让合同）
     * 标的相关合同的用户为购买转让的用户，时间及金额为购买转让的时间和金额。
     */
    public function actionCreditOrder()
    {
        //保全开关
        $toggle = \Yii::$app->params['enable_ebaoquan'];
        if (!$toggle) {
            $this->stdout("缺少配置参数：enable_ebaoquan；或者enable_ebaoquan 被配置为false 。\n", Console::BOLD);
            return 0;
        }
        $cmdLock = new FileLock(\Yii::getAlias('@lock'), 'bao_quan_credit_order', 120);
        if (!$cmdLock->acquire()) {
            exit;
        }

        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_CREDIT_ORDER])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (count($queues) > 0) {
            $this->dealCreditOrderBaoQuan($queues);
        }

        $cmdLock->release();
    }

    private function dealCreditOrderBaoQuan($queues)
    {
        $txClient = \Yii::$container->get('txClient');
        $ids = ArrayHelper::getColumn($queues, 'itemId');
        $res = $txClient->post('assets/success-list', [
            'credit_order_ids' => $ids,
        ]);
        $assets = $res['data'];
        $assets = ArrayHelper::index($assets, 'credit_order_id');
        foreach ($queues as $queue) {
            if (!isset($assets[$queue['itemId']])) {
                continue;
            }
            $asset = $assets[$queue['itemId']];
            $user = User::findOne($asset['user_id']);
            try {
                $contracts = $this->getUserContract($asset);
                Client::createCreditBq($contracts, $user, $asset);

                $queue->status = BaoQuanQueue::STATUS_SUCCESS;
                $queue->save(false);
            } catch (\Exception $ex) {
                $queue->status = BaoQuanQueue::STATUS_FAILED;
                $queue->save(false);
            }
        }
    }

    /**
     * 保全卖方债权合同（债权撤销或者完成之后，保全卖方合同包括原始合同，三份合成一份，包括转让合同，所有该转让的订单的合并）
     * 标的相关合同为卖方当初购买标的或者购买转让的合同
     */
    public function actionCreditNote()
    {
        //保全开关
        $toggle = \Yii::$app->params['enable_ebaoquan'];
        if (!$toggle) {
            $this->stdout("缺少配置参数：enable_ebaoquan；或者enable_ebaoquan 被配置为false 。\n", Console::BOLD);
            return 0;
        }
        $cmdLock = new FileLock(\Yii::getAlias('@lock'), 'bao_quan_credit_note', 300);
        if (!$cmdLock->acquire()) {
            exit;
        }

        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_CREDIT_NOTE])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (!empty($queues)) {
            $this->dealCreditNoteBaoQuqn($queues);
        }

        $cmdLock->release();
    }

    private function dealCreditNoteBaoQuqn($queues)
    {
        $ids = ArrayHelper::getColumn($queues, 'itemId');
        $txClient = \Yii::$container->get('txClient');
        $res = $txClient->post('credit-note/sold-res', ['credit_note_ids' => $ids]);
        if (empty($res)) {
            return;
        }
        $txClient = \Yii::$container->get('txClient');
        foreach ($queues as $queue) {
            $noteId = $queue['itemId'];
            if (!isset($res[$noteId])) {
                continue;
            }
            $note = $txClient->get('credit-note/detail', ['id' => $noteId]);
            $seller = User::findOne($note['user_id']);
            $assets = $res[$noteId];
            $amount = 0;
            $creditContract = [];
            $asset = reset($assets);
            $user = User::findOne($asset['user_id']);
            $loan = OnlineProduct::findOne($asset['loan_id']);
            if (empty($user) || empty($loan)) {
                throw new \Exception('信息不全');
            }
            foreach ($assets as $asset) {
                if ($asset['note_id'] && $asset['credit_order_id']) {
                    //购买该转让生成的转让合同
                    $creditTemplate = $this->loadCreditContractByAsset($asset, $txClient, $loan);
                    $creditContract[] = $creditTemplate['content'];
                    $amount = bcadd($amount, $creditTemplate['amount'], 2);
                }
            }
            $content = implode(' <br><hr><br> ', $creditContract);

            $queue->status = BaoQuanQueue::STATUS_SUCCESS;
            $queue->save(false);
            try {
                Client::createCreditSellerBq($content, $user, $amount, $loan, $noteId, $seller);
            } catch (\Exception $ex) {
                $queue->status = BaoQuanQueue::STATUS_FAILED;
                $queue->save(false);
                \Yii::trace('转让结束之后生成保全合同，标的ID:' . $loan->id . ';保全失败,债权ID:' . $noteId . ';失败信息' . $ex->getMessage(), 'bao_quan');
            }
        }
    }

    /**
     * 失败的保全重新进行保全，手工运行，如果正式环境也出现失败保全，那么考虑增加定时任务。
     */
    public function actionCheck()
    {
        $cmdLock = new FileLock(\Yii::getAlias('@lock'), 'bao_quan_check', 300);
        if (!$cmdLock->acquire()) {
            exit('其他进程正在占用资源，请稍后重试');
        }

        //处理普通标的失败保全
        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_FAILED, 'itemType' => BaoQuanQueue::TYPE_LOAN])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (count($queues) > 0) {
            $this->dealLoanOrderBaoQuan($queues);
        }
        //处理购买债权失败订单
        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_FAILED, 'itemType' => BaoQuanQueue::TYPE_CREDIT_ORDER])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (count($queues) > 0) {
            $this->dealCreditOrderBaoQuan($queues);
        }
        //处理卖方债权保全
        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_FAILED, 'itemType' => BaoQuanQueue::TYPE_CREDIT_NOTE])->orderBy(['id' => SORT_ASC])->limit(20)->all();
        if (!empty($queues)) {
            $this->dealCreditNoteBaoQuqn($queues);
        }

        $cmdLock->release();
    }
}