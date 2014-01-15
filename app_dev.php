<?php
use Response\NewsMsgResponse;
use Event\WeatherEvent;
use Controller\CheckSignatureController;
use Controller\DispacherController;

include_once(__DIR__.'/vendor/autoload.php');
$postObj = (object) [
    'FromUserName' => 'UserName:archy',
    'ToUserName' => 'HostName:hbu',
    'content' => '天气',
];

$content = '这是一次测试';

$head = $_GET;

//$textMsg = new TextMsgResponse($postObj, 'text');

//echo $textMsg->setContents($content)->send();

$articles['1']['title'] = 'test';
$articles['2']['description'] = '这是一次测试测试';
$articles['1']['picUrl'] = '#';
$articles['1']['url'] = '#';

//$newsMsg = new NewsMsgResponse($postObj, 'news');

//echo $newsMsg->setArticles($articles)->send();a

//$weather = new WeatherEvent($conf, $postObj);

//$weather->setContent()->setResponse();

//$checkSignature = new CheckSignatureController($conf['token']);

//echo $weather->getResponse();

//echo $checkSignature->valid($head);a

$dispacher = new DispacherController($conf, $postObj);

$event = $dispacher->getEvent();

$response = $event->setContent()->setResponse()->getResponse();
