<?php 

	global $_W,$_GPC;
	$action = 'customer';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'add';
	//查找此人信息
	
	if ($operation == "add") {

		//判断是否为post
		if ($_W['ispost']) {
			
			$manage = $_GPC['manage'];
			$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid AND phone=:phone",array("openid"=>$openid,"phone"=>$_GPC['phone']));
			if (empty($person)) {
				
			$recommend_id = pdo_fetch("SELECT * FROM ".tablename("qrcode_stat")." WHERE openid=:openid AND uniacid=:uniacid ORDER BY id DESC ",array("openid"=>$openid,"uniacid"=>$uniacid));
			$scene_str1 = explode(":",$recommend_id['scene_str']);
			$type = $scene_str1[1];
			$recommend = $scene_str1[3];
			$add1['openid'] = $openid;
			$add1['nickname'] = $nickname;//获取当前用户的昵称信息
			$add1['avatar'] = $avatar;//获取当前用户的头像信息
			$add1['uid'] = $uid;//获取当前用户的uid
			$add1['time'] = time();
			$add1['name'] = $_GPC['name'];
			$add1['plot'] = $_GPC['plot'];
			$add1['phone'] = $_GPC['phone'];
			$add1['account'] = $_GPC['account'];
			
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
						

						message("注册信息失败",$url,"error");

					}else{

						//判断是否为办理业务
						if ($manage == 1 ) {
							header('Location: '.$this->createMobileUrl("broad",array("op"=>"add")));
						}else if(!empty($_GPC['fid'])){

						header('Location: '.$this->createMobileUrl("fixed",array("op"=>"add","id"=>$_GPC['fid'])));

						}else{	
							message("注册信息成功",$url,'success');
						}
						
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

						message("注册信息失败",$url,"error");

					}else{

						//判断是否为办理业务
						if ($manage == 1 ) {
							header('Location: '.$this->createMobileUrl("broad",array("op"=>"add")));
						}else if(!empty($_GPC['fid'])){

						header('Location: '.$this->createMobileUrl("fixed",array("op"=>"add","id"=>$_GPC['fid'])));

						}else{	
							message("注册信息成功",$url,'success');
						}
					}


				}

			}else{

					$add1['type'] = 3;
					$add1['pid'] = 0;

					//插入到数据库
					$result2 = pdo_insert("move_customer",$add1);

					if (empty($result2)) {
						
					message("注册信息失败",$url,"error");

					}else{

						//判断是否为办理业务
						if ($manage == 1 ) {
							header('Location: '.$this->createMobileUrl("broad",array("op"=>"add")));
						}else if(!empty($_GPC['fid'])){

						header('Location: '.$this->createMobileUrl("fixed",array("op"=>"add","id"=>$_GPC['fid'])));

						}else{	
							message("注册信息成功",$url,'success');
						}
					}


			}

				
		}else{

			message("您已注册成功",$url,"success");
		}


		}else{

			message("绑定信息失败",'error');
		}

	}else if ($operation == "update") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			$manage1 = $_GPC['manage'];
			//接收数据
			$update['name'] = $_GPC['name'];
			$update['plot'] = $_GPC['plot'];
			$update['phone'] = $_GPC['phone'];
			// $update['account'] = $_GPC['account'];

			//执行修改
			$res = pdo_update("move_customer",$update,array("id"=>$_GPC['id']));

			//判断是否修改成功
			if (!empty($res)) {
				
				//判断是否为办理业务
				if ($manage == 1 ) {
					header('Location: '.$this->createMobileUrl("broad",array("op"=>"add")));
				}else if(!empty($_GPC['fid'])){

				header('Location: '.$this->createMobileUrl("fixed",array("op"=>"add","id"=>$_GPC['fid'])));

				}else{	
				header('Location: '.$this->createMobileUrl("customer",array("op"=>"list")));
				}
			}else{

				message("完善信息失败",$url,"error");
			}

		}else{

			message("绑定信息失败",'error');
		}
	}



 ?>