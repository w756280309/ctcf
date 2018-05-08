<?php

namespace common\models;

/*
 * 短信验证码接口
 * 2014/06/25
 * Aaron
 * 使用方法
 * 1    use common\models\Sms;
 * 2    $sms = new Sms();
 *      $data = $sms->sendTemplateSMS('15810036547',array('0000',5),12552);
 */
class Sms
{
	private $AccountSid;             //主账户id
	private $AccountToken;            //主账户授权令牌
	private $AppId = '';                  //应用id
	private $ServerIP= 'app.cloopen.com';                                 //服务器地址
	private $ServerPort= '8883';                                          //服务器端口
	private $SoftVersion= '2013-12-26';                                   //API版本
	private $Batch;  //时间戳
	private $BodyType = "xml";//包体格式，可填值：json 、xml
	private $enabeLog = true; //日志开关。可填值：true、
	private $Filename; //日志文件
	private $Handle;

	public function __construct()
	{
        $this->AppId = \Yii::$app->params['sms']['config']['APP_ID'];
        $this->AccountSid = \Yii::$app->params['sms']['config']['ACCOUNT_SID'];
        $this->AccountToken = \Yii::$app->params['sms']['config']['AUTH_TOKEN'];
        $this->Filename = dirname(dirname(__DIR__)).'/data/log/sms.log.txt';
		$this->Batch = date('YmdHis');	//时间戳
        $this->Handle = fopen($this->Filename, 'a');
	}

   /**
    * 打印日志
    *
    * @param log 日志内容
    */
    function showlog($log)
    {
        if ($this->enabeLog) {
            fwrite($this->Handle, $log."\n");
        }
    }

    /**
     * 发起HTTPS请求
     */
     function curl_post($url, $data, $header, $post = 1)
     {
       //初始化curl
       $ch = curl_init();
       //参数设置
       $res= curl_setopt ($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
       curl_setopt($ch, CURLOPT_POST, $post);
       if($post)
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
       $result = curl_exec ($ch);
       //连接失败
       if($result == FALSE){
          if($this->BodyType=='json'){
             $result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
          } else {
             $result = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>";
          }
       }

       curl_close($ch);
       return $result;
     }

   /**
    * 发送模板短信
    * @param to 短信接收手机号码集合,用英文逗号分开
    * @param datas 内容数据
    * @param $tempId 模板Id
    */
    function sendTemplateSMS($to, $datas, $tempId)
    {
        // 拼接请求包体
        if($this->BodyType=="json"){
           $data="";
           for($i=0;$i<count($datas);$i++){
              $data = $data. "'".$datas[$i]."',";
           }
           $body= "{'to':'$to','templateId':'$tempId','appId':'$this->AppId','datas':[".$data."]}";
        }else{
           $data="";
           for($i=0;$i<count($datas);$i++){
              $data = $data. "<data>".$datas[$i]."</data>";
           }
           $body="<TemplateSMS>
                    <to>$to</to> 
                    <appId>$this->AppId</appId>
                    <templateId>$tempId</templateId>
                    <datas>".$data."</datas>
                  </TemplateSMS>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/SMS/TemplateSMS?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
           $datas=json_decode($result);
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
      //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        //重新装填数据
        if($datas->statusCode==0){
         if($this->BodyType=="json"){
            $datas->TemplateSMS =$datas->templateSMS;
            unset($datas->templateSMS);
          }
        }

        return $datas;
    }
 }