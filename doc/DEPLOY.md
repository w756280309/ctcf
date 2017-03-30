恢复vendor（慢）：

COMPOSER_CACHE_DIR=/data/composer-cache composer install --no-dev --prefer-dist -o

需要的扩展：

bcmath、mbstring、xml、opcache、gd、curl、mcrypt

注意事项：

7.1提示mcrypt弃用

以下未维护，暂不参考：

定时任务部署
*1.定时短信 ./yii smscrontab/send
2.定时标的上线 ./yii dealcrontab/now
3.定时标的满标 ./yii dealcrontab/full
4.定时标的流标 ./yii dealcrontab/liu
*5.定时发起结算 ./yii settlementcrontab/launch
*6.定时更新结算 ./yii settlementcrontab/update 【debug 开启调试模式】
*7.定时发起充值结果检测 ./yii brechargecrontab/launch
*8.定时发起批量代付 ./yii batchpaycrontab/launch
*9.定时更新批量代付 ./yii batchpaycrontab/update 【debug 开启调试模式】 可以在11时-15时执行
10.定时获取中金对账单 ./yii checkaccrontab/cfca 【debug 开启调试模式】 凌晨五时出对账单一次
11.定时获取温都金服交易数据 ./yii checkaccrontab/wdjf 【debug 开启调试模式】 可以在凌晨获取一次
12.定时获取比较中金温都数据 ./yii checkaccrontab/compare 【debug 开启调试模式】 依赖于中金对账单
13.定时获取汇总 ./yii checkaccrontab/hz  【debug 开启调试模式】 依赖于比较
注意：
1)./yii 代表项目路径
2).*标：通过Supervisord让部分脚本持续保持运行（脚本本身运行较短时间后就会退出）同时控制脚本运行实例之间的间隔（比如3s，避免执行过多的短时间运行的脚本）
3).10-13项，第12【比较】基于10【中金对账单】和11【温对账单】的数据，13【汇总】基于12【比较】

应确保/common/config/payment/ump/目录下有对应的证书文件，生产环境为*_prod.crt/key，开发、测试环境为*_dev.crt/key
应确保/common/config/params-local.php里配置了有效的参数，包括回调地址、商户号等（生产环境与开发、测试环境不同）
项目根目录下需要添加第三方的PHP代码，可以用composer安装，或者手工上传（症状：网站不能访问）
frontend项目下assets下需要添加第三方的js/css文件，可以用bower安装，或者手工上传（症状：PC端缺js/css）

14.已有SQL不允许修改,db目录下存的都是新增的SQL语句

添加站点：lstatic.wenjf.com（所有流量走CDN）
/m/* 根目录指向 web01的/path/to/WDJF/wap/web
/pc/* 根目录指向 web01的/path/to/WDJF/frontend/web
/promo/* 根目录指向 cache01的/path/to/PROMO

例如 static.wenjf.com/m/upload/adv/xxxx 可以访问到 m.wenjf.com/upload/adv/xxx

cdn已经上线，具体信息如下：

- wap站的图片与css,js访问方式如下： http://static.wenjf.com/m/images/tubiao.png
- pc站的图片与css,js访问方式如下： http://static.wenjf.com/pc/js/common.js
- 后台上传的图片访问地址为： http://static.wenjf.com/upload/adv/c636f7c79be7672c44b5535504fe686d.jpg
- 活动页面的访问方式： http://static.wenjf.com/promo
