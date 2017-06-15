<?php

namespace common\models\message;

use common\models\user\DrawRecord;
use common\utils\StringUtils;
use Yii;

/**
 * Class DrawMessage
 * @package common\models\message
 * @testCase \Test\Common\Models\Message\DrawMessageTest
 */
class DrawMessage extends WechatMessage
{
    public function __construct(DrawRecord $drawRecord)
    {
        $this->data = [
            'first' => ['尊敬的客户，您申请的提现成功，资金已到达银行卡，请注意查看。', 'black'],
            'keyword1' => [date('Y-m-d H:i:s', $drawRecord->created_at), 'black'],
            'keyword2' => [StringUtils::amountFormat3($drawRecord->money).'元', 'black'],
            'remark' => ['如有疑问请致电：400-101-5151进行咨询。', 'black'],
        ];
        $this->user = $drawRecord->user;
        $this->templateId = Yii::$app->params['draw_message_template_id'];
    }
}
