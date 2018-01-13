<?php

function l($echoStr) {
     echo "logged: $echoStr";
     file_put_contents("/tmp/fastcgi.log", "$echoStr \n", FILE_APPEND);  
}

echo "ok";
$echoStr = $_GET["echostr"];
l($echoStr);

?>