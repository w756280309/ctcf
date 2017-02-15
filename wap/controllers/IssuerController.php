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
     * 2. 如果非这四个发行方，统一抛404;
     *    2   =>   宁富1号三都投资
     *    3   =>   宁富17号北大高科
     *    5   =>   南金交中盛海润
     *   10   =>   南金交宁富20号中科建
     *
     * @param int $id 发行方ID
     */
    public function actionIndex($id)
    {
        if (!in_array($id, [2, 3, 5, 10])) {
            throw $this->ex404();
        }
        $issuer = $this->findOr404(Issuer::class, $id);
        $loansCount = $this->loanQuery($issuer->id)
            ->count();

        return $this->render('index', [
            'issuer' => $issuer,
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
