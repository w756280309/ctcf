# API接口公共约定

1. 如无特殊需求，响应以json格式返回
2. 所有请求、响应参数命名遵循 little Camel-Case (小驼峰)
3. 非标接口请求头中需要包含 Accept:application/vnd.uft.mob.legacy+json
4. 非标接口成功返回
```
[
    'code' => 0.
    'status' => 'success',
    'message' => '成功',
    'data' => [
        'code' => 0,
        'msg' => '成功',
        'result' => 'success',
        'content' =>[
            //标准响应数据
        ],
    ],
]
```
5. 非标接口失败返回
```
[
    'code' => 0,
    'status' => 'success',
    'message' => '成功',
    'data' => [
        'code' => 1,
        'msg' => '失败',//返回具体失败信息，如无明确需求，默认返回"失败"
        'result' => 'fail',
        'content' => null,
    ],
]
```
6. 标准接口成功返回
```
[
    //标准响应数据
]
```
7. 标准接口失败返回：返回异常


# 图形验证码接口

> 请求此接口可以拿到图形验证码ID及base64编码处理之后的图片数据

- 接口path：/v2/auth/captchas 
- 请求方式：POST
- 请求参数：无参数
- 响应结果：
```
captchaId   string  验证码ID
captchaData string  base64编码之后的图片
```

# 登录设置接口

> 请求此接口会返回是否需要图形验证码，如果需要图形验证码，验证码ID及base64编码处理之后的数据也会一起返回

- 接口path：/v2/auth/login-status
- 请求方式：POST
- 请求参数：无参数
- 响应结果：
    - 需要图形验证码时候的响应数据
    ```
    isCaptchaRequired   bool    是否需要验证码(1)
    captchaId           string  验证码ID
    captchaData         string  base64编码之后的图片
    ```
    - 不需要图形验证码时候的返回
    ```
    isCaptchaRequired   bool    是否需要验证码(0)
    ```
#  登录接口

> 请求此接口发起登录请求，能够拿到用于登录身份识别的auth 及 登录过期日期

- 接口path：/v2/auth/tokens 
- 请求方式：POST
- 请求参数：
```
参数名           必须     类型             含义
mobile          Y       string          用户手机号
password        Y       string          用户登录密码
captchaCode     N       string          验证码
captchaId       N       string          验证码ID
```
- 响应参数：
    - 成功登录响应
    ```
    token       string      登录身份识别token
    expireTime  dateTime    登录过期日期(Y-m-d H:i:s)
    ```
    - 登录失败且需要验证码响应
    ```
    isCaptchaRequired   bool    是否需要验证码(1)
    captchaId           string  验证码ID
    captchaData         string  base64编码之后的图片
    ```
    - 登录失败且不需要验证码
     ```
     isCaptchaRequired   bool    是否需要验证码(0)
     ```
