<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\UserAccount;
use common\models\user\UserInfo;
use Yii;
use yii\web\Controller;

class LicaiController extends Controller
{
    use HelpersTrait;

    public function actionNotes($page = 1)
    {
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $userAccount = UserAccount::findOne(['uid' => $user->id]);
            $userInfo = UserInfo::findOne(['user_id' => $user->id]);
        }
        if (is_null($user) || $userAccount->available_balance + $userInfo->investTotal < 50000) {
            $jianguan = true;
        } else {
            $jianguan = false;
        }

        $array = [];
        if ($jianguan) {
            $query = OnlineProduct::find()->select('id');
            $query->andWhere(['isLicai' => false]);
            $loans = $query->andWhere("NOT((cid = 2) and if(refund_method = 1, expires > 180, expires > 6))")->asArray()->all();
            foreach ($loans as $v) {
                $array[] = $v['id'];
            }
        }
        $notes = [];
        $tp = 0;
        $txClient = Yii::$container->get('txClient');
        $response = $txClient->post('credit-note/list', ['page' => $page, 'page_size' => 5, 'isCanceled' => false, 'loans' => $array]);
        if (null !== $response) {
            $user = Yii::$app->user->getIdentity();
            if (!is_null($user)) {
                $userIn = UserInfo::findOne(['user_id' => $user->id]);
            }

            $notes = $response['data'];

            foreach ($notes as $key => $note) {
                $loan_id = (int) $note['loan_id'];
                $order_id = (int) $note['order_id'];

                $notes[$key]['loan'] = OnlineProduct::findOne($loan_id);
                $notes[$key]['order'] = OnlineOrder::findOne($order_id);
            }

            $tp = ceil($response['total_count'] / $response['page_size']);
            $header = [
                'count' => $response['total_count'],
                'size' => $response['page_size'],
                'tp' => $tp,
                'cp' => $response['page'],
            ];
            $code = ($page > $tp) ? 1 : 0;
            $message = ($page > $tp) ? '数据错误' : '消息返回';
        }

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/views/licai/_more_note.php', ['notes' => $notes]);
            return ['header' => $header, 'code' => $code, 'message' => $message, 'notes' => $notes, 'html' => $html];
        }

        return $this->render('notes', ['notes' => $notes, 'tp' => $tp]);
    }
}
