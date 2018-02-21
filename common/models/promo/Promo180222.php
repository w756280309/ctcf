<?php

namespace common\models\promo;

use common\models\adv\Session;
use common\models\user\User;
use yii\helpers\ArrayHelper;
use Yii;

class Promo180222 extends BasePromo
{
    /**
     * 交卷并发奖逻辑ACTION
     *
     * @param User $user 用户对象
     * @param string $sn 批次号
     * @param string $res 答题结果
     *
     * @return array
     * @throws \Exception
     */
    public function finish($user, $sn, $res)
    {
        $qa = Question::find()
            ->select('id,answer')
            ->where(['batchSn' => $sn])
            ->andWhere(['promoId' => $this->promo->id])
            ->indexBy('id')
            ->asArray()
            ->all();
        if (empty($qa)) {
            throw new \Exception('无答题信息');
        }

        //计算答题正确次数
        $correctNum = 0;
        $resDecode = json_decode($res, true);
        if (null === $resDecode) {
            throw new \Exception('答题信息格式错误');
        }

        foreach ($resDecode as $quesId => $answerId) {
            if (isset($qa[$quesId]) && (string) $answerId === $qa[$quesId]['answer']) {
                $correctNum++;
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //根据答对的条数来判断奖池概率
            if (0 === $correctNum) {
                $pool = ['180222_ZW' => '1'];
            } else if ($correctNum <= 3) {
                $pool = [
                    '180222_ZW' => '0.2',
                    '180222_C3' => '0.25',
                    '180222_C5' => '0.25',
                    '180222_C8' => '0.2',
                    '180222_C10' => '0.1',
                ];
            } else {
                $pool = [
                    '180222_C3' => '0.25',
                    '180222_C5' => '0.25',
                    '180222_C8' => '0.25',
                    '180222_C10' => '0.15',
                    '180222_C20' => '0.1',
                ];
            }

            //防止重复发奖
            TicketToken::initNew($this->promo->id.'-'.$user->id.'-'.$sn)->save(false);
            $awardSn = PromoService::openLottery($pool);
            $reward = Reward::fetchOneBySn($awardSn);
            $awardBool = PromoService::award($user, $reward, $this->promo);
            if (!$awardBool) {
                throw new \Exception('发奖失败');
            }

            //插入答题记录
            $session = Session::initNew($user, $sn);
            $session->answers = $res;
            $session->save(false);
            $transaction->commit();

            return [
                'prize' => $reward,
                'correctNum' => $correctNum,
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 获得本地的一批题目信息
     *
     * @param User $user 用户对象
     * @param \DateTime $joinTime 参与时间
     *
     * @return array
     */
    public function getQuestions(User $user, $joinTime)
    {
        $sessionCount = (int) Session::findByCreateTime($user, $joinTime)->count();
        $sessionCount = $sessionCount >= 1 ? 1 : $sessionCount;
        $dateSn = $joinTime->format('Ymd') . $sessionCount;
        $data = [];
        $questions = Question::find()
            ->select("id,title as content,batchSn")
            ->where(['batchSn' => $dateSn])
            ->andWhere(['promoId' => $this->promo->id])
            ->asArray()
            ->all();
        if (!empty($questions)) {
            $questionsIds = ArrayHelper::getColumn($questions, 'id');

            $options = Option::find()
                ->select('id, content, questionId')
                ->where(['in', 'questionId', $questionsIds])
                ->asArray()
                ->all();

            $options = ArrayHelper::map($options, 'id', 'content', 'questionId');

            foreach ($questions as $k => $question) {
                if (isset($options[$question['id']])) {
                    $data[$k] = $question;
                    $data[$k]['options'] = $options[$question['id']];
                }
            }
        }
        shuffle($data);

        return $data;
    }
}
