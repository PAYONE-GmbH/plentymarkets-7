<?php
use Monolog\Logger;

$mail = SdkRestApi::getParam('mail', true);
$subject = SdkRestApi::getParam('subject', true);
$message = SdkRestApi::getParam('message', true);

$monolog = new Logger('Mail logger');

$monolog->pushHandler(new Monolog\Handler\NativeMailerHandler(
    $mail,
    $subject,
    $message,
    Monolog\Logger::DEBUG
));

return $loger->log(Logger::DEBUG, $message);
