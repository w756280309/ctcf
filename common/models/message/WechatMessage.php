<?php

namespace common\models\message;

use Lhjx\Noty\WechatTemplateMessageInterface;

class WechatMessage implements WechatTemplateMessageInterface
{
    public $data;
    public $linkUrl = null;
    public $openId = null;
    public $templateId = null;
    protected $user;

    //获取消息内容
    public function getData()
    {
        return $this->data;
    }

    //获取模板ID
    public function getTemplateId()
    {
        return $this->templateId;
    }

    //获取链接Url
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    //获取OPEN_ID
    public function getOpenIdByUser()
    {
        $user = $this->user;
        if (null !== $user) {
            $socialConnect = $user->socialConnect;
            if (null !== $user->socialConnect) {
                $this->openId = $socialConnect->resourceOwner_id;
            }
        }

        return $this->openId;
    }

    //获取所有的参数
    public function getParams()
    {
        return [
            'data' => $this->getData(),
            'linkUrl' => $this->getLinkUrl(),
            'openId' => $this->getOpenIdByUser(),
            'templateId' => $this->getTemplateId(),
        ];
    }
}
