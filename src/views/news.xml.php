<?php

$article = <<<'TEMP'
    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
TEMP;

$newsArticle = null;

if (isset($articleTitle) && isset($articleDescription) && isset($articlePicurl) && isset($articleUrl)) {
    foreach ($articleTitle as $key => $val) {
        $newsArticle .= sprintf($article, $val, $articleDescription[$key], $articlePicurl[$key], $articleUrl[$key]);
    }
} else {
    $newsArticle = sprintf($article, 'Sorry', '对不起哦，发生了一些未知的错误', '#', '#');
}

$news = <<<'TEMP'
    <xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>%s</Articles>
        <FuncFlag>1</FuncFlag>
    </xml>
TEMP;

$newsResponse = sprintf($news, $this->userName, $this->hostName, time(), $this->msgType, count($articleTitle), $newsArticle);

return $newsResponse;
