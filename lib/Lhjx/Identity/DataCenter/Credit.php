<?php

namespace Lhjx\Identity\DataCenter;

class Credit
{
    const CREDIT_IDCARDELEMENTS = '身份二要素';
    const CREDIT_BANKELEMENTS_TWO = '银行卡二要素';
    const CREDIT_BANKELEMENTS_THREE = '银行卡三要素';
    const CREDIT_BANKELEMENTS_FOUR = '银行卡四要素';
    const CREDIT_PHONEELEMENTS_TWO = '手机号二要素';
    const CREDIT_PHONEELEMENTS_THREE = '手机号三要素_在网状态_在网时长';
    const CREDIT_CRIMINALRECORD = '犯罪记录';
    const CREDIT_MULTILOAN = '多头借贷';
    const CREDIT_DISCREDIT = '法院失信';
    const CREDIT_OVERDUE = '逾期记录';
    const CREDIT_BODYGUARD = '信贷保镖';

    const CREDIT_CSRFAUTH = 'csrfAuth';//获取csrf令牌
    const CREDI_LOGINAUTH = 'loginAuth';//模拟登陆地址
    const CREDIT_USERINFOAUTH = 'userInfoAuth';//用戶信息權限
    const CREDIT_LISTAPISAUTH = 'listApisAuth';//获取所有的请求接口
    const CREDIT_APISERVICEAUTH = 'apiServiceAuth';// 查询风控信息
    const CREDIT_APIRESPONSERESULT = 'apiResponseResult';//轮巡异步码返回检查结果
}
