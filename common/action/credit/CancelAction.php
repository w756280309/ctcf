<?php

namespace common\action\credit;

use Yii;
use yii\base\Action;

class CancelAction extends Action
{
    public function run($id)
    {
        if (empty($id) || null === ($note = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => $id]))) {
            return ['code' => 1, 'message' => '没有找到该转让信息'];
        }

        $userId = $this->controller->getAuthedUser()->getId();

        if ((int) $userId !== $note['user_id']) {
            return ['code' => 1, 'message' => '非本人不能撤销该转让信息'];
        }

        try {
            Yii::$container->get('txClient')->get('credit-note/cancel', [
                'id' => $id,
            ]);
        } catch (\Exception $ex) {
            $result = json_decode(strval($ex->getResponse()->getBody()), true);
            if (isset($result['name'])
                && $result['name'] === 'Bad Request'
                && isset($result['message'])
                && isset($result['status'])
                && $result['status'] !== 200
            ) {
                return ['code' => 1, 'message' => $result['message']];
            }
        }

        return ['code' => 0, 'message' => '撤销成功'];
    }
}
