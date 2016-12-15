<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use yii\web\Controller;

class IssuerController extends Controller
{
    use HelpersTrait;

    /**
     * 发行方介绍页.
     *
     * @param int $id 发行方ID.
     * @param int $type 标志静态页面渲染内容,1代表宁富1号三都国资定向融资工具,2代表南金交·中盛海润1号 临时代码,今后统一为从后台读取内容
     */
    public function actionIndex($id, $type = 1)
    {
        $type = intval($type);
        if (!in_array($type, [1, 2])) {
            $type = 1;
        }

        $issuer = $this->findOr404(Issuer::class, $id);
        $loan = OnlineProduct::find()
            ->where([
                'issuer' => $issuer->id,
                'online_status' => true,
                'del_status' => false,
                'isPrivate' => false,
                'isTest' => false,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return $this->render('index', [
            'issuer' => $issuer,
            'loan' => $loan,
            'type' => $type,
        ]);
    }
}