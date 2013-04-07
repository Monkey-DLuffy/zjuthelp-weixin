<?php
/*
+--------------------------------------------------------------------------
|   Anwsion [#RELEASE_VERSION#]
|   ========================================
|   by Anwsion dev team
|   (c) 2011 - 2012 Anwsion Software
|   http://www.anwsion.com
|   ========================================
|   Support: zhengqiang@gmail.com
|   
+---------------------------------------------------------------------------
*/

if (! defined('IN_ANWSION'))
{
	die();
}

class weixin_class extends AWS_MODEL
{
	var $text_tpl = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>';
	var $news1_tpl = '<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>1</ArticleCount>
					<Articles>
					<item>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>
					</Articles>
					<FuncFlag>0</FuncFlag>
					</xml>';
	var $help = "发送以下关键词：\n【课表】查询原创课表（部分用户可能课表有误）\n【天气】查看校园天气\n【校车】查校车时刻表\n【外卖】提供外卖号码\n【优惠】最新优惠活动\n【电话】查看校园电话\n【黄历】查工大老黄历\n【求签】测测各类吉凶\n\n关键词以外的回复皆默认搜索工大助手问答平台www.izjut.com并根据网站回答回复相应答案";
	public function fetch_message()
	{
		$postStr = file_get_contents('php://input');
		
		//extract post data
		if (! empty($postStr))
		{
			$postObj = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			return array(
				'fromUsername' => $postObj['FromUserName'],
				'toUsername' => $postObj['ToUserName'],
				'content' => trim($postObj['Content']),
				'time' => time(),
				'msgType' => $postObj['MsgType'],
				'event' => $postObj['Event'],
				'eventKey' => $postObj['EventKey']
			);
		}
	}
	
	public function response_message($input_message = array())
	{
		if ($input_message['msgType'] == 'event')
		{
			switch ($input_message['event'])
			{
				case 'subscribe':
					$response_message = "欢迎关注 ". get_setting('site_name') . "————工大学子的学习生活好帮手\n\n".$this->help;
					echo sprintf($this->text_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'], $response_message);
				break;
			}
		}
		if ($input_message['msgType'] == 'text')
		{
			switch ($input_message['content'])
			{
				case "帮助":
                    $response_message = $this->help;
					echo sprintf($this->text_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'], $response_message);
                    break;
				case '校车':
					$contentPicUrl ='http://mmsns.qpic.cn/mmsns/nMeVe6ic1s7MUE8P8fuIpUwFG8KZzVERuibpMiaVFVD2hicHzwtpPia5b4g/0';
                   	$contentUrl='http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NzY2NTcyMQ==&appmsgid=10000031&itemidx=1#wechat_redirect';
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'校车时刻表','',$contentPicUrl,$contentUrl);
					break;
				case "天气":
                    $response_message = $this->getWeather()."\n\n小助手提醒您注意天气变化，爱心伞记得放回原处哦~";
					echo sprintf($this->text_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'], $response_message);
                    break;
                case "课表":
                	$contentPicUrl ="";
                   	$contentUrl="http://www.izjut.com/weixin/zjutfunction/loginyuanchuang.html";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'本学期课表查询','',$contentPicUrl,$contentUrl);
                    break;
                case "优惠":
                	$contentPicUrl ="";
                   	$contentUrl="http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NzY2NTcyMQ==&appmsgid=10000041&itemidx=1#wechat_redirect";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'最新校园周边优惠活动','',$contentPicUrl,$contentUrl);
                    break;
                case "外卖":
                	$contentPicUrl ="";
                   	$contentUrl="http://www.izjut.com/weixin/zjutfunction/food.html";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'提供部分外卖电话','',$contentPicUrl,$contentUrl);
                    break;
                case "电话":
                	$contentPicUrl ="";
                   	$contentUrl="http://www.izjut.com/weixin/zjutfunction/tel.html";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'校内各种电话热线','',$contentPicUrl,$contentUrl);
                    break;
                case "黄历":
                    $contentPicUrl ="http://www.fjsen.com/images/attachement/jpg/site1/2011-12-23/5160716855638538788.jpg";
                   	$contentUrl="http://www.izjut.com/weixin/zjutcal/";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'工大老黄历','',$contentPicUrl,$contentUrl);
                    break;
                case "求签":
                    $contentPicUrl ="http://d.hiphotos.baidu.com/baike/pic/item/0dd7912397dda14406509992b3b7d0a20cf486b1.jpg";
                   	$contentUrl="http://www.izjut.com/weixin/zjutpray/";
					echo sprintf($this->news1_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'],'工大求签','',$contentPicUrl,$contentUrl);
                    break;
				default:
					if ($search_result = $this->model('search')->search_questions($input_message['content'], null, 6))
					{
						$response_message = '为您找到下列相关问题:' . "\n";
						
						foreach ($search_result AS $key => $val)
						{
							$response_message .= "\n" . '• <a href="' . get_js_url('/m/question/' . $val['question_id']) . '">' . $val['question_content'] . '</a>' . "\n";
								
							if ($key == 0 AND $val['answer_count'] > 0)
							{
								$response_message .= "--------------------\n";
									
								if ($val['best_answer'])
								{
									$answer_list = $this->model('answer')->get_answer_list_by_question_id($val['question_id'], 1, 'answer.answer_id = ' . (int)$val['best_answer']);
								}
								else
								{
									$answer_list = $this->model('answer')->get_answer_list_by_question_id($val['question_id'], 1, null, 'agree_count DESC');
								}
									
								$response_message .= "最佳答案: \n\n" . cjk_substr($answer_list[0]['answer_content'], 0, 128, 'UTF-8', '...') . "\n";
									
								$response_message .= "--------------------\n";
							}
						}
					}
					else
					{
						$response_message = "抱歉, 没有找到相关问题, 请您替换关键词重新检索\n如果您要继续提问，可以<a href=\"" . get_js_url('/m/add_question/' . urlencode($input_message['content'])) . "\">点击这里提问</a>.\n\n回复 帮助 查看如何使用";
					}
					echo sprintf($this->text_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'], $response_message);
					break;
			}	
		}
		die;
	}
	
	public function func_parser($input_message = array())
	{
		$func_code = strtoupper(substr($input_message['content'], 2, 2));
		$func_param_original = trim(substr($input_message['content'], 4));
		$func_param = strtoupper($func_param_original);
		
		switch ($func_code)
		{
			default:
				$response_message = "代码无效, 支持的代码: \nFN00 - 查询微信绑定状态\nFN02 - 解除微信绑定\nFN10 - 我的提问\nFN11 - 最新通知\nFN20用户名 - 查询用户动态";
			break;
			
			case '99':
				$response_message = "代码无效, 支持的代码: \nFN00 - 查询微信绑定状态\nFN02 - 解除微信绑定\nFN10 - 我的提问\nFN11 - 最新通知\nFN20用户名 - 查询用户动态";
			break;
			
			case '11':
				if ($user_info = $this->model('account')->get_user_info_by_weixin_id($input_message['fromUsername']))
				{
					if ($notifications = $this->model('notify')->list_notification($user_info['uid'], 0, 5))
					{
						$response_message = '最新通知:';
						
						foreach($notifications AS $key => $val)
						{
							$response_message .= "\n\n• " . $val['message'];
						}	
					}
					else
					{
						$response_message = '暂时没有新通知';
					}
				}
				else
				{
					$response_message = '你的微信帐号没有绑定 ' . get_setting('site_name') . ' 的帐号, 请登录网站绑定';
				}
			break;
			
			case '10':
				if ($user_info = $this->model('account')->get_user_info_by_weixin_id($input_message['fromUsername']))
				{
					if ($user_actions = $this->model('account')->get_user_actions($user_info['uid'], 5, 101))
					{
						$response_message = "我的提问: \n";
						
						foreach ($user_actions AS $key => $val)
						{
							$response_message .= "\n" . '• <a href="' . get_js_url('/m/question/' . $val['question_id']) . '">' . $val['question_content'] . '</a> (' . $val['answer_count'] . ' 个回答)' . "\n";
							
							if ($val['answer_count'] > 0)
							{
								$response_message .= "--------------------\n";
									
								if ($val['best_answer'])
								{
									$answer_list = $this->model('answer')->get_answer_list_by_question_id($val['question_id'], 1, 'answer.answer_id = ' . (int)$val['best_answer']);
								}
								else
								{
									$answer_list = $this->model('answer')->get_answer_list_by_question_id($val['question_id'], 1, 'answer.uninterested_count < ' . get_setting('uninterested_fold') . ' AND answer.force_fold = 0', 'add_time DESC');
								}
									
								$response_message .= "最新答案: \n\n" . cjk_substr($answer_list[0]['answer_content'], 0, 128, 'UTF-8', '...') . "\n";
									
								$response_message .= "--------------------\n";
							}
						}
					}
					else
					{
						$response_message = '你还没有进行提问';
					}
				}
				else
				{
					$response_message = '你的微信帐号没有绑定 ' . get_setting('site_name') . ' 的帐号, 请登录网站绑定';
				}
			break;
			
			case '20':
				if ($user_info = $this->model('account')->get_user_info_by_username($func_param_original))
				{
					if ($user_actions = $this->model('account')->get_user_actions($user_info['uid'], 5, 101))
					{
						$response_message = $user_info['user_name'] . "的动态: \n";
						
						foreach ($user_actions AS $key => $val)
						{
							$response_message .= "\n" . '• ' . $val['last_action_str'] . ', <a href="' . get_js_url('/question/' . $val['question_id']) . '">' . $val['question_content'] . '</a> (' . date_friendly($val['add_time']) . ')' . "\n";
						}
					}
					else
					{
						$response_message = '该用户目前没有动态';
					}
				}
				else
				{
					$response_message = '没有找到相关用户';
				}
			break;
			
			// 绑定认证
			case '00':
				if ($user_info = $this->model('account')->get_user_info_by_weixin_id($input_message['fromUsername']))
				{
					$response_message = '你的微信帐号绑定社区帐号: ' . $user_info['user_name'];
				}
				else
				{
					$response_message = '你的微信帐号没有绑定 ' . get_setting('site_name') . ' 的帐号, 请登录网站绑定';
				}
			break;
			
			// 绑定认证
			case '01':
				$response_message = $this->weixin_valid($func_param, $input_message['fromUsername']);
			break;
			
			// 解除绑定
			case '02':
				$response_message = $this->weixin_unbind($input_message['fromUsername']);
			break;
		}
		
		echo sprintf($this->text_tpl, $input_message['fromUsername'], $input_message['toUsername'], $input_message['time'], 'text', $response_message);
		die;
	}
	
	public function create_weixin_valid($uid)
	{
		if ($weixin_valid = $this->fetch_row('weixin_valid', "uid = " . intval($uid)))
		{
			return $weixin_valid['code'];
		}
		else
		{
			$valid_code = strtoupper(fetch_salt(6));
			
			while($this->fetch_row('weixin_valid', "`code` = '" . $this->quote($valid_code) . "'"))
			{
				$valid_code = strtoupper(fetch_salt(6));
			}
			
			$this->insert('weixin_valid', array(
				'uid' => intval($uid),
				'code' => $valid_code
			));
			
			return $valid_code;
		}
	}
	
	public function weixin_valid($param, $weixin_id)
	{
		if ($this->model('account')->get_user_info_by_weixin_id($weixin_id))
		{
			return '微信帐号已经与一个账户绑定, 解绑请回复 FN02';
		}
		else if ($weixin_valid = $this->fetch_row('weixin_valid', "`code` = '" . $this->quote($param) . "'"))
		{
			$this->update('users', array(
				'weixin_id' => $weixin_id
			), 'uid = ' . intval($weixin_valid['uid']));
			
			$this->delete('weixin_valid', 'id = ' . intval($weixin_valid['id']));
			
			return '微信帐号绑定成功';
		}
		
		return '微信绑定代码无效';
	}
	
	public function weixin_unbind($weixin_id)
	{
		$this->update('users', array('weixin_id' => ''), "`weixin_id` = '" . $this->quote($weixin_id) . "'");
		
		return '微信绑定解除成功';
	}

	public function check_signature($signature, $timestamp, $nonce)
	{
		if (!get_setting('weixin_mp_token'))
		{
			return false;
		}
		
		$tmpArr = array(
			get_setting('weixin_mp_token'), 
			$timestamp, 
			$nonce
		);
		
		sort($tmpArr);
		
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		
		if ($tmpStr == $signature)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public function getWeather()
	{
		$url = 'http://m.weather.com.cn/data/101210101.html';
		$output = file_get_contents($url);
		$weather = json_decode($output,true);
		$info = $weather['weatherinfo'];
		$result = "今天：".$info['temp1']." ".$info['weather1']." ".$info['wind1']."\n明天：".$info['temp2']." ".$info['weather2']." ".$info['wind2']."\n后天：".$info['temp3']." ".$info['weather3']." ".$info['wind3'];
      	return $result;
	}
}
