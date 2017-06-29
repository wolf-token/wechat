<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = ((!empty($_GPC['op']) ? $_GPC['op'] : 'display'));
if ($operation == 'display') 
{
	$list = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_push') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY time DESC');
	foreach ($list as $key => $value ) 
	{
		$list[$key]['time'] = date('Y-m-d', $value['time']);
	}
}
else if ($operation == 'post') 
{
	$id = intval($_GPC['id']);
	if (empty($id)) 
	{
		ca('shop.push.add');
	}
	else 
	{
		ca('shop.push.edit|shop.push.view');
	}
	if (checksubmit('submit')) 
	{
		$data = array('uniacid' => $_W['uniacid'], 'name' => trim($_GPC['name']), 'content' => trim($_GPC['content']), 'description' => trim($_GPC['description']), 'time' => time());
		pdo_insert('sz_yi_push', $data);
		$id = pdo_insertid();
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/index.php?i=' . $_W['uniacid'] . '&c=entry&p=pushinfo&do=member&m=sz_yi&id=' . $id;
		require IA_ROOT . '/addons/sz_yi/core/inc/plugin/vendor/leancloud/src/autoload.php';
		$setdata = m('cache')->get('sysset');
		$set = unserialize($setdata['sets']);
		$app = $set['app']['base'];
		LeanCloud\LeanClient::initialize($app['leancloud']['id'], $app['leancloud']['key'], $app['leancloud']['master'] . ',master');
		$post_data = '{' . "\n" . '          "alert":             "' . $data['name'] . '",' . "\n" . '          "badge":             "1",' . "\n" . '          "content-available": "0",' . "\n" . '          "sound":             "1.wav",' . "\n" . '          "action_type":"1",' . "\n" . '          "title":            "' . $data['content'] . '",' . "\n" . '          "action":            "' . $app['leancloud']['notify'] . '",' . "\n" . '          "ext": {"id":"' . $id . '","url":"' . $url . '"}' . "\n" . '        }';
		$data = json_decode($post_data, true);
		$lean_push = new LeanCloud\LeanPush($data);
		$lean_push->send();
		message('更新推送成功！', $this->createWebUrl('shop/push', array('op' => 'display')), 'success');
	}
	$item = pdo_fetch('select * from ' . tablename('sz_yi_push') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
}
else if ($operation == 'delete') 
{
	ca('shop.push.delete');
	$id = intval($_GPC['id']);
	$item = pdo_fetch('SELECT id,name FROM ' . tablename('sz_yi_push') . ' WHERE id = \'' . $id . '\' AND uniacid=' . $_W['uniacid'] . '');
	if (empty($item)) 
	{
		message('抱歉，推送不存在或是已经被删除！', $this->createWebUrl('shop/push', array('op' => 'display')), 'error');
	}
	pdo_delete('sz_yi_push', array('id' => $id));
	message('推送删除成功！', $this->createWebUrl('shop/push', array('op' => 'display')), 'success');
}
load()->func('tpl');
include $this->template('web/shop/push');
?>