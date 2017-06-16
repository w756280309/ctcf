<?php

namespace common\models\message;

use common\models\mall\PointRecord;
use Yii;

/**
 * Class PointMessage
 * @package common\models\message
 * @testCase \Test\Common\Models\Message\PointMessageTest
 */
class PointMessage extends WechatMessage
{
    public function __construct(PointRecord $record)
    {
        $this->data = [
            'first' => ['恭喜您成功绑定账户，获得'.$record->incr_points.'积分奖励！', '#000000'],
            'keyword1' => [$record->incr_points, '#000000'],
            'keyword2' => [PointRecord::getTypeName($record->ref_type), '#000000'],
        ];
        $this->user = $record->user;
        $this->templateId = Yii::$app->params['wx.msg_tpl.draw_success'];
    }
}
