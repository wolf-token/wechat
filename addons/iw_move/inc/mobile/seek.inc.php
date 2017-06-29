<?php 

	global $_W,$_GPC;
	$action = 'seek';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	//查找此人信息
	if ($operation == "list") {
		
		//查找此人信息
		$ret = pdo_fetch("SELECT * FROM ".tablename("users")." WHERE openid=:openid ",array("openid"=>$openid));
		if (!empty($ret)) {
			
			$seek_details = $ret;
			include $this->template("seek_details");
		}else{
			include $this->template("service");
		}
		

	}elseif ($operation == "add") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//接收数据
			$name = pdo_fetch("SELECT * FROM ".tablename("users")." WHERE username=:username ",array("username"=>$_GPC['name']));
			// echo "nice";
			//判断
			if (!empty($name)) {
				
				$passwordinput = "{$_GPC['pass']}-{$name['salt']}-{$_W['config']['setting']['authkey']}";
				$password = sha1($passwordinput);
				$res = pdo_fetch("SELECT * FROM ".tablename("users")." WHERE username=:username AND password=:password ",array("username"=>$_GPC['name'],"password"=>$password));
				// var_dump($res);
				if (!empty($res)) {
					
					//修改数据
					$rr = pdo_update("users",array("openid"=>$openid,"avatar"=>$avatar,"nickname"=>$nickname),array("uid"=>$res['uid']));
					//判断
					if (!empty($rr)) {
						
						message("恭喜您绑定信息成功",$this->createMobileUrl('seek',array('op'=>'details','uid'=>$res['uid'])),"info");
					}else{
						message("对不起您绑定信息失败",$url,"error");
					}
				}else{
						message("对不起您绑定信息失败",$url,"error");
				}
			}
		}
	}else if ($operation == "details") {
		
		//加载详细页码
		$seek_details = pdo_fetch("SELECT * FROM ".tablename("users")." WHERE uid=:uid ",array("uid"=>$_GPC['uid']));

		include $this->template("seek_details");
	}
	



 ?>