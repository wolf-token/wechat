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
			
			//查找信息
			$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE account=:account ",array("account"=>$_GPC['account']));
			//判断是否存在
			if (!empty($person) && empty($person['openid'])) {
				
				//绑定信息
				$add['openid'] = $openid;
				$add['avatar'] = $avatar;
				$add['nickname'] = $nickname;
				$add['uid'] = $uid;

				//修改信息
				$result = pdo_update("move_customer",$add,array("id"=>$person['id']));

				//判断是否绑定成功
				if (!empty($result)) {
					//判断是否是从班里业务跳转
					if ($_GPC['manage'] == 1) {
						
						header('Location: '.$this->createMobileUrl("broad",array("op"=>"add")));

					}else if(!empty($_GPC['fid'])){

						header('Location: '.$this->createMobileUrl("fixed",array("op"=>"add","id"=>$_GPC['fid'])));

					}else{	
						header('Location: '.$this->createMobileUrl("customer",array("op"=>"list")));
					}
					
				}else{

					message("绑定信息失败",$url);
				}
			}else if (empty($person)) {
				
				//加载注册页面
				message("对不起您输入的宽带账号信息错误或者是工作人员还未录入信息,请重新输入！",$url);
			}

		}else{

			message("绑定信息失败",$url);
		}

	}else if ($operation == "list") {
		// echo "string";die();
		$manage = $_GPC['manage'];
		$fid = $_GPC['fid'];
		//加载登录页面
		include $this->template("register",$manage,$fid);

	}else if ($operation == "check") {
		
		//查找数据
		$date = pdo_fetchall("SELECT * FROM ".tablename("move_customer")." WHERE account=:account ",array("account"=>$_GPC['account']));
		//判断是否存在账号
		if (empty($date)) {
			
			echo "no";

		}else{

			echo "yes";
		}
	}else if ($operation == "num") {
		
		//查找数据
		$date1 = pdo_fetchall("SELECT * FROM ".tablename("move_staff")." WHERE number=:number ",array("number"=>$_GPC['number']));
		//判断是否存在账号
		if (!empty($date1)) {
			
			echo "no";

		}else{

			echo "yes";
		}
	}else if ($operation == "pop") {
		
		//查找数据
		$date2 = pdo_fetchall("SELECT * FROM ".tablename("move_staff")." WHERE phone=:phone ",array("phone"=>$_GPC['phone']));
		//判断是否存在账号
		if (!empty($date2)) {
			
			echo "no";

		}else{

			echo "yes";
		}
	}else if ($operation == "seek") {
		
		//查找数据
		$date3 = pdo_fetchall("SELECT * FROM ".tablename("users")." WHERE username=:username ",array("username"=>$_GPC['name']));
		//判断是否存在账号
		if (!empty($date3)) {
			
			echo "no";

		}else{

			echo "yes";
		}
	}




 ?>