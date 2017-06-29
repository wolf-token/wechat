<?php 

	
	global $_W,$_GPC;
	$action = 'color';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	
	if ($operation == "ja") {
		
		// $index = array();
		//引入文件
		require_once "distance.php";
		$model =  new Distance();

		//判断是否有值
		if (!empty($_GPC['lng']) && !empty($_GPC['lat'])) {

			//获取信息
			$lat = $_GPC['lat'];
			$lng = $_GPC['lng'];

			//查找门店信息
			$door = pdo_fetchall("SELECT * FROM ".tablename("move_nearby"));
			foreach ($door as $key => $value) {
				
				//计算距离
				$distance = $model->getDistance($lat,$lng,$value['lat'],$value['lng']);
			    
			     //判断距离 推荐小于10千米范围内的门店
			     if ($distance <= 10000) {
			     	
			     	$index[$key] = $value;
			     }

			}
			// var_dump($index);die;
			//判断是否为空
			if (!empty($index)) {

				foreach ($index as $ko => $val) {
					
					$index[$ko]['photo'] = json_decode($val['photo']);
				}
				
				//加载页面信息
				include $this->template("index");

			}else{

				message("没有找到附近的门店信息",'',"info");
			}
			

		}else{

			message("获取信息失败",$this->createMobileUrl("color",array("op"=>'list')),"info");
		}


	}else if ($operation == "list") {
		
		//加载页面信息
		
		include $this->template("color",$index);

	}



 ?>