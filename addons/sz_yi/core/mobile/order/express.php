<?php
if (!defined('IN_IA'))
{
	exit('Access Denied');
}
error_reporting(7);
defined('EBusinessID') or define('EBusinessID', '1278705');
defined('AppKey') or define('AppKey', '7609137b-5980-4a02-ba87-cc6115966aff');
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx'); //单号识别
defined('ChaURL') or define('ChaURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');//快递查询
global $_W;
global $_GPC;
$operation = ((!empty($_GPC['op']) ? $_GPC['op'] : 'display'));
$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['id']);
if ($_W['isajax'])
{
	if ($operation == 'display')
	{
		$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		if (empty($order))
		{
			show_json(0);
		}
		$goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));
		$goods = set_medias($goods, 'thumb');
		$order['goodstotal'] = count($goods);
		$set = set_medias(m('common')->getSysset('shop'), 'logo');
		show_json(1, array('order' => $order, 'goods' => $goods, 'set' => $set));
	}
	else if ($operation == 'step')
	{
		$express = trim($_GPC['express']);//订单号
		$kuaidi2 = trim($_GPC['expresssn']);
		$kuaidi2=20000026773199;
		$fhshibie = kuaidi_bianma($kuaidi2);
		$map=json_decode($fhshibie,true);
		$sbtzm=$map['Shippers'][0]['ShipperCode'];
		$sbtgs=$map['Shippers'][0]['ShipperName'];
		$jieg = kuaidi_cha($sbtzm,$kuaidi2);
		$map3=json_decode($jieg,true);
		array_reverse($map3['Traces']);
		show_json(1, array('list' => $map3['Traces']));
	}
}
include $this->template('order/express');
function sortByTime($a, $b)
{
	if ($a['ts'] == $b['ts'])
	{
		return 0;
	}
	return ($b['ts'] < $a['ts'] ? 1 : -1);
}

/**
 * 单号识别
 */
function kuaidi_bianma($kuaididanhao){
	$requestData= "{'LogisticCode':'$kuaididanhao'}";
	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '2002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = kuaidi_encrypt($requestData, AppKey);
	$result=kuaidi_post(ReqURL, $datas);
	return $result;
}
/**
 * Json方式 查询订单物流轨迹
 */
function kuaidi_cha($bianma,$danhao){
	$requestData= "{'OrderCode':'','ShipperCode':'$bianma','LogisticCode':'$danhao'}";
	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = kuaidi_encrypt($requestData, AppKey);
	$result=kuaidi_post(ChaURL, $datas);
	return $result;
}
function kuaidi_encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}
/**
 *  post提交数据
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据
 * @return url响应返回的html
 */
function kuaidi_post($url, $datas) {
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);

    return $gets;
}