<?php
use Response\NewsMsgResponse;
use Controller\WeatherController;

include_once(__DIR__.'/vendor/autoload.php');
$postObj = (object) [
    'FromUserName' => 'UserName:archy',
    'ToUserName' => 'HostName:hbu',
];

$content = '这是一次测试';

//$textMsg = new TextMsgResponse($postObj, 'text');

//echo $textMsg->setContents($content)->send();

$articles['1']['title'] = 'test';
$articles['2']['description'] = '这是一次测试测试';
$articles['1']['picUrl'] = '#';
$articles['1']['url'] = '#';

//$newsMsg = new NewsMsgResponse($postObj, 'news');

//echo $newsMsg->setArticles($articles)->send();a

$weather = new WeatherController('101010100');

$weather->getWeatherText();
echo $weather->postWeatherText($postObj);
