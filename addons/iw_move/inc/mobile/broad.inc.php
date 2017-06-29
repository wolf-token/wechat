<?php 

		global $_W,$_GPC;
	$action = 'broad';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	

	if ($operation == "add") {
		
		$manage = $_GPC['manage'];
		//查找用户信息
		$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid AND uid=:uid",array("openid"=>$openid,"uid"=>$uid));
		//判断是否有此客户信息
		// if (empty($person)) {
			
		// 	//加载登录页面
		// 	include $this->template("login",$manage);
			
		// }else if (!empty($person) && empty($person['name'])) {
			
		// 	$pid = $person['id'];
		// 	//加载注册页面
		// 	include $this->template("enroll",$pid,$manage);	
		
		// }else if(!empty($person) && !empty($person['name']) && !empty($person['openid'])){

			//查找宽带信息
			$band = pdo_fetchall("SELECT * FROM ".tablename("move_broadband")." WHERE status=0 ");
			//判断是否有宽带信息
			if (!empty($band)) {
				
				//加载信息
				include $this->template("band",$person);

			}else{

				message("对不起此类产品还未上架",$this->createMobileUrl("question",array("op"=>'list')),"info");
			}
			

		// }else{

		// 	message("对不起未找到与您匹配信息！",$this->createMobileUrl("question",array("op"=>"list")),"info");
		// }
		
		
		
		
	}else if ($operation == "list") {
		
		//查找信息
		$broad = pdo_fetch("SELECT * FROM ".tablename("move_photo")." WHERE status=0 ORDER BY id DESC ");
		$broad['content'] = htmlspecialchars_decode($broad['content']);
		// var_dump($broad);die();
		//判断是否存在信息
		if (!empty($broad['content'])) {
			
			//加载页面
			include $this->template("broad");

		}else{

			message("对不起此类产品还未上架",$this->createMobileUrl("question",array("op"=>"list")),"info");
			
		}

	
	}else if ($operation == "last") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			// var_dump($_POST);die();
			//查找固话信息
			$tell = pdo_fetch("SELECT * FROM ".tablename("move_broadband")." WHERE status=0 AND id=:id",array("id"=>$_GPC['id']));
			// var_dump($tell);die();
			//模糊查找
			$where = '';

			if (!empty($_GPC['plat']) && isset($_GPC['plat'])) {
				
				$where = ' WHERE `name` LIKE :keyword ';
				$params[':keyword'] = "%{$_GPC['plat']}%"; 
			}

			//查找申报人的来源
			$come = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid ",array("openid"=>$openid));
			$comea = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE openid=:openid ",array("openid"=>$openid));
			if (!empty($come) && !empty($comea)) {
				
				$add1['come'] = 1;
				$add1['uid'] = $come['id'];

			}
			if (empty($come) && empty($comea)) {
				
				$add1['come'] = 1;
				$add1['uid'] = 0;

			}
			if (empty($come) && !empty($comea)) {
				
				$add1['come'] = 0;
				$add1['uid'] = $comea['id'];
				
			}
			if (!empty($come) && empty($comea)) {
				
				$add1['come'] = 1;
				$add1['uid'] = $come['id'];
				
			}
			
			//判断是否已经录入
			$lu = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE name=:name AND phone=:phone AND plat=:plat AND openid=:openid",array("name"=>$_GPC['name'],"openid"=>$openid,"phone"=>$_GPC['phone'],"plat"=>$_GPC['plat']));

			$add1['name'] = $_GPC['name'];
			$add1['details'] = $tell['name']."安装";
			$add1['plat'] = $_GPC['plat'];
			$add1['type'] = $_GPC['type'];
			$add1['phone'] = $_GPC['phone'];
			$add1['openid'] = $openid;
			$add['nickname'] = $nickname;
			$add['avatar'] = $avatar;
			$add1['wid'] = $uid;
			$add1['time'] = time();
			$time1 = date("Y-m-d H:i:s",time());
			
			//判断是否
			if (empty($lu)) {
				
						//添加数据
			$re = pdo_insert("move_declare",$add1);
			$mid = pdo_insertid();
			//判断
			if (!empty($re)) {

				$team = pdo_fetch("SELECT * FROM ".tablename("move_team").$where." ORDER BY id ASC LIMIT 1 ",$params);
				//查找维修工信息
				$message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE tid=:tid ORDER BY service ASC LIMIT 1 ",array("tid"=>$team['pid']));
				
				if (!empty($message)) {
					
					//发送消息通知
					date_default_timezone_set('PRC');
		
					$data1 = json_decode(file_get_contents("token.json"),true);

					if ($data1->expire_time < time()) {

						$account_api = WeAccount::create();
						// $account_api->clearAccessToken();
						$access_token = $account_api->getAccessToken();
						date_default_timezone_set('PRC');
						$data1->expire_time = time() + 7000;
						$data1->access_token = $access_token;
						 $fp = fopen("token.json", "w");
					    fwrite($fp, json_encode($data1));
					    fclose($fp);

					}else{
						
						$access_token = $data1['access_token'];
						
					}

						//模板消息
						$template=array(
						        'touser'=>$message['openid'],
						        'template_id'=>"ngxt5b7D35pGbeiAWqWUt3B98O7D2UfOjqRyhfag29o",
						        'url'=>"http://weixin.qq.com/download",
						        'topcolor'=>"#53B7DB",
						        'data'=>array(
						                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好,您有新的".$tell['name']."宽带安装订单！！！")),
						                'keyword1'=>array('value'=>urlencode($tell['name']."宽带安装"),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword2'=>array('value'=>urlencode($add1['name']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword3'=>array('value'=>urlencode($add1['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword4'=>array('value'=>urlencode($add1['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'remark'=>array('value'=>urlencode('请不要点击详情，详情：安装'.$add1['details'].'宽带'),'color'=>'#0A12B3','font-size'=>'15px'), )
						            );
					
					$json_template=json_encode($template);
					//echo $json_template;
					//echo $this->access_token;
					$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
					$data = urldecode($json_template);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
					// POST数据
					curl_setopt($ch, CURLOPT_POST, 1);
					// 把post的变量加上
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$output = curl_exec($ch);
					curl_close($ch);
					$res=$output;
					
					if ($res[errcode]==0){

						//修改信息
						pdo_update("move_declare",array("pid"=>$message['id'],'status'=>4),array("id"=>$mid));
						$meth = intval($message['service'])+1;
						pdo_update("move_staff",array("service"=>$meth),array("id"=>$message['id']));

						date_default_timezone_set('PRC');

						$data2 = json_decode(file_get_contents("token.json"),true);

						if ($data2->expire_time < time()) {

							$account_api1 = WeAccount::create();
							// $account_api->clearAccessToken();
							$access_token1 = $account_api1->getAccessToken();
							date_default_timezone_set('PRC');
							$data2->expire_time = time() + 7000;
							$data2->access_token = $access_token1;
							 $fp1 = fopen("token.json", "w");
						    fwrite($fp1, json_encode($data2));
						    fclose($fp1);

						}else{
							
							$access_token1 = $data2['access_token'];
							
						}

						//模板消息
						$template1=array(
						        'touser'=>$openid,
						        'template_id'=>"j_zeIcYPx5YoMOh541mKQMyHvZ_e0SoK-7EPhjkXJ9E",
						        'url'=>"http://weixin.qq.com/download",
						        'topcolor'=>"#53B7DB",
						        'data'=>array(
						                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好,您的订单已受理维修工处理！！！")),
						                'keyword1'=>array('value'=>urlencode($add1['details']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword2'=>array('value'=>urlencode($time1),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword3'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword4'=>array('value'=>urlencode($message['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'remark'=>array('value'=>urlencode("请不要点击详情，请保持通话畅通，如有需要可直接拨打工程师电话，期待您再次使用微信报修"),'color'=>'#0A12B3','font-size'=>'15px'), )
						            );

						
						$json_template1=json_encode($template1);
						//echo $json_template;
						//echo $this->access_token;
						$url1="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token1;
						$data4 = urldecode($json_template1);
						$ch1 = curl_init();
						curl_setopt($ch1, CURLOPT_URL, $url1);
						curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, FALSE);
						// POST数据
						curl_setopt($ch1, CURLOPT_POST, 1);
						// 把post的变量加上
						curl_setopt($ch1, CURLOPT_POSTFIELDS, $data4);
						$output1 = curl_exec($ch1);
						curl_close($ch1);
						
						header('Location: '.$this->createMobileUrl("interact",array("op"=>"list")));
					}else{

						pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));		
						message("分配员工失败,等待管理员分配",$this->createMobileUrl("interact",array("op"=>"list")),"info");
					}
				}else{

					pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));

					message("未找到与之匹配的维修工,等待管理员分配",$this->createMobileUrl("interact",array("op"=>"list")),"info");
				}

			 // $this->createMobileUrl("declare",array("op"=>"list"));
				
			
				
			}else{

			  header('Location: '.$this->createMobileUrl("declare",array("op"=>"list")));

			}
			}else{

				message("您已经录入信息",$this->createMobileUrl("customer",array("op"=>"list")),"info");
			}
		}
	}




 ?>