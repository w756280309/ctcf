<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\product\Issuer;
use common\models\product\JxPage;
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
     * 3. 如果上述四个发行方存在项目配置页，则优先以项目配置页显示
     *
     * @param int $id 发行方ID
     */
    public function actionIndex($id)
    {
        if (null === ($jxPage = JxPage::find()->where(['issuerId' => $id])->one())) {
            if (!in_array($id, [2, 3, 5, 10])) {
                throw $this->ex404();
            }
        }

        $issuer = $this->findOr404(Issuer::class, $id);
        $loansCount = OnlineProduct::findSpecial(['issuer' => $issuer->id])
            ->count();

        if (in_array($id, [2, 3, 5, 10]) && null === $jxPage) {
            return $this->render('index', [
                'issuer' => $issuer,
                'loansCount' => intval($loansCount),
            ]);
        }
        return $this->render('new_index', [
            'issuer' => $issuer,
            'loansCount' => intval($loansCount),
            'jxPage' => $jxPage,
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

        $loan = OnlineProduct::fetchSpecial([
            'issuer' => $issuerid,
        ]);

        return $this->redirect(null === $loan ? '/deal/deal' : '/deal/deal/detail?sn='.$loan->sn);
    }
}
