# 定时脚本

```
# 保全
*/1 * * * * /usr/bin/php /data/www/WDJF/yii bao-quan/credit-order >> /dev/null 2>&1
*/1 * * * * /usr/bin/php /data/www/WDJF/yii bao-quan/credit-note >> /dev/null 2>&1     
# 统计             
30 03 * * * /usr/bin/php /data/www/WDJF/yii count/index >> /dev/null 2>&1
01 03 * * * /usr/bin/php /data/www/WDJF/yii stats/update >> /dev/null 2>&1
# 导出
00 */1 * * * /usr/bin/php /data/www/WDJF/yii data/export-lender-data >> /dev/null 2>&1
50 23 * * * /usr/bin/php /data/www/WDJF/yii data/export-issuer-record>> /dev/null 2>&1
# crm数据同步
*/2 * * * * /usr/bin/php /data/www/WDJF/yii crm/identity/importonline >> /dev/null 2>&1 
*/2 * * * * /usr/bin/php /data/www/WDJF/yii crm/identity/importoffline >> /dev/null 2>&1 
# crm数据补充
*/2 * * * * /usr/bin/php /data/www/WDJF/yii crm/identity/sup >> /dev/null 2>&1 
# 标的
*/1 * * * * /usr/bin/php /data/www/WDJF/yii dealcrontab/now >> /dev/null 2>&1
*/1 * * * * /usr/bin/php /data/www/WDJF/yii dealcrontab/full >> /dev/null 2>&1
*/1 * * * * /usr/bin/php /data/www/WDJF/yii dealcrontab/liu >> /dev/null 2>&1
*/1 * * * * /usr/bin/php /data/www/WDJF/yii drawcrontab/confirm >> /dev/null 2>&1
*/1 * * * * /usr/bin/php /data/www/WDJF/yii dealcrontab/ackcancelord >> /dev/null 2>&1
# 短信
*/1 * * * * /usr/bin/php /data/www/WDJF/yii smscrontab/send >> /dev/null 2>&1 
# 代金券
01 10 * * * /usr/bin/php /data/www/WDJF/yii coupon/no-order >> /dev/null 2>&1
02 10 * * * /usr/bin/php /data/www/WDJF/yii coupon/only-xs >> /dev/null 2>&1
50 08 * * * /usr/bin/php /data/www/WDJF/yii promo/send-coupon >> /dev/null 2>&1
# 邦卡
*/3 * * * * /usr/bin/php /data/www/WDJF/yii verify/bindcard >> /dev/null 2>&1
# 充值
*/1 * * * * /usr/bin/php /data/www/WDJF/yii recharge/check >> /dev/null 2>&1
*/2 19-20 * * * /usr/bin/php /data/www/WDJF/yii retention/call >> /dev/null 2>&1
# 免密
*/1 * * * * /usr/bin/php /data/www/WDJF/yii verify/mianmi >> /dev/null 2>&1
# 提现
00 10 * * * /usr/bin/php /data/www/WDJF/yii draw-notify/reminder >> /dev/null 2>&1
# 钉钉通知7天内、当天回款数
01 11,15 * * * /usr/bin/php /data/www/WDJF/yii notify/repayment-notify >> /dev/null 2>&1
# 记录用户注册来源
*/5 * * * * /usr/bin/php /data/www/WDJF/yii user-info/reg-location >> /dev/null 2>&1
# 流失客户召回
00 02 * * * /usr/bin/php /data/www/WDJF/yii add-retention >> /dev/null 2>&1

# 转让
*/5 * * * * /usr/bin/php /data/www/WDJF/yii tx/credit-note/cancel >> /dev/null 2>&1
*/5 * * * * /usr/bin/php/data/www/WDJF/yii tx/credit-order/check >> /dev/null 2>&1
# 对接南金交
0 3 * * * /usr/bin/php /data/www/WDJF/yii  njfae/njfae/init-loans >> /dev/null 2>&1
10 3 * * * /usr/bin/php /data/www/WDJF/yii njfae/njfae/init-payments >> /dev/null 2>&1
20 3 * * * /usr/bin/php /data/www/WDJF/yii njfae/njfae/init-credits >> /dev/null 2>&1

# 公众号菜单
*/5 * * * * /usr/bin/php /data/www/WDJF/yii wechat-menu/menu-del >> /dev/null 2>&1
*/5 * * * * /usr/bin/php /data/www/WDJF/yii wechat-menu/menu-add >> /dev/null 2>&1
```

# 常驻进程脚本
在 /etc/supervisor/conf.d 中加入以下文件

- QueueWorker.conf #工作队列
```
[program:queueworker]                                                                  
command=/usr/bin/php /data/www/WDJF/yii queue/worker
autostart=true
autorestart=true
startsecs=0
user=wjf
```

- baoquan.conf    #保全
```
[program:baoquan]
command=/usr/bin/php /data/www/WDJF/yii bao-quan/index
autostart=true
autorestart=true
startsecs=0 
user=wjf
```

- orderqueue.conf #订单队列
```
[program:orderqueue]
command=/usr/bin/php /data/www/WDJF/yii order/queue
autostart=true
autorestart=true
startsecs=0 
user=wjf
```

- transferqueue.conf  #现金红包
```
[program:transferqueue]
command=/usr/bin/php /data/www/WDJF/yii transfer/queue
autostart=true
autorestart=true
startsecs=0 
user=wjf
```

- credit-order-confirm.conf   #转让订单
```
[program:creditOrderConfirm]
command=/usr/bin/php /data/www/WDJF/yii tx/credit-order/confirm
autostart=true
autorestart=true
startsecs=0 
user=wjf
```