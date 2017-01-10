<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use yii\console\Controller;

class DrawRepairController extends Controller
{
    /**
     * 修复联动已成功但温都依旧为已审核的订单
     *
     * @param  int  $id  提现订单id
     *
     * @throws \Exception
     */
    public function actionConfirm($id)
    {
        $drawRecord = DrawRecord::findOne($id);
        if (null === $drawRecord) {
            throw new \Exception('未找到该订单');
        }

        if (DrawRecord::STATUS_EXAMINED !== (int) $drawRecord->status) {
            throw new \Exception('提现状态必须为已审核');
        }

        try {
            DrawManager::commitDraw($drawRecord);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }
}
