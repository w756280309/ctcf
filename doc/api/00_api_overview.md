Api接口：

1. 输出格式，统一为json格式；
2. 输出样式，如下所示：

{
    "status": "success",//程序级别成功失败
    "message": "成功",
    "data": {
        "result": "success",//业务级别成功失败
        "msg": "成功",
        "content": {
            "version_code": "1",
            "version_name": "1.0.0",
            "update_content": "测试更新",
            "update_type": 1
        }
    }
}

{
    "status": "fail",//程序级别成功失败
    "message": "需要登录",
    "data": null
}

3. 输出项如果没有，统一为null；
4. 金额统一原生输出，并以字符串的形式；
5. 身份证号，银行卡号输出需要隐藏部分内容；
6. 版本更新接口调用，采用get的方式请求，输入版本号，相关版本信息目前直接配置在接口当中，后期可以参照旺财谷的做法，将配置信息配置在后台数据库中；


API接口需要写一份对应的接口文档，样本链接如下：
http://dx.wangcaigu.cn/w/旺财谷/开发文档/7、app我的资产接口/
