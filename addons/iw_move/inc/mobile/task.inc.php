<?php 

	global $_W,$_GPC;
	$action = 'tastk';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	// echo "nice";die();

	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	
	if ($operation == "list") {
	//查找信息
	$task = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE status=4 AND appraise=0 ");
	// var_dump($task);
	//循环处理
	foreach ($task as $key => $value) {
		
		$value['time'] = date("Y-m-d H:i:s",$value['time']);
		//查找维修人员信息
		$message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:pid ",array("pid"=>$value['pid']));
		// var_dump($message);die();
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

		//echo $this->access_token;exit;
		//判断类型
		if ($value['type'] == 1) {
			
			//模板消息
			$template=array(
			        'touser'=>$message['openid'],
			        'template_id'=>"KTGd44Bn1AFLX-5TIn87Zz8toAvVyMdcKxCqoCITMNw",
			        'url'=>"http://weixin.qq.com/download",
			        'topcolor'=>"#E20000",
			        'data'=>array(
			                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."你好，你有一张待确认宽带业务报修工单！！！"),'color'=>'#E20000'),
			                'keyword1'=>array('value'=>urlencode($value['details']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword2'=>array('value'=>urlencode($value['name']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword3'=>array('value'=>urlencode($value['phone']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword4'=>array('value'=>urlencode($value['plat']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'remark'=>array('value'=>urlencode("请不要点击详情，此单生成时间：".$value['time']),'color'=>'#86CCE6','font-size'=>'15px'), )
			            );
		}else{
			//模板消息
			$template=array(
			        'touser'=>$message['openid'],
			        'template_id'=>"ngxt5b7D35pGbeiAWqWUt3B98O7D2UfOjqRyhfag29o",
			        'url'=>"http://weixin.qq.com/download",
			        'topcolor'=>"#E20000",
			        'data'=>array(
			                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好,您有一张待确认的订单！！！"),'color'=>'#E20000'),
			                'keyword1'=>array('value'=>urlencode("报装"),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword2'=>array('value'=>urlencode($value['name']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword3'=>array('value'=>urlencode($value['phone']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword4'=>array('value'=>urlencode($value['plat']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#86CCE6','font-size'=>'30px'),
			                'remark'=>array('value'=>urlencode('请不要点击详情，详情：'.$value['details']),'color'=>'#86CCE6','font-size'=>'15px'), )
			            );
		}
		
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

	}

}
 ?>