<?php

namespace Controller;

use Response\TextMsgResponse;

class WeatherController extends AbstractController
{
    protected $cityId;

    protected $content;

    public function __construct($cityId)
    {
        $this->cityId = $cityId;
    }

    public function getWeatherText()
    {
        $weekList = array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六');

        try {
           $urlResponse = $this->getUrlResponse('http://m.weather.com.cn/data/'.$this->cityId.'.html');
        } catch (Exception $ex) {
            return $ex->getMessagea();
        }

        $day = array();

        for ($i = 1; $i < 6; $i++) {
            $day[$i] = date('m月d日', mktime(0, 0, 0, date('m'), date('d')+$i, date('Y')));
        }

        $today = date('Y年m月d');

        $weather = $urlResponse->weatherinfo;

        if ($weather->date_y == $day[1]) {
            $content = '『'. $weather->city .'』'. $weather->date_y . $weather->week . '，天气情况：\n';
        } else {
            $content = '『'. $weather->city .'』'. $today . $weekList[date('w')] . '，天气情况：\n';
        }

        $content .= $day[1] .'：'. $weather->temp1 .'，'. $weather->weather1 .'；/n'. $day[2] .'：'. $weather->temp2 .'，'. $weather->weather2 .'；\n'. $day[3] .'：'. $weather->temp3 .'；'. $weather->weather3 .'；\n'. $day[4] .':'. $weather->temp4 .'，'. $weather->weather4 .'；\n 回复帮助获得更多信息';

        $this->content = $content;

        return $this;
    }

    public function postWeatherText($postObj)
    {
        $msgType = 'text';

        $response = new TextMsgResponse($postObj, $msgType);

        $response->setContents($this->content);

        return $response->send();
    }
}
