<?php
/**
  * wechat php test
  */

function l($echoStr) {
     file_put_contents("/tmp/fastcgi.log", "$echoStr \n", FILE_APPEND);  
}


l("start....");


//define your token
define("TOKEN", "ceyixia");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{
    public function valid() {
        $echoStr = $_GET["echostr"];

        l("valid( $echoStr )");

        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        } else {
            l("checkSignature failed");
        }
    }

    public function responseMsg() {
       //get post data, May be due to the different environments
       $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

       l("postStr is:( $postStr )");

       //extract post data
       if (!empty($postStr)){
           $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
           $fromUsername = $postObj->FromUserName;
           $toUsername = $postObj->ToUserName;
           $keyword = trim($postObj->Content);
           $time = time();
           $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";             
           if(!empty( $keyword )) {
               l(" !empty( $keyword ) ");
               $msgType = "text";
               $contentStr = "Welcome to wechat world!";
               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
               echo $resultStr;
           } else {
               l("===== 2 ======");
               echo "Input something...";
           }
       } else {
            echo "";
            exit;
       }
    }
		
    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	

        l("=====  $signature  $timestamp  $nonce  ======");

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        l("tmpStr:  $tmpStr ");

        if( $tmpStr == $signature ){
            return true;
        } else {
            return false;
        }
    }
}

?>