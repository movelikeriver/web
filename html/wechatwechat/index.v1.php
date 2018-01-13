<?php
    define ( "TOKEN", "helloczq" );
    $wechatObj = new wechat ();
    $wechatObj->valid ();
    $wechatObj->responseMsg ();
    class wechat {
        public function valid() {
          $echoStr = $_GET ["echostr"];
          if (! $this->checkSignature ())
            exit ();
        }
        public function responseMsg() {
          $postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
          if (! emptyempty ( $postStr )) {
            $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $content = trim ( $postObj->Content );
            $time = time ();
            $talk = new talk();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            $msgType = "text";
            $contentStr = $talk->reply($content);
            $resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
            echo $resultStr;
          } else {echo $_GET ["echostr"];
            exit ();
          }
        }
        private function checkSignature() {
          $signature = $_GET ["signature"];
          $timestamp = $_GET ["timestamp"];
          $nonce = $_GET ["nonce"];
          $token = TOKEN;
          $tmpArr = array (
                $token,
                $timestamp,
                $nonce
          );
          sort ( $tmpArr );
          $tmpStr = implode ( $tmpArr );
          $tmpStr = sha1 ( $tmpStr );
          
          if ($tmpStr == $signature) {
            return true;
          } else {
            return false;
          }
        }
    }
    class talk {
        public function reply($str) {
          $kv = new SaeKV ();
          $kv->init ();
          if ($str == 'help' || $str == '求助'){
            return "要教我读书，请英文下划线开头，接着问题，接着英文冒号，接着回答";
          }
          if (substr($str, 0,1) == '_'){
            $pos = strpos($str, ':');
            if ($pos > -1){
                $q = substr($str, 1,$pos - 1);
                $a = substr($str, $pos + 1);
                $ret = $kv->get('know_' . md5($q));
                if ($ret === false || !is_array($ret))
                  $ret = array();
                $ret[] = $a;
                $kv->set('know_' . md5($q), $ret);
                return "known::" . $q . '/' . $a ;
            }
          }
          $ret = $kv->get('know_' . md5($str));
          if ($ret === false　|| !is_array($ret) || count($ret) == 0){
            return '我什么都不知道,输入"help"求助';
          }else{
            //随机一个
            while(count($ret) > 1){
                $re = array_shift($ret);
                if (rand(0, 1) == 0)
                  return $re;
            }
            return array_shift($ret);
          }
        }
    }
    ?> 