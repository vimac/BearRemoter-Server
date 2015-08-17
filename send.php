<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;
use JPush\JPushClient;
use JPush\Model as M;

$fromCellPhone = isset($_REQUEST['cellPhone']) ? $_REQUEST['cellPhone'] : "";
$nickname = isset($_REQUEST['nickname']) ? $_REQUEST['nickname'] : "匿名";
$targetCellPhone = isset($_REQUEST['targetCellPhone']) ? $_REQUEST['targetCellPhone'] : "";
$message = isset($_REQUEST['message']) ? $_REQUEST['message'] : "召唤";

if (empty($targetCellPhone) or empty($fromCellPhone)) {
    die("nothing else comes so close");
}
$target = $prefix . $targetCellPhone;

$br = '<br/>';
$spilt = ' - ';

//JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
$client = new JPushClient($app_key, $master_secret);

// 以下演示推送给 Android, IOS 平台下Tag为tag1的用户的示例
try {
    $result = $client->push()
        ->setPlatform(M\Platform('ios'))
        ->setAudience(M\Audience(M\alias([$target])))
        ->setNotification(M\notification('来自' . $nickname . '的指令:' . $message,
//            M\android('Hi, Android', 'Message Title', 1, array("key1"=>"value1", "key2"=>"value2")),
            M\ios('来自' . $nickname . '的指令:' . $message, "happy", "+1", true, [
                "from" => $nickname, "message" => $message
            ], "BearRemoter")
        ))
//        ->setMessage(M\message($message , '来自' . $nickname . '的指令', null, []))
        ->printJSON()
        ->send();
    echo 'Push Success.' . $br;
    echo 'sendno : ' . $result->sendno . $br;
    echo 'msg_id : ' . $result->msg_id . $br;
    echo 'Response JSON : ' . $result->json . $br;
} catch (APIRequestException $e) {
    echo 'Push Fail.' . $br;
    echo 'Http Code : ' . $e->httpCode . $br;
    echo 'code : ' . $e->code . $br;
    echo 'Error Message : ' . $e->message . $br;
    echo 'Response JSON : ' . $e->json . $br;
    echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
    echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
    echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
} catch (APIConnectionException $e) {
    echo 'Push Fail: ' . $br;
    echo 'Error Message: ' . $e->getMessage() . $br;
    //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
    echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
}
