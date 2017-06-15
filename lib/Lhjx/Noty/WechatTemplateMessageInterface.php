<?php

namespace Lhjx\Noty;

interface WechatTemplateMessageInterface
{
    public function getData();            //获取消息内容
    public function getTemplateId();      //获取模板ID
    public function getLinkUrl();         //获取链接Url
    public function getOpenIdByUser();    //获取OPEN_ID
    public function getParams();          //获取所有的参数
}