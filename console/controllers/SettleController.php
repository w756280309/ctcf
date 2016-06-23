<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class SettleController extends Controller
{
    public function actionDump()
    {
        $date = new \DateTime('2016-03-01');
        $end = new \DateTime('2016-05-31');
        while (true) {
            $dateString = $date->format('Ymd');

            $resp = Yii::$container->get('ump')->getSettlement($dateString);
            file_put_contents($dateString.'_settlement.txt', $resp);

            $date->add(new \DateInterval('P1D'));
            if ($date > $end) {
                break;
            }
        }
    }
}
