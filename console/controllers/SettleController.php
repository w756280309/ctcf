<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\settle\Settle;

class SettleController extends Controller
{
    public function actionDump($type, $start, $end)
    {
        $date = new \DateTime($start);
        $end = new \DateTime($end);
        while (true) {
            $dateString = $date->format('Ymd');

            $resp = Yii::$container->get('ump')->getSettlement($dateString, $type);
            file_put_contents('settle/'.$dateString.'_settlement.txt', $resp);

            $date->add(new \DateInterval('P1D'));
            if ($date > $end) {
                break;
            }
        }
    }

    private $count = 0;
    private $lines = [];

    public function actionTransform()
    {
        if ($dirHandle = opendir('settle')) {
            while (false !== ($entry = readdir($dirHandle))) {
                $filename = 'settle/'.$entry;
                if (is_file($filename)) {
                    $this->parse($filename);
                }
            }

            closedir($dirHandle);
        }

        echo $this->count."\n";
    }

    private function parse($filename)
    {
        if (false === ($lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))) {
            throw new \Exception();
        }

        $this->count += count($lines);

        $logs = [];
        $valid = false;
        $startMark = 'TRADEDETAIL-START';
        $startMarkLen = strlen($startMark);
        $endMark = 'TRADEDETAIL-END';
        $endMarkLen = strlen($endMark);
        foreach ($lines as $line) {
            if (!$valid && strlen($line) >= $startMarkLen && substr($line, 0, $startMarkLen) === $startMark) {
                $valid = true;
                continue;
            }

            if (!$valid) {
                continue;
            }

            if (strlen($line) >= $endMark && substr($line, 0, $endMarkLen) === $endMark) {
                break;
            }

            echo $line."\n";
        }
    }

    /**
     * @return int
     */
    public function actionInsert($type)
    {
        $startdate = new \DateTime('2016-08-01');
        $enddate = new \DateTime('2016-09-01');
        $num = 0;

        while (true) {
            $dateString = $startdate->format('Ymd');
            $content = Yii::$container->get('ump')->getSettlement($dateString, $type);
            $carr = explode("\n", $content);
            foreach ($carr as $line) {
                if (false !== stripos($line, 'start') or false !== stripos($line, 'end')) {
                    continue;
                }
                $settle = $this->initSettle($type, $line);
                if ($settle->save()) {
                    $num++;
                }
                $settle = null;
            }

            $startdate->add(new \DateInterval('P1D'));
            if ($startdate > $enddate) {
                break;
            }
        }
        echo $num;
    }

    private function initSettle($type, $settle)
    {
        $newsettle = new Settle();
        switch ($type) {
            case '01';
                $items = explode(',', $settle);
                $txSn = $items[0];
                $txDate = $items[1];
                $money = $items[4];
                $fee = $items[8];
                $serviceSn = $items[7];
                $txType = Settle::TXTYPE_RECHARGE;
                break;
            case '02';
                $items = explode(',', $settle);
                $txSn = $items[2];
                $txDate = $items[3];
                $money = $items[4];
                $fee = $items[5];
                $serviceSn = $items[9];
                $txType = Settle::TXTYPE_DRAW;
                break;
            case '03';
                break;
            case '04';
                break;
            case '05';
                break;
            case '06';
                $items = explode('|', $settle);
                $txSn = $items[0];   //商户用户标识
                $txDate = $items[7];
                $money = 0.00;
                $fee = 0.00;
                $serviceSn = $items[1]; //资金托管平台用户号
                $txType = Settle::TXTYPE_REALNAME;
                break;
        }
        $newsettle->txSn = $txSn;
        $newsettle->txDate = $txDate;
        $newsettle->money = $money;
        $newsettle->fee = $fee;
        $newsettle->serviceSn = $serviceSn;
        $newsettle->txType = $txType;
        return $newsettle;
    }
}
