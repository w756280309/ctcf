<?php

namespace common\action\credit;

use common\models\product\OnlineProduct;
use yii\base\Action;

class RiskNoteAction extends Action
{
    /**
     * 风险揭示提示页.
     *
     * @param int type      1 转让人 2 受让人
     * @param int loanId    转让记录对应的标的ID
     */
    public function run($type = 1, $loanId = null)
    {
        $type = intval($type);
        if (!in_array($type, [1, 2])) {
            $type = 1;
        }

        $loan = null;
        if (2 === $type) {
            if (empty($loanId)) {
                throw $this->controller->ex404();
            } else {
                $loan = $this->controller->findOr404(OnlineProduct::class, $loanId);
            }
        }

        return $this->controller->render('risk_note', [
            'type' => $type,
            'loan' => $loan,
        ]);
    }
}
