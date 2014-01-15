<?php

use Controller\DispacherController;
use Controller\CheckSignatureController;

date_default_timezone_set('Asia/Hong_Kong');

include_once(__DIR__.'/vendor/autoload.php');

$postStr = $GLOBALS['HTTP_RAW_POST_DATA'];

$head = $_GET;

if (array_key_exists('echostr', $head)) {
    $checkSignature = new CheckSignatureController($conf['token']);

    echo $checkSignature->valid($head);

    exit;
}

if (empty($postStr)) {
    exit;
}

$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

$dispacher = new DispacherController($conf, $postObj);

$event = $dispacher->getEvent();

$response = $event->setContent()->setResponse()->getResponse();
