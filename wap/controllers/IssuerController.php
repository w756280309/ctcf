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
     * 1. 如果发行方没有对应的标的,则按钮不显示;
     *
     * @param int $id 发行方ID.
     * @param int $type 标志静态页面渲染内容,1代表宁富1号三都国资定向融资工具,2代表南金交·中盛海润1号,3代表北大高科 临时代码,今后统一为从后台读取内容
     */
    public function actionIndex($id, $type = 1)
    {
        $type = intval($type);
        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        $issuer = $this->findOr404(Issuer::class, $id);
        $loansCount = $this->loanQuery($issuer->id)
            ->count();

        return $this->render('index', [
            'issuer' => $issuer,
            'type' => $type,
            'loansCount' => intval($loansCount),
        ]);
    }

    /**
     * 立即认购按钮跳转.
     *
     * 1. 跳转对应标的规则如下:
     * 优先跳转到募集中项目且募集比例高的项目;
     * 如果没有募集中项目,就按照发行方对应的标的倒序排列,取最新的一个;
     *
     */
    public function actionToLoan($issuerid)
    {
        if (empty($issuerid)) {
            return $this->redirect('/deal/deal');
        }

        $loan = $this->loanQuery($issuerid)
            ->andWhere(['status' => OnlineProduct::STATUS_NOW])
            ->andWhere(['<', 'finish_rate', 1])
            ->orderBy(['finish_rate' => SORT_DESC, 'id' => SORT_DESC])
            ->one();

        if (null === $loan) {
            $loan = $this->loanQuery($issuerid)
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }

        return $this->redirect(null === $loan ? '/deal/deal' : '/deal/deal/detail?sn='.$loan->sn);
    }

    private function loanQuery($issuerId)
    {
        return OnlineProduct::find()->where([
            'issuer' => $issuerId,
            'online_status' => true,
            'del_status' => false,
            'isPrivate' => false,
            'isTest' => false,
        ]);
    }
}