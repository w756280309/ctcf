<?php

namespace common\models\user;

use PayGate\Cfca\CfcaUtils;
use ew \common\lib\bchelp\BcRound;

class MoneyLogUtils {

    private $sn;
    private $osn;
    private $user;
    private $money;
    private $type;

    public function __construct(
    $user, $osn, $money, $type
    ) {
        $this->sn = CfcaUtils::generateSn("M");
        $this->osn = $osn;
        $this->user = $user;
        $this->type = $type;
        $this->money = $money;
    }

    public function getSn() {
        return $this->sn;
    }

    /**
     * 创建资金流记录
     * @return boolean
     * @throws Exception
     */
    public function buildRecord() {
        bcscale(14);
        $bcround = new BcRound();
        $uaccount = $this->user->accountInfo;

        $moneyRecord = new MoneyRecord([
            'sn' => $this->getSn(),
            'type' => $this->type,
            'osn' => $this->osn,
            'account_id' => $uaccount->id,
            'uid' => $this->user->id
        ]);
        $available_balance = 0;
        $account_balance = 0;
        if (in_array($this->type, [MoneyRecord::TYPE_RECHARGE, MoneyRecord::TYPE_FANGKUAN, MoneyRecord::TYPE_HUANKUAN, MoneyRecord::TYPE_CHEBIAO])) {//充值、放款、还款、撤标时资金账户增加
            $moneyRecord->in_money = $this->money;
            $uaccount->in_sum = $bcround->bcround(bcadd($uaccount->in_sum, $this->money), 2);
            $available_balance = $bcround->bcround(bcadd($uaccount->available_balance, $this->money), 2);
            $account_balance = $bcround->bcround(bcadd($uaccount->account_balance, $this->money), 2);
        } else if (in_array($this->type, [MoneyRecord::TYPE_DRAW, MoneyRecord::TYPE_FEE])) {//提现和手续费时资金减
            $moneyRecord->out_money = $this->money;
            $uaccount->out_sum = $bcround->bcround(bcadd($uaccount->out_sum, $this->money), 2);
            $available_balance = $bcround->bcround(bcsub($uaccount->available_balance, $this->money), 2);
            $account_balance = $bcround->bcround(bcsub($uaccount->account_balance, $this->money), 2);
        } else {
            throw new Exception('资金记录类型异常');
        }
        $moneyRecord->balance = $available_balance;
        $uaccount->available_balance = $available_balance;
        $uaccount->account_balance = $account_balance;
        if (bccomp($available_balance, 0) < 0 || bccomp($account_balance, 0) < 0) {
            throw new Exception('资金余额异常');
        }
        $transaction = Yii::$app->db->beginTransaction();
        if (!$moneyRecord->save()) {
            $transaction->rollBack();
            throw new Exception('资金记录异常');
        }
        if (!$uaccount->save()) {
            $transaction->rollBack();
            throw new Exception('资金修改异常');
        }
        $transaction->commit();
        return true;
    }

}
