<?php

//require_once 'vendor/autoload.php';
use Monolog\Logger;

$mail = SdkRestApi::getParam('mail', true);
$subject = SdkRestApi::getParam('subject', true);
$message = SdkRestApi::getParam('message', true);

$slack = SdkRestApi::getParam('slack', true);

$monolog = new Logger('Mail logger');

if ($slack) {
    $monolog->pushHandler(new Monolog\Handler\SlackHandler(
        $slack['token'],
        $slack['channel'],
        $slack['user'],
        'true',
        null,
        Monolog\Logger::DEBUG
    ));
}

$monolog->pushHandler(new Monolog\Handler\NativeMailerHandler(
    $mail,
    $subject,
    $message,
    Monolog\Logger::DEBUG
));

return $monolog->log(Logger::DEBUG, $message);
