<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
global $_W;
global $_GPC;
@session_start();
setcookie('preUrl', $_W['siteurl']);
$openid = m('user')->getOpenid();
$popenid = m('user')->islogin();
$openid = (($openid ? $openid : $popenid));
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$goodsid = intval($_GPC['id']);
$params = array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid);
$sql = 'SELECT count(id) FROM ' . tablename('sz_yi_order_comment') . ' where 1 and uniacid = :uniacid and goodsid=:goodsid and deleted=0 ORDER BY `id` DESC';
$commentcount = pdo_fetchcolumn($sql, $params);
$goods = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_goods') . ' WHERE id = :id limit 1', array(':id' => $goodsid));
if ($goods['pcate']) 
{
	$pcate = pdo_fetchcolumn(' select name from ' . tablename('sz_yi_category') . ' where id =' . $goods['pcate'] . ' and uniacid=' . $uniacid);
}
if ($goods['ccate']) 
{
	$ccate = pdo_fetchcolumn(' select name from ' . tablename('sz_yi_category') . ' where id =' . $goods['ccate'] . ' and uniacid=' . $uniacid);
}
if ($goods['tcate']) 
{
	$tcate = pdo_fetchcolumn(' select name from ' . tablename('sz_yi_category') . ' where id =' . $goods['tcate'] . ' and uniacid=' . $uniacid);
}
$shop = set_medias(m('common')->getSysset('shop'), 'logo');
$shop['url'] = $this->createMobileUrl('shop');
$mid = intval($_GPC['mid']);
$shopset = m('common')->getSysset('shop');
$opencommission = false;
if (p('commission')) 
{
	if (empty($member['agentblack'])) 
	{
		$cset = p('commission')->getSet();
		$opencommission = 0 < intval($cset['level']);
		if ($opencommission) 
		{
			if (empty($mid)) 
			{
				if (($member['isagent'] == 1) && ($member['status'] == 1)) 
				{
					$mid = $member['id'];
				}
			}
			if (!empty($mid)) 
			{
				if (empty($cset['closemyshop'])) 
				{
					$shop = set_medias(p('commission')->getShop($mid), 'logo');
					$shop['url'] = $this->createPluginMobileUrl('commission/myshop', array('mid' => $mid));
				}
			}
			$commission_text = ((empty($cset['buttontext']) ? '我要分销' : $cset['buttontext']));
		}
	}
}
$showdiyform = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) 
{
	$diyformtype = $goods['diyformtype'];
	$diyformid = $goods['diyformid'];
	$diymode = $goods['diymode'];
	if (!empty($diyformtype) && !empty($diyformid)) 
	{
		$formInfo = $diyform_plugin->getDiyformInfo($diyformid);
		$fields = $formInfo['fields'];
		$f_data = $diyform_plugin->getLastData(3, $diymode, $diyformid, $goodsid, $fields, $member);
	}
	if ($_W['isajax'] && ($_GPC['op'] == 'create')) 
	{
		$insert_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);
		$idata = $insert_data['data'];
		$goods_temp = $diyform_plugin->getGoodsTemp($goodsid, $diyformid, $openid);
		$insert = array('cid' => $goodsid, 'openid' => $openid, 'diyformid' => $diyformid, 'type' => 3, 'diyformfields' => iserializer($fields), 'diyformdata' => $idata, 'uniacid' => $_W['uniacid']);
		if (empty($goods_temp)) 
		{
			pdo_insert('sz_yi_diyform_temp', $insert);
			$gdid = pdo_insertid();
		}
		else 
		{
			pdo_update('sz_yi_diyform_temp', $insert, array('id' => $goods_temp['id']));
			$gdid = $goods_temp['id'];
		}
		show_json(1, array('goods_data_id' => $gdid));
	}
}
$html = $goods['content'];
preg_match_all('/<img.*?src=[\\\'| "](.*?(?:[\\.gif|\\.jpg]?))[\\\'|"].*?[\\/]?>/', $html, $imgs);
if (isset($imgs[1])) 
{
	foreach ($imgs[1] as $img ) 
	{
		$im = array('old' => $img, 'new' => tomedia($img));
		$images[] = $im;
	}
	if (isset($images)) 
	{
		foreach ($images as $img ) 
		{
			$html = str_replace($img['old'], $img['new'], $html);
		}
	}
	$goods['content'] = $html;
}
$levelid = $member['level'];
$groupid = $member['groupid'];
if (!is_weixin()) 
{
	if ($goods['showlevels'] != '') 
	{
		$showlevels = explode(',', $goods['showlevels']);
		if (!in_array($levelid, $showlevels)) 
		{
			message('当前商品禁止访问，请联系客服……', $this->createMobileUrl('shop/index'), 'error');
		}
	}
	if ($goods['showgroups'] != '') 
	{
		$showgroups = explode(',', $goods['showgroups']);
		if (!in_array($groupid, $showgroups)) 
		{
			message('当前商品禁止访问，请联系客服……', $this->createMobileUrl('shop/index'), 'error');
		}
	}
}
$commissionprice = p('commission')->getCommission($goods);
if ($_W['isajax']) 
{
	if (empty($goods)) 
	{
		show_json(0);
	}
	$goods = set_medias($goods, 'thumb');
	$goods['canbuy'] = !empty($goods['status']) && empty($goods['deleted']);
	$goods['timestate'] = '';
	$goods['userbuy'] = '1';
	if (0 < $goods['usermaxbuy']) 
	{
		$order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $goodsid, ':uniacid' => $uniacid, ':openid' => $openid));
		if ($goods['usermaxbuy'] <= $order_goodscount) 
		{
			$goods['userbuy'] = 0;
		}
	}
	$goods['levelbuy'] = '1';
	if ($goods['buylevels'] != '') 
	{
		$buylevels = explode(',', $goods['buylevels']);
		if (!in_array($levelid, $buylevels)) 
		{
			$goods['levelbuy'] = 0;
		}
	}
	$goods['groupbuy'] = '1';
	if ($goods['buygroups'] != '') 
	{
		$buygroups = explode(',', $goods['buygroups']);
		if (!in_array($groupid, $buygroups)) 
		{
			$goods['groupbuy'] = 0;
		}
	}
	$goods['timebuy'] = '0';
	if ($goods['istime'] == 1) 
	{
		if (time() < $goods['timestart']) 
		{
			$goods['timebuy'] = '-1';
			$goods['timestate'] = 'before';
			$goods['buymsg'] = '限时购活动未开始';
		}
		else if ($goods['timeend'] < time()) 
		{
			$goods['timebuy'] = '1';
			$goods['buymsg'] = '限时购活动已经结束';
		}
		else 
		{
			$goods['timestate'] = 'after';
		}
	}
	$goods['canaddcart'] = true;
	if (($goods['isverify'] == 2) || ($goods['type'] == 2) || ($goods['type'] == 3)) 
	{
		$goods['canaddcart'] = false;
	}
	$pics = array($goods['thumb']);
	$thumburl = unserialize($goods['thumb_url']);
	if (is_array($thumburl)) 
	{
		$pics = array_merge($pics, $thumburl);
	}
	unset($thumburl);
	$pics = set_medias($pics);
	$marketprice = $goods['marketprice'];
	$productprice = $goods['productprice'];
	$maxprice = $marketprice;
	$minprice = $marketprice;
	$stock = $goods['total'];
	$allspecs = array();
	if (!empty($goods['hasoption'])) 
	{
		$allspecs = pdo_fetchall('select * from ' . tablename('sz_yi_goods_spec') . ' where goodsid=:id order by displayorder asc', array(':id' => $goodsid));
		foreach ($allspecs as &$s ) 
		{
			$items = pdo_fetchall('select * from ' . tablename('sz_yi_goods_spec_item') . ' where  `show`=1 and specid=:specid order by displayorder asc', array(':specid' => $s['id']));
			$s['items'] = set_medias($items, 'thumb');
		}
		unset($s);
	}
	$options = array();
	if (!empty($goods['hasoption'])) 
	{
		$options = pdo_fetchall('select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from ' . tablename('sz_yi_goods_option') . ' where goodsid=:id order by id asc', array(':id' => $goodsid));
		$options = set_medias($options, 'thumb');
		foreach ($options as $o ) 
		{
			if ($maxprice < $o['marketprice']) 
			{
				$maxprice = $o['marketprice'];
			}
			if (($o['marketprice'] < $minprice) && (0 < $o['marketprice'])) 
			{
				$minprice = $o['marketprice'];
			}
		}
		$goods['maxprice'] = $maxprice;
		$goods['minprice'] = $minprice;
	}
	$specs = $allspecs;
	$params = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_goods_param') . ' WHERE uniacid=:uniacid and goodsid=:goodsid order by displayorder asc', array(':uniacid' => $uniacid, ':goodsid' => $goods['id']));
	$fcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_favorite') . ' where uniacid=:uniacid and openid=:openid and goodsid=:goodsid and deleted=0 ', array(':uniacid' => $uniacid, ':openid' => $openid, ':goodsid' => $goods['id']));
	pdo_query('update ' . tablename('sz_yi_goods') . ' set viewcount=viewcount+1 where id=:id and uniacid=\'' . $uniacid . '\' ', array(':id' => $goodsid));
	$history = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_history') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0 limit 1', array(':goodsid' => $goodsid, ':uniacid' => $uniacid, ':openid' => $openid));
	$history_goods = set_medias(pdo_fetchall('select g.* from ' . tablename('sz_yi_member_history') . ' h ' . ' left join ' . tablename('sz_yi_goods') . ' g on h.goodsid = g.id  where  h.uniacid=:uniacid and h.openid=:openid and h.deleted=0 and g.deleted = 0  order by h.createtime desc limit 5', array(':uniacid' => $uniacid, ':openid' => $openid)), 'thumb');
	if ($history <= 0) 
	{
		$history = array('uniacid' => $uniacid, 'openid' => $openid, 'goodsid' => $goodsid, 'deleted' => 0, 'createtime' => time());
		pdo_insert('sz_yi_member_history', $history);
	}
	$level = m('member')->getLevel($openid);
	$discounts = json_decode($goods['discounts'], true);
	if (is_array($discounts)) 
	{
		if (!empty($level['id'])) 
		{
			if ((0 < $discounts['level' . $level['id']]) && ($discounts['level' . $level['id']] < 10)) 
			{
				$level['discount'] = $discounts['level' . $level['id']];
			}
		}
		else 
		{
			$level['levelname'] = (empty($shopset['levelname']) ? '普通会员' : $shopset['levelname']);
			if ((0 < $discounts['default']) && ($discounts['default'] < 10)) 
			{
				$level['discount'] = $discounts['default'];
			}
			else 
			{
				$level['discount'] = 10;
			}
		}
	}
	$comment = set_medias(pdo_fetchall('select * from ' . tablename('sz_yi_goods_comment') . ' where goodsid=:id and uniacid=:uniacid', array(':id' => $goodsid, ':uniacid' => $uniacid)), 'headimgurl');
	$commentcount = pdo_fetchcolumn('select count(id) from ' . tablename('sz_yi_goods_comment') . ' where goodsid=:id and uniacid=:uniacid', array(':id' => $goodsid, ':uniacid' => $uniacid));
	if ($goods['tcate']) 
	{
		$ishot = set_medias(pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where tcate=:tcate and pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0   order by sales desc limit 10', array(':uniacid' => $uniacid, ':tcate' => $goods['tcate'], ':pcate' => $goods['pcate'], ':ccate' => $goods['ccate'])), 'thumb');
	}
	else if ($goods['ccate']) 
	{
		$ishot = set_medias(pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0 order by sales desc limit 10', array(':uniacid' => $uniacid, ':pcate' => $goods['pcate'], ':ccate' => $goods['ccate'])), 'thumb');
	}
	else if ($goods['pcate']) 
	{
		$ishot = set_medias(pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where pcate=:pcate  and uniacid=:uniacid and deleted = 0 order by sales desc limit 10', array(':uniacid' => $uniacid, ':pcate' => $goods['pcate'])), 'thumb');
	}
	else 
	{
		$ishot = set_medias(pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and deleted = 0 order by sales desc limit 10', array(':uniacid' => $uniacid)), 'thumb');
	}
	$category = m('shop')->getCategory();
	$stores = array();
	if ($goods['isverify'] == 2) 
	{
		$storeids = array();
		if (!empty($goods['storeids'])) 
		{
			$storeids = array_merge(explode(',', $goods['storeids']), $storeids);
		}
		if (empty($storeids)) 
		{
			$stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
		}
		else 
		{
			$stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
		}
	}
	$followed = m('user')->followed($openid);
	$followurl = ((empty($goods['followurl']) ? $shop['followurl'] : $goods['followurl']));
	$followtip = ((empty($goods['followtip']) ? '如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~' : $goods['followtip']));
	$sale_plugin = p('sale');
	$saleset = false;
	if ($sale_plugin) 
	{
		$saleset = $sale_plugin->getSet();
		$saleset['enoughs'] = $sale_plugin->getEnoughs();
	}
	$ret = array('goods' => $goods, 'followed' => ($followed ? 1 : 0), 'followurl' => $followurl, 'followtip' => $followtip, 'saleset' => $saleset, 'shopset' => $shopset, 'pics' => $pics, 'options' => $options, 'specs' => $specs, 'params' => $params, 'commission' => $opencommission, 'commission_text' => $commission_text, 'level' => $level, 'shop' => $shop, 'goodscount' => pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and status=1 and deleted=0 ', array(':uniacid' => $uniacid)), 'cartcount' => pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', array(':uniacid' => $uniacid, ':openid' => $openid)), 'isfavorite' => 0 < $fcount, 'stores' => $stores, 'comment' => $comment, 'commentcount' => $commentcount, 'ishot' => $ishot, 'history' => $history_goods, 'category' => $category);
	$commission = p('commission');
	if ($commission) 
	{
		$shopid = $shop['mid'];
		if (!empty($shopid)) 
		{
			$myshop = set_medias($commission->getShop($shopid), array('img', 'logo'));
		}
	}
	if (!empty($myshop['selectgoods']) && !empty($myshop['goodsids'])) 
	{
		$ret['goodscount'] = count(explode(',', $myshop['goodsids']));
	}
	$ret['detail'] = array('logo' => (!empty($goods['detail_logo']) ? tomedia($goods['detail_logo']) : $shop['logo']), 'shopname' => (!empty($goods['detail_shopname']) ? $goods['detail_shopname'] : $shop['name']), 'totaltitle' => trim($goods['detail_totaltitle']), 'btntext1' => trim($goods['detail_btntext1']), 'btnurl1' => (!empty($goods['detail_btnurl1']) ? $goods['detail_btnurl1'] : $this->createMobileUrl('shop/list')), 'btntext2' => trim($goods['detail_btntext2']), 'btnurl2' => (!empty($goods['detail_btnurl2']) ? $goods['detail_btnurl2'] : $shop['url']));
	show_json(1, $ret);
}
$_W['shopshare'] = array('title' => (!empty($goods['share_title']) ? $goods['share_title'] : $goods['title']), 'imgUrl' => (!empty($goods['share_icon']) ? tomedia($goods['share_icon']) : tomedia($goods['thumb'])), 'desc' => (!empty($goods['description']) ? $goods['description'] : $shop['name']), 'link' => $this->createMobileUrl('shop/detail', array('id' => $goods['id'])));
$com = p('commission');
if ($com) 
{
	$cset = $com->getSet();
	if (!empty($cset)) 
	{
		if (($member['isagent'] == 1) && ($member['status'] == 1)) 
		{
			$_W['shopshare']['link'] = $this->createMobileUrl('shop/detail', array('id' => $goods['id'], 'mid' => $member['id']));
			if (empty($cset['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) 
			{
				$trigger = true;
			}
		}
		else if (!empty($_GPC['mid'])) 
		{
			$_W['shopshare']['link'] = $this->createMobileUrl('shop/detail', array('id' => $goods['id'], 'mid' => $_GPC['mid']));
		}
	}
}
$this->setHeader();
include $this->template('shop/detail');
?>