<?php
/**
 * 中移铁通湘西分公司模块微站定义
 *
 * @author 中移铁通湘西分公司
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Iw_moveModuleSite extends WeModuleSite {

	// public function doMobileIndex() {
	// 	//这个操作被定义用来呈现 功能封面
		
	// 	global $_W,$_GPC;
	// 	date_default_timezone_set('PRC');
		
	// 	$data1 = json_decode(file_get_contents("token.json"),true);

	// 	if ($data1->expire_time < time()) {

	// 		$account_api = WeAccount::create();
	// 		// $account_api->clearAccessToken();
	// 		$access_token = $account_api->getAccessToken();
	// 		date_default_timezone_set('PRC');
	// 		$data1->expire_time = time() + 7000;
 //        	$data1->access_token = $access_token;
	// 		 $fp = fopen("token.json", "w");
	//         fwrite($fp, json_encode($data1));
	//         fclose($fp);

	//     }else{
	    	
	//     	$access_token = $data1['access_token'];
	    	
	//     }
		 
 //        $json_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        
 //        $i = 2;
 //        $curl_data='';
        
 //        //永久 post的json数据
 //        $curl_data='{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$i.'}}}';
 //        $url = $json_url;
 //        $data = $curl_data;

 //        $ch = curl_init();
 //        $header = "Accept-Charset: utf-8";
 //        curl_setopt($ch, CURLOPT_URL, $url);
 //        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
 //        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
 //        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
 //        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 //        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
 //        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
 //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

 //        $tmpInfo = curl_exec($ch);

 //        if (curl_errno($ch)) {
 //            //curl_close( $ch )
 //            $json_info=json_decode($ch,true);
 //        }else{
 //            //curl_close( $ch ) 
 //            $json_info=json_decode($tmpInfo,true);
 //        }
 //        curl_close( $ch ) ;
        
      
 //        //这里代表生成成功，记录数据以便插入到数据库，方便以后统计查找
 //        if(!empty($json_info['ticket'])){ 


 //            $date['token']=$access_token;
 //            $date['tiket']=$json_info['ticket'];
 //            $date['url']=$json_info['url'];
 //            $date['scene_id']=$i;
 //            $data['openid']=$_W['fans']['openid'];
 //            $date['action_name']="QR_LIMIT_SCENE";
 //            $date['remark']='';
 //            $date['createtime']=time();

 //            if(!empty($date)){
 //            $res= pdo_insert("move_token",$date);//插入数据
 //            if($res){
	//                echo "你好";
	//             }else{
	//                 echo "no";
	//             }
	//         }else{
	//              echo "fuck";
	//         } 


 //         }
        

        
	// 	//include $this->template("index");
	// }

	public function doMobileCkeck() {
		//这个操作被定义用来呈现 功能封面
		global $_W,$_GPC;

		$action = 'check';
		$url = $this->createMobileUrl($action, array('op' => 'ja'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'ja';
		
 if ($operation == "ja") {
			
			if ($_W['isajax']) {
				
				$add['name'] = "河北工业大学";
				$add['position'] = "天津市";
				$add['lat'] = $_GPC['lat'];
				$add['lng'] = $_GPC['lng'];
				$add['time'] = date("Y-m-d H:i:s",time());

				$ja = pdo_insert("move_nearby",$add);
				//判断
				if (!empty($ja)) {
					
					return json_encode($ja);
				}else{

					return json_encode(false);
				}
				
			}
		}

		
		
}

	public function doWebControl() {
		//这个操作被定义用来呈现 规则列表
		global $_W,$_GPC;
		$action = 'control';
		$url = $this->createWebUrl($action, array('op' => 'ja'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'ja';

		if ($operation == "ja") {
		
		if ($_W['ispost']) {
			
			$update1['status'] = $_GPC['status'];
			
			$update1['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res = pdo_update("move_control",$update1,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					return json_encode($res);
					// echo "nice";
				}else{
					// echo "no";
					return json_encode($res);
				}
		}
	}else if ($operation == "la") {
		
		if ($_W['ispost']) {
			
			$update1['status'] = $_GPC['status'];
			
			$update1['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res1 = pdo_update("move_control",$update1,array("id"=>$_GPC['id']));
				if (!empty($res1)) {
					
					// echo "yes";
					return json_encode($res1);

				}else{
					// echo "no";
					return json_encode($res1);
				}
		}
	}

	}
	// public function doWebInteract() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebStore() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebQuestion() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebFault() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebRecommend() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebUser() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebStaff() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }
	// public function doWebMission() {
	// 	//这个操作被定义用来呈现 管理中心导航菜单
	// }

}