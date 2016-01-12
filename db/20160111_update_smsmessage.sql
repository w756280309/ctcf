alter table `sms_message` 
   change `message` `message` varchar(1000) character set utf8 collate utf8_general_ci NOT NULL comment '短信模板拼接内容,json';