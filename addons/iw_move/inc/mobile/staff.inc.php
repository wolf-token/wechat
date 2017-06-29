<?php 

	
	global $_W,$_GPC;
	$action = 'staff';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	
	if ($operation == "list") {
		
		//查找用户信息
		$staff = pdo_fetch("SELECT * FROM ".tablename("move_staff")."WHERE openid=:openid",array("openid"=>$openid));
		//设置条件
		// var_dump($staff);die();
		//判断
		if (empty($staff)) {
			
			
			include $this->template("staff");

		}else{
			
			header('Location: '.$this->createMobileUrl("staff",array("op"=>"details")));
		}
		
		
	}else if ($operation == "add") {
	// var_dump($_POST);die();
		if ($_W['ispost']) {
			
			$pass = md5($_GPC['pass']);
			//查找是否已经存在信息
			$res = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE name=:name AND phone=:phone AND pass=:pass",array("name"=>$_GPC['name'],"phone"=>$_GPC['phone'],"pass"=>$pass));

			if (!empty($res)) {
					
					
					$add['openid'] = $openid;
					$add['nickname'] = $nickname;
					$add['avatar'] = $avatar;
					$add['uid'] = $uid;
					$add['time'] = time();

					//添加数据
					$re = pdo_update("move_staff",$add,array("id"=>$res['id']));

					//判断
					if (!empty($re)) {

					 // $this->createMobileUrl("staff",array("op"=>"list"));
						header('Location: '.$this->createMobileUrl("staff",array("op"=>"list")));
					
						
					}else{

					  header('Location: '.$this->createMobileUrl("staff",array("op"=>"list")));

					}

				}
			//接收信息

		}

	}else if ($operation == "details") {
		
		$details = pdo_fetch("SELECT * FROM ".tablename("move_staff")."WHERE openid=:openid",array("openid"=>$openid));
		$details['start'] =  pdo_fetch("SELECT * FROM ".tablename("move_control")." ORDER BY ID DESC");
		//加载页面
		include $this->template("details");

	}else if ($operation == "record") {
		
		$money = pdo_fetch("SELECT * FROM ".tablename("move_staff")."WHERE id=:id",array("id"=>$_GPC['id']));

		//查找维修记录
		$record = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE status!=0 AND status!=2 AND pid=:pid ORDER BY time DESC ",array("pid"=>$_GPC['id']));
		//加载页面
		include $this->template("record");

	}else if ($operation == "center") {
		
		$center = pdo_fetchall("SELECT * FROM ".tablename("move_record")." WHERE type=2 AND pid=:pid",array("pid"=>$_GPC['id']));
		$shu = count($center);
		//加载页面
		include $this->template("center",$shu);

	}else if ($operation == "generalize") {
		
		$generalize = pdo_fetch("SELECT * FROM ".tablename("move_staff")."WHERE openid=:openid",array("openid"=>$openid));
		
		//判断是否有二维码
		if (empty($generalize['code'])) {
			
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
			 
	        $json_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
	        //查取个人信息

	        $scene_str1 ="scan:2:{$generalize['openid']}:{$generalize['id']}";
	        $curl_data='';
	        
	        //永久 post的json数据
	        $curl_data='{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str":"'.$scene_str1.'"}}}';
	        $url = $json_url;
	        $data = $curl_data;

	        $ch = curl_init();
	        $header = "Accept-Charset: utf-8";
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	        $tmpInfo = curl_exec($ch);

	        if (curl_errno($ch)) {
	            //curl_close( $ch )
	            $json_info=json_decode($ch,true);
	        }else{
	            //curl_close( $ch ) 
	            $json_info=json_decode($tmpInfo,true);
	        }
	        curl_close( $ch ) ;
	        
	      
	        //这里代表生成成功，记录数据以便插入到数据库，方便以后统计查找
	        if(!empty($json_info['ticket'])){ 


	            $date['token']=$access_token;
	            $date['tiket']=$json_info['ticket'];
	            $date['url']=$json_info['url'];
	            $date['scene_id']=$i;
	            $data['openid']=$_W['fans']['openid'];
	            $date['action_name']="QR_LIMIT_SCENE";
	            $date['remark']='';
	            $date['createtime']=time();

	            if(!empty($date)){
	            $res= pdo_insert("move_token",$date);//插入数据
	            
	            $generalizea = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$json_info['ticket'];
	            //修改信息
	             pdo_update("move_staff",array("code"=>$generalizea),array("openid"=>$openid,"uid"=>$uid));

	             //加载页面
				include $this->template("generalizea");

		        }else{

		             header('Location: '.$this->createMobileUrl("staff",array("op"=>"generalize")));

		        } 


	         }

		}else{

			$generalizea = $generalize['code'];
			// var_dump($generalizea);die();
			include $this->template("generalizea");

		}
		

	}else if ($operation == "edit") {
		
		//查找信息
		$edit = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$_GPC['id']));

		//加载页面信息
		include $this->template("edit");
	}else if ($operation == "update") {
		
		//判断
		if ($_W['ispost']) {
			
			//判断是否修改密码
			if (!empty($_GPC['pass'])) {
				
				$update['pass'] = $_GPC['pass'];
			}
			//接收数据
			$update['name'] = $_GPC['name'];
			$update['phone'] = $_GPC['phone'];
			
			$update['team'] = $_GPC['team'];
			$update['tid'] = $_GPC['tid'];
			$update['time'] = time();
			
			//修改数据
			$result = pdo_update("move_staff",$update,array("id"=>$_GPC['id']));

			//判断
			if (!empty($result)) {
				
				header('Location: '.$this->createMobileUrl("staff",array("op"=>"list")));
			}else{

				message("修改个人信息失败",$this->createMobileUrl("staff",array("op"=>'edit')),"info");
			}
		}
	}else if ($operation == "login") {
		
		//加载注册页面
		include $this->template("staff_login");
		
	}else if ($operation == "register") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//查找信息
			$person = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE openid=:openid AND  phone=:phone",array("openid"=>$openid,"phone"=>$_GPC['phone']));
			//判断是否存在
			if (empty($person)) {
				
				//接收数据
				$login['name'] = $_GPC['name'];
				$login['tid'] = $_GPC['tid'];
				$login['phone'] = $_GPC['phone'];
				$login['team'] = $_GPC['team'];
				$login['pass'] = md5($_GPC['pass']);
				$login['openid'] = $openid;
				$login['avatar'] = $avatar;
				$login['nickname'] = $nickname;
				$login['uid'] = $uid;
				$login['time'] = time();

				//添加数据
				$rer = pdo_insert("move_staff",$login);

				//判断是否插入成功
				if (!empty($rer)) {
					
					header('Location: '.$this->createMobileUrl("staff",array("op"=>"details")));
					
				}else{

					message("对不起注册信息失败");
				}
			}else{

				header('Location: '.$this->createMobileUrl("staff",array("op"=>"details")));
			}
			
		}
	}




 ?>