<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

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
}
