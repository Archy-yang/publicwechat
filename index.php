<?php
/**********************设定时区为香港*******************/
date_default_timezone_set('Asia/Hong_Kong');  

/**********************设定"TOKEN"认证**********************/

define("TOKEN", "qige");

/***************根据实际应用做相应的更改********************/
define ("CityText" ,"保定");
define ("HelpText", "欢迎来到河北大学公共平台，回复“帮助”获得更多信息");
define ("ConcernText","欢迎您关注河大微信,试着回复 天气 看看~");
define ("SchNum","21001"); //为学校在FACEJOKING上的代码,此代码为；廊坊师范学院代码
/********************连接数据库********************/

$link=mysql_connect("183.90.189.90","yangqidream","yangqidream");
mysql_select_db("yangqidream",$link);
mysql_query("set names utf8");//设置中文语言格式为 “utf-8”

/*********************连接IP跟踪*********************/

traceHttp();

/*******************数据接收、处理及发送****************/

$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

/****************关闭数据库连接***********************/

mysql_close($link);

class wechatCallbackapiTest
{	
    public function responseMsg()
    {
		$time=time();
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//获取微信服务器POST的数据
		
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		
		if (!empty($postStr))
		{
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			$userNum=$postObj->FromUserName;
			$RX_TYPE=trim($postObj->MsgType);
			
			$userNumDB=mysql_query("SELECT * FROM gx_hbu WHERE user_Id ='$userNum'" );//这里请自行选择项目的数据库名称 gx_hbu 为河北大学的数据库
			$db_user_info=mysql_fetch_object($userNumDB);
			
			$infoMsg=trim($postObj->Content);
			if($db_user_info&&(trim($infoMsg)=="帮助"||trim($infoMsg)=="天气"||trim($infoMsg)=="温度"||trim($infoMsg)=="查分"||trim($infoMsg)=="校花"||trim($infoMsg)=="校草"||trim($infoMsg)=="留言"||trim($infoMsg)=="运动会"||trim($infoMsg)=="夜夜谈"))
			{
				mysql_query("DELETE FROM gx_hbu WHERE user_Id='$userNum'");
			}
			
			$userNumDB=mysql_query("SELECT * FROM gx_hbu WHERE user_Id ='$userNum'" );//这里请自行选择项目的数据库名称 gx_hbu 为河北大学的数据库
			$db_user_info=mysql_fetch_object($userNumDB);
			/********对用户ID进行判断*********/
			
			if ($db_user_info)
			{
				$userTime=$db_user_info->user_LoginTime;
				$time=time();
				
				/**********对用户登录时间间隔进行判断***********/
				
				if(($time-$userTime)>300)
				{	
					mysql_query("DELETE FROM gx_hbu WHERE user_Id ='$userNum'");
                    $contents="我们等待你的回复已经等到花儿都谢了，所以你就回到主页面了哦，回复“帮助”获得更多信息吧~~！";
					$resultStr = $this->sendMsg($postObj,"text",$contents);
					echo $resultStr;
				}
				if(($time-$userTime)<=300)
				{
					$userMode=$db_user_info->user_Mode;//获取用户此时所处的模式
					
					switch ($userMode)
					{
					case 1:	
						$info_c=$postObj->Content;
						switch ($info_c)
						{
						case 1:	
							mysql_query("UPDATE gx_hbu SET user_Mode='101',user_LoginTime='$time' WHERE user_Id='$userNum'");
							$contents = "告诉老娘你的姓名；\n 回复“退出”可退出查分模式。";
							$resultStr = $this->sendMsg($postObj,"text",$contents);
							echo $resultStr;
							break;
						case 2:
							//mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$contents="计算机成绩官方还未公布，公布后我们会尽快提供查询功能。";
							$resultStr = $this->sendMsg($postObj,"text",$contents);
							echo $resultStr;
							break;
						case "退出":
							mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$contentsInfo=$HelpText;
							$resultStr = $this->sendMsg($postObj,"text",$contentsInfo);
							echo $resultStr;
							break;
						}
						break;
					case 101:
						$info_c=$postObj->Content;
						switch($info_c)
						{
						case "退出":
							mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$contentsInfo=$HelpText;
							$resultStr = $this->sendMsg($postObj,"text",$contentsInfo);
							echo $resultStr;
							break;
						case 2:
							mysql_query("UPDATE gx_hbu SET user_Mode='1' WHERE user_Id='$userNum'");
							$contents="计算机成绩官方还未公布，公布后我们会尽快提供查询功能。";
							$resultStr = $this->sendMsg($postObj,"text",$contents);
							echo $resultStr;
							break;
						default:
							mysql_query("UPDATE gx_hbu SET user_Mode='102',user_Name='$info_c',user_LoginTime='$time'  WHERE user_Id='$userNum'");
							$contents = "输准考证号哦（不许输学号）；\n 回复“退出”可退出查分模式。";
							$resultStr = $this->sendMsg($postObj,"text",$contents);
							echo $resultStr;
							break;
						}
						break;
					case 102:
						$info_c=$postObj->Content;
						$info_d=$db_user_info->user_Name;
						$contentsInfo=$this->CET_4_6(trim($info_d),trim($info_c));
						$resultStr = $this->sendMsg($postObj,"text",$contentsInfo)."\n \n直接输入姓名继续查询或者输入“退出”退出此查询模式";
						mysql_query("UPDATE gx_hbu SET user_Mode='1',user_Name = '' WHERE user_Id='$userNum'");
						echo $resultStr;
						break;
					/*case 2:
						mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
						$contents="计算机成绩官方还未公布，公布后我们会尽快提供查询功能。";
						$resultStr = $this->sendMsg($postObj,"text",$contents);
						echo $resultStr;
						break;*/
					case 40:
						$info_c=$postObj->Content;
						if ($info_c>=1&&$info_c<=10)
						{
							$Fmode=0;
							$MsgType="news";
							$this->FACEJOKING($postObj,$Fmode,$MsgType,$info_c);
						}
						elseif($info_c=="退出")
						{
							mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$MsgType="text";
							$contents = $HelpText;
							$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
							echo $resultStr;
						}
						else
						{
							//mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$info_c=urlencode($info_c);
							$url="http://www.facejoking.com/api/search/".$SchNum."/0/".$info_c."/1";
							$html=file_get_contents($url);
							$info_s=json_decode($html);
							$info_s_data=$info_s->data;
							if($info_s_data)
							{
								$this->FACEJOKING_S($postObj,"news","1",$info_s_data);
							}
							if(empty($info_s_data))
							{
								$MsgType="text";
								$contents = "没找到你要找的人哦\n <a href=\"http://www.facejoking.com/upload/".$SchNum."/0\">点击我上传她的照片吧</a>";
								$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
								echo $resultStr;
							}
						}
						break;
					case 41:
						$info_c=$postObj->Content;
						if ($info_c>=1&&$info_c<=10)
						{
							$Fmode=1;
							$MsgType="news";
							$this->FACEJOKING($postObj,$Fmode,$MsgType,$info_c);
						}
						elseif($info_c=="退出")
						{
							mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$MsgType="text";
							$contents = $HelpText;
							$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
							echo $resultStr;
						}
						else
						{
							//mysql_query("DELETE FROM gx_hbu WHere user_Id ='$userNum'");
							$info_c=urlencode($info_c);
							$url="http://www.facejoking.com/api/search/".$SchNum."/1/".$info_c."/1";
							$html=file_get_contents($url);
							$info_s=json_decode($html);
							$info_s_data=$info_s->data;
							if($info_s_data)
							{
								$this->FACEJOKING_S($postObj,"news","1",$info_s_data);
							}
							if(empty($info_s_data))
							{
								$MsgType="text";
								$contents = "没找到你要找的人哦\n <a href=\"http://www.facejoking.com/upload/".$SchNum."/0\">点击我上传他的照片吧</a>";
								$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
								echo $resultStr;
							}
						}
						break;
					case 6:
						$info_c=$postObj->Content;
						$info=mysql_query("SELECT * FROM gx_sportinfo");
						$i=0;
						$info_db=array();
						$info_db_info=array();
						while($info1=mysql_fetch_array($info))
						{
							$info_db[$i]=$info1["sport_Name"];
							$info_db_info[$i]=$info1["sport_Info"];
							$i++;
						}
						$MsgType="text";
						if(trim($info_c)>0&&trim($info_c)<=$i)
						{
							$contents=$info_db[trim($info_c-1)]."的信息如下：\n".$info_db_info[trim($info_c-1)];
						}
						else
						{
							$contents="输入错误";
						}
						$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
						echo $resultStr;
						break;
					}
				}
			}
			if(empty($db_user_info))
			{
			switch ($RX_TYPE)
			{
				case "text":
					$this->getTextTypeInfo($postObj);
					break;
				case "event":
					$eventInfo=trim($postObj->Event);
					if ($eventInfo=="subscribe")
					{
						$MsgType="text";
						$contents = $ConcernText;
						$resultStr = $this->sendMsg($postObj,$MsgType,$contents);
						echo $resultStr;
					}
					break;
			}
			}
        }else {
        	echo "";
        	exit;
        }
    }
	
	public function getTextTypeInfo($postObj)
	{
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		$time=time();
		$userNum=$postObj->FromUserName;
		$infoMsg=trim($postObj->Content);
		if(!empty($infoMsg))
		{	
			$infoMsgType=explode(" ",$infoMsg);
			if (trim($infoMsgType[0])=="帮助")
			{
				$MsgType="text";
				$contentsInfo="回复“社区”可点击进入河大微社区;\n回复“查分”可查询全国英语四、六考试；\n 回复“天气”可以得到本地天气；\n 回复“校花”或“校草”查看本校的白富美、高富帅（信息来自FaceJoking）；\n  回复 “留言  内容”与我们沟通";
			}
			elseif (trim($infoMsgType[0])=="天气"||trim($infoMsgType[0])=="温度")
			{	
				$MsgType="text";
				if (!empty($infoMsgType[1]))
					$contentsInfo=$this->weather(trim($infoMsgType[1]));
				else
					$contentsInfo=$this->weather($CityText);

			}
			elseif (trim($infoMsgType[0])=="留言")
			{	
				$MsgType="text";
				$contentsInfo="您的留言已收到，我们会尽快人工回复，期待您分享您在河大的生活故事。";
			}
			elseif(trim($infoMsgType[0])=="CET"||trim($infoMsgType[0])=="cet")
			{	
				$MsgType="text";
				$contentsInfo=$this->CET_4_6(trim($infoMsgType[1]),trim($infoMsgType[2]));
			}
			elseif (trim($infoMsgType[0])=="校花")
			{
				mysql_query("INSERT INTO gx_hbu (user_ID,user_Mode,user_LoginTime)VALUE('$userNum',40,'$time')");
				$MsgType="text";
				$contentsInfo="请选择\n 1、TOP 1-10 \n 2、TOP 11-20\n 3、TOP 21-30 \n 4、TOP 31-40 \n 5、TOP 41-50 \n 6、TOP 51-60 \n 7、TOP 61-70 \n 8、TOP 71-80 \n 9、TOP 81-90 \n 10、TOP 91-100 \n 回复序号即可。也可以直接输入姓名进行查询（此处只可查询校花哦）。回复“退出”退出校花浏览与查询。";			
			}
			elseif(trim($infoMsgType[0])=="校草")
			{
				mysql_query("INSERT INTO gx_hbu (user_ID,user_Mode,user_LoginTime)VALUE('$userNum',41,'$time')");
				$MsgType="text";
				$contentsInfo="请选择\n 1、TOP 1-10 \n 2、TOP 11-20\n 3、TOP 21-30 \n 4、TOP 31-40 \n 5、TOP 41-50 \n 6、TOP 51-60 \n 7、TOP 61-70 \n 8、TOP 71-80 \n 9、TOP 81-90 \n 10、TOP 91-100 \n 回复序号即可。也可以直接输入姓名进行查询（此处只可查询校草哦）。回复“退出”退出校花浏览与查询。";
			}
			elseif (trim($infoMsgType[0])=="查分")
			{
				mysql_query("INSERT INTO gx_hbu (user_ID,user_Mode,user_LoginTime)VALUE('$userNum',1,'$time')");
				$MsgType="text";
				$contentsInfo="请选择所查类型：\n 1、英语四、六级考试 \n 2、计算机等级考试 \n 直接回复序号即;\n 回复“退出”可退出查分模式。";
				
			}
			elseif(trim($infoMsgType[0])=="运动会")
			{
				mysql_query("INSERT INTO gx_hbu (user_ID,user_Mode,user_LoginTime)VALUE('$userNum',6,'$time')");
				$info=mysql_query("SELECT * FROM gx_sportinfo");
				$i=0;
				$info_db=array();
				while($info1=mysql_fetch_array($info))
				{
					$info_db[$i]=$info1["sport_Name"];
					$i++;
				}
				$MsgType="text";
				for($j=0;!empty($info_db[$j]);$j++)
				{
					$contents.="\n".($j+1)."、".$info_db[$j];
				}
				$contentsInfo="请选择比赛科目：".$contents."\n 回复科目前序号即可";
			}
			elseif (trim($infoMsgType[0])=="夜夜谈")
			{
				$this->yeyetan($postObj);
			}
			elseif(trim($infoMsgType[0]) === "社区"){
				$MsgType="text";
				$contentsInfo="<a href=\"http://quan.qgc.qq.com/151203565\">点击进入河大微社区</a>";
			}
			else
			{	
				$MsgType="text";
				$contentsInfo=$HelpText;
			}
		}
		else
		{
			$contentsInfo="请输入";
		}
		if ($MsgType=="text")
		{	
			$resultStr = $this->sendMsg($postObj,$MsgType,$contentsInfo);
			echo $resultStr;
		}
	}
	/******************/
	//函数功能：将受到的信息经过处理后按格式显示在页面上
	//******************/
	public function sendMsg($postObj,$MsgType,$contents)
	{
        $CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";  	
		$resultStrInfo = sprintf($textTpl, $postObj->FromUserName,$postObj->ToUserName, time(), $MsgType, $contents);
		return $resultStrInfo;
	}
	
    /*************************/
	//函数功能：获取天气信息并返回字符串格式的天气信息
	/**************************/
		
	public function weather($keywode)
	{
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		$city=array("北京","石家庄","保定","邯郸","廊坊","呼和浩特");
		$code=array("101010100","101090101","101090201","101091001","101090601","101080101");
		for($i=0;$i<6;$i++)
		{
			if (!strcmp($keywode,$city[$i]))
			$keywode=$code[$i];
		}
		$week=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
		$url="http://m.weather.com.cn/data/".$keywode.".html";
		$getMsg=file_get_contents($url);
		$get_php_Msg=json_decode($getMsg,true);
		$info=$get_php_Msg['weatherinfo'];
		$day=date("m月d日",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$day_1=date("m月d日",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$day_2=date("m月d日",mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
		$day_3=date("m月d日",mktime(0, 0, 0, date("m")  , date("d")+3, date("Y")));
		$day_4=date("m月d日",mktime(0, 0, 0, date("m")  , date("d")+4, date("Y")));
		$day_5=date("Y年m月d");
		if ($day==$info['date_y'])
		{
			$contentStr="【".$info['city']."】".$info['date_y'].$info['week'].
					"，天气情况：\n".$day.":".$info['temp1']."，".$info['weather1']."； \n ".$day_1."：".$info['temp2']."，".$info['weather2']."；\n  ".$day_2."：".$info['temp3']."，".$info['weather3']."； \n ".$day_3."：".$info['temp4']."，".$info['weather4']."；\n 回复 帮助 获得更多信息";
		}
		if($day!=$info['date_y'])
		{
			$contentStr="【".$info['city']."】".$day_5.$week[date("w")].
					"，天气情况：\n".$day.":".$info['temp1']."，".$info['weather1']."； \n ".$day_1."：".$info['temp2']."，".$info['weather2']."；\n  ".$day_2."：".$info['temp3']."，".$info['weather3']."； \n ".$day_3."：".$info['temp4']."，".$info['weather4']."；\n 回复 帮助 获得更多信息";
		}
		return $contentStr;
	}
	
	/*****************************/
	//函数功能：获取四六级成绩并返回字符串格式的考试信息
	/*****************************/
	function CET_4_6($name,$numb)
	{
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		$server = 'www.chsi.com.cn';
		$host = 'www.chsi.com.cn';
		$target = '/cet/query?zkzh='.$numb.'&xm='.$name;
		$referer = 'http://www.chsi.com.cn/cet/'; // Referer
		$port = 80;
		$fp = fsockopen($server, $port, $errno, $errstr, 30);
		if (!$fp) 
		{
			echo "$errstr ($errno)<br />\n";
		} 
		else 
		{
			$out = "GET $target HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Cookie: SESSIONID=test\r\n";
			$out .= "Referer: $referer\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			while (!feof($fp)) 
			{ 
				$html .=fgets($fp, 128);
			}
			fclose($fp);
		}
		$ereg="全国大学英语四、六级考试成绩查询结果";
		$ereg1="CET分数解释";
		$arr_str=explode($ereg,$html);
		$arr_str1=explode($ereg1,$arr_str[1]);
		$arr_str2=strip_tags($arr_str1[0]);
		$arr_str3=explode("	",$arr_str2);
		for($i=0;$i<=34;$i++)
		{
			if(!empty($arr_str3[$i]))
			{
				$arr_str4.=ltrim($arr_str3[$i]);
			}
		}
		$arr_str33=explode("&nbsp;",$arr_str3[35]);
		for ($j=0;$j<=8;$j++)
		{
			if(!empty($arr_str33[$j]))
			{
				$arr_str4.=trim($arr_str33[$j]);
			}
		}
		echo "<br/>";
		return $arr_str4."\n 回复 帮助 获得更多信息";
	}
	
	/*****************************************/
	//函数功能：获取校花校草信息
	/*****************************************/
	public function FACEJOKING($postObj,$Fmode,$MsgType,$Fnum)
	{
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;  
		$Furl="http://www.facejoking.com/api/top/".$SchNum."/".$Fmode."/1";
		

		$info=file_get_contents($Furl);
		$info=json_decode($info);
		$info=$info->data;
		$FnewsTp1="<item>
				<Title><![CDATA[%s]]></Title> 
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>";
		for($i=($Fnum-1)*10;!empty($info[$i])&&$i<$Fnum*10;$i++)
		{
				$Title="TOP".($i+1).$info[$i]->name;
				$Desctiption=$info[$i]->name;
				$PicUrl="http://www.facejoking.com/pic/".$info[$i]->pid.".jpg";
				$Url="http://www.facejoking.com/people/".$info[$i]->pid;
				$FresultStrInfo.= sprintf($FnewsTp1,$Title,$Desctiption,$PicUrl,$Url);
		}
		
		//file_put_contents("log.html","$i de zhi wei ".$i."<br/>",FILE_APPEND);
		
		$i=$i-($Fnum-1)*10;
		
		$newsTpl=" <xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".$i."</ArticleCount>
					<Articles>".$FresultStrInfo."</Articles>
					<FuncFlag>1</FuncFlag>
					</xml>";
		if(!empty($info[($Fnum-1)*10]))
		{
			$resultStrInfo=sprintf($newsTpl,$postObj->FromUserName,$postObj->ToUserName, time(), $MsgType);
			echo $resultStrInfo;
		}
		else
		{
			if($Fmode==0)
			{
				$MsgType="text";
				$contents="没找到想要的照片？\n <a href=\"www.http://www.facejoking.com/upload/".$SchNum."/".$Fmode."\">点击这里上传她的照片吧</a>";
				$resultSriInfo=$this->sendMsg($postObj,$MsgType,$contents);
				echo $resultSriInfo;
			}
			if($Fmode==1)
			{
				$MsgType="text";
				$contents="没找到想要的照片？\n <a href=\"www.http://www.facejoking.com/upload/".$SchNum."/".$Fmode."\">点击这里上传他的照片吧</a>";
				$resultSriInfo=$this->sendMsg($postObj,$MsgType,$contents);
				echo $resultSriInfo;	
			}
		}
	}
	public function FACEJOKING_S($postObj,$MsgType,$Fnum,$info)
	{
		$CityText=CityText;
		$HelpText=HelpText;
		$ConcernText=ConcernText;
		$SchNum=SchNum;
		$FnewsTp1="<item>
				<Title><![CDATA[%s]]></Title> 
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>";
		for($i=($Fnum-1)*10;!empty($info[$i])&&$i<$Fnum*10;$i++)
		{
				$Title=($i+1)."、".$info[$i]->name;
				$Desctiption=$info[$i]->name;
				$PicUrl="http://www.facejoking.com/pic/".$info[$i]->pid.".jpg";
				$Url="http://www.facejoking.com/people/".$info[$i]->pid;
				$FresultStrInfo.= sprintf($FnewsTp1,$Title,$Desctiption,$PicUrl,$Url);
		}
		$newsTpl=" <xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".$i."</ArticleCount>
					<Articles>".$FresultStrInfo."</Articles>
					<FuncFlag>1</FuncFlag>
					</xml>";
		$resultStrInfo=sprintf($newsTpl,$postObj->FromUserName,$postObj->ToUserName, time(), $MsgType);
		echo $resultStrInfo;
	}
	
	public function yeyetan($postObj)
	{
		$url='http://sns.video.qq.com/fcgi-bin/dlib/dataout_ex?auto_id=269&itype=1555&sort=1&page=0';
		$html=file_get_contents($url);
		$info = simplexml_load_string($html, 'SimpleXMLElement', LIBXML_NOCDATA);
		$MsgType="news";
		$i=0;
		$FnewsTp1="<item>
				<Title><![CDATA[%s]]></Title> 
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>";
		for($i=0;$i<10;$i++)
		{
			$Title=$info->cover[$i]->c_second_title."\n".$info->cover[$i]->c_date;
			$Desctiption=$info->cover[$i]->c_title;
			$PicUrl=$info->cover[$i]->c_pic2;
			$Url="http://3g.v.qq.com/play/play.html?coverid=".$info->cover[$i]->c_cover_id;
			$FresultStrInfo.= sprintf($FnewsTp1,$Title,$Desctiption,$PicUrl,$Url);
		}
		$newsTpl=" <xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".$i."</ArticleCount>
					<Articles>".$FresultStrInfo."</Articles>
					<FuncFlag>1</FuncFlag>
					</xml>";
		$resultStrInfo=sprintf($newsTpl,$postObj->FromUserName,$postObj->ToUserName, time(), $MsgType);
		echo $resultStrInfo;
		
	}
}
	/**********************************************/
	//函数功能：获取访问IP并将IP存储在 “log.html”文件中
	/**********************************************/
	function traceHttp()
	{
		logger("REMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].((strpos($_SERVER["REMOTE_ADDR"],"101.226"))?"FORM WEIXIN":"unknown IP"));
		logger("QUERY_STRING:".$SERVER["QUERY_STRING"]);
	}
	function logger($content)
	{
		file_put_contents("log.html",date('Y-m-d H:i:s').$content."<br/>",FILE_APPEND);
	}

?>