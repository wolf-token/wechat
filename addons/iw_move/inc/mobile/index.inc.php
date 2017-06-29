<?php 

	global $_W,$_GPC;
	$action = 'finish';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if ($operation == "list") {
		

		//查找信息
		// $index = pdo_fetchall("SELECT * FROM ".tablename("move_nearby"));
		// foreach ($index as $key => $value) {
			
		// 	$index[$key]['photo'] = json_decode($value['photo']);
		// }
		
		// include $this->template("index");
		//查找此人信息
		$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid AND uid=:uid",array("openid"=>$openid,"uid"=>$uid));
		// var_dump($uid);die();
		//判断是否有此客户信息
		if (empty($person)) {
			
			//加载登录页面
			include $this->template("login");
			
		}else if (!empty($person) && empty($person['name'])) {
		
			$pid = $person['id'];
			//加载注册页面
			include $this->template("enroll",$pid);		
		
		}else if(!empty($person) && !empty($person['name']) && !empty($person['openid'])){

			//查找用户信息
			$customer = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid",array("openid"=>$openid));
			$remark = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE openid=:openid AND wid=:wid ORDER BY time DESC ",array("openid"=>$openid,"wid"=>$uid));
			//判断是否已经添加信息
			if (empty($customer)) {
				
				$recommend_id = pdo_fetch("SELECT * FROM ".tablename("qrcode_stat")." WHERE openid=:openid AND uniacid=:uniacid ORDER BY id DESC ",array("openid"=>$openid,"uniacid"=>$uniacid));
				$scene_str1 = explode(":",$recommend_id['scene_str']);
				$type = $scene_str1[1];
				$recommend = $scene_str1[3];
				$add1['openid'] = $openid;
				$add1['nickname'] = $nickname;//获取当前用户的昵称信息
				$add1['avatar'] = $avatar;//获取当前用户的头像信息
				$add1['uid'] = $uid;//获取当前用户的uid
				$add1['time'] = time();
				
				//判断此人扫码是扫的和人的码
				//2为扫员工码，1为扫客户码
				if (!empty($type) && !empty($recommend)) {

					
					//判断扫码的类型
					//扫客户码

					if ($type == 1) {
						
						$add1['type'] = $type;
						$add1['pid'] = $recommend;

						//插入到数据库
						$result = pdo_insert("move_customer",$add1);
						pdo_insert("move_record",$add1);
						//查找客户信息
						$info = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id ",array("id"=>$recommend));
						$infos = intval($info['math'])+1;
						$rr = pdo_update("move_customer",array("math"=>$infos),array("id"=>$info['id']));
						
						//判断是否插入成功
						if (empty($result)) {
							
							message("申报数据错误",$this->createMobileUrl("declare",array("op"=>"list")),"info");
						}

					}else if ($type == 2) {
						
						$add1['type'] = $type;
						$add1['pid'] = $recommend;

						//插入到数据库
						$result1 = pdo_insert("move_customer",$add1);
						pdo_insert("move_record",$add1);
						$too = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$recommend));
						$toos = intval($too['math'])+1;
						pdo_update("move_staff",array("math"=>$toos),array("id"=>$too['id']));
						
						if (empty($result1)) {

							message("申报数据错误",$this->createMobileUrl("declare",array("op"=>"list")),"info");
						}

					}

				}else{

						$add1['type'] = 3;
						$add1['pid'] = 0;

						//插入到数据库
						$result2 = pdo_insert("move_customer",$add1);

						if (empty($result2)) {
							
						message("申报数据错误",$this->createMobileUrl("declare",array("op"=>"list")),"info");
						}

				}

				$photo = "/addons/iw_move/template/mobile/images/ma.jpg";
				$hint = 1;//代表未注册信息
				//查找申报记录
				
				include $this->template("customer",$photo,$remark,$hint);

			}else{

				
				//判断是否有二维码
				if (empty($customer['code']) && !empty($customer['name'])) {
					
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

			         $scene_str1 ="scan:1:{$customer['openid']}:{$customer['id']}";
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
			            $date['scene_id']= $scene_str1;
			            $data['openid']=$_W['fans']['openid'];
			            $date['action_name']="QR_LIMIT_SCENE";
			            $date['remark']='';
			            $date['createtime']=time();

			            if(!empty($date)){
			            $res= pdo_insert("move_token",$date);//插入数据
			            
			            $generalizea = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$json_info['ticket'];
			            //修改信息
			             pdo_update("move_customer",array("code"=>$generalizea),array("openid"=>$openid,"uid"=>$uid));
			             $photo = $generalizea;
			             $hint = 2;//代表已经注册信息
			             //加载页面
						include $this->template("customer",$photo,$remark,$hint);

				        }else{

				             header('Location: '.$this->createMobileUrl("customer",array("op"=>"list")));

				        } 


			         }else{

			         	message("申报数据错误",$this->createMobileUrl("declare",array("op"=>"list")),"info");
			         }

				}else if(!empty($customer['code']) && !empty($customer['name'])){

					$photo = $customer['code'];
					$hint = 2;//代表已经注册信息
					// var_dump($remark);die;
					include $this->template("customer",$photo,$remark,$hint);

				}else{

					$photo = "/addons/iw_move/template/mobile/images/ma.jpg";
					$hint = 1;//代表未注册信息
					// var_dump($remark);die;
					include $this->template("customer",$photo,$remark,$hint);
				}
			}
			

		}else{

			message("对不起未找到与您匹配信息！",$this->createMobileUrl("question",array("op"=>"list")),"info");
		}

	}
	


 ?>