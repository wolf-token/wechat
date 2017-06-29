<?php
global $_W;
global $_GPC;
$operation = ((empty($_GPC['op']) ? 'display' : $_GPC['op']));
if ($operation == 'display') 
{
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$roleid = $this->model->getRoleId();
	$where = '';
	if (empty($_GPC['uid'])) 
	{
		$where .= ' and uniacid=' . $_W['uniacid'];
	}
	else 
	{
		$where .= ' and uid="' . $_GPC['uid'] . '" and uniacid=' . $_W['uniacid'];
	}
	if (p('merchant') && !empty($_GPC['member_id'])) 
	{
		$ismerchant = true;
		$member_id = intval($_GPC['member_id']);
		$uids = p('merchant')->getAllSupplierUids($_GPC['member_id']);
		$list = pdo_fetchall('select * from ' . tablename('sz_yi_perm_user') . ' where roleid=' . $roleid . ' and uniacid=' . $_W['uniacid'] . ' and uid in (' . $uids . ') LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize);
	}
	else 
	{
		$ismerchant = false;
		$list = pdo_fetchall('select * from ' . tablename('sz_yi_perm_user') . ' where roleid=' . $roleid . ' ' . $where . ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize);
	}
	$total = count($list);
	$pager = pagination($total, $pindex, $psize);
}
else if ($operation == 'detail') 
{
	$applyid = intval($_GPC['applyid']);
	$finish = $_GPC['type'];
	if (!empty($applyid)) 
	{
		$apply_info = pdo_fetch('select * from ' . tablename('sz_yi_supplier_apply') . ' where uniacid=' . $_W['uniacid'] . ' and id=' . $applyid);
		$openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and uid=\'' . $apply_info['uid'] . '\'');
		if (!empty($openid)) 
		{
			$supplierinfo = pdo_fetch('select * from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and uid=\'' . $apply_info['uid'] . '\'');
		}
		else 
		{
			$supplierinfo = pdo_fetch('select * from ' . tablename('sz_yi_af_supplier') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $openid . '\'');
		}
		if (!empty($supplierinfo['openid'])) 
		{
			$saler = m('member')->getInfo($supplierinfo['openid']);
			$avatar = $saler['avatar'];
		}
		$diyform_flag = 0;
		$diyform_plugin = p('diyform');
		if ($diyform_plugin) 
		{
			if (!empty($supplierinfo['diymemberdata'])) 
			{
				$diyform_flag = 1;
				$fields = iunserializer($supplierinfo['diymemberfields']);
			}
		}
	}
	else 
	{
		$uid = intval($_GPC['uid']);
		$openid = $_GPC['openid'];
		if (empty($openid)) 
		{
			$supplierinfo = pdo_fetch('select * from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and uid=\'' . $uid . '\'');
		}
		else 
		{
			$supplierinfo = pdo_fetch('select * from ' . tablename('sz_yi_af_supplier') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $openid . '\'');
			if (empty($supplierinfo)) 
			{
				$supplierinfo = pdo_fetch('select * from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $openid . '\'');
				$uid = pdo_fetchcolumn('select uid from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $openid . '\'');
			}
			$uid = pdo_fetchcolumn('select uid from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $openid . '\'');
		}
		if (!empty($supplierinfo['openid'])) 
		{
			$saler = m('member')->getInfo($supplierinfo['openid']);
		}
		$diyform_flag = 0;
		$diyform_plugin = p('diyform');
		if ($diyform_plugin) 
		{
			if (!empty($supplierinfo['diymemberdata'])) 
			{
				$diyform_flag = 1;
				$fields = iunserializer($supplierinfo['diymemberfields']);
			}
		}
		$supinfo = $this->model->getSupplierInfo($uid);
		$totalmoney = $supinfo['totalmoney'];
		$totalmoneyok = $supinfo['costmoney'];
		if (checksubmit('submit')) 
		{
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			if (!empty($data['openid'])) 
			{
				$result = pdo_fetch('select * from ' . tablename('sz_yi_perm_user') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $data['openid'] . '\'');
				if (!empty($result)) 
				{
					if ($data['openid'] != $supplierinfo['openid']) 
					{
						message('该微信已绑定，请更换!', $this->createPluginWebUrl('supplier/supplier'), 'error');
					}
					else 
					{
						pdo_update('sz_yi_perm_user', $data, array('openid' => $openid));
						message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
					}
				}
				else 
				{
					pdo_update('sz_yi_perm_user', $data, array('openid' => $openid));
					message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
				}
			}
			else 
			{
				pdo_update('sz_yi_perm_user', $data, array('uid' => $uid));
				message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
			}
		}
	}
}
else if ($operation == 'merchant') 
{
	$uid = intval($_GPC['uid']);
	$merchants = $this->model->getSupplierMerchants($uid);
	$total = count($merchants);
}
else if ($operation == 'merchant_post') 
{
	$uid = intval($_GPC['uid']);
	if (checksubmit('submit')) 
	{
		$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
		if (empty($data['openid'])) 
		{
			message('请选择微信!', referer(), 'error');
		}
		else 
		{
			$result = pdo_fetch('select * from ' . tablename('sz_yi_merchants') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $data['openid'] . '\' and supplier_uid=' . $uid);
			if (!empty($result)) 
			{
				message('该微信角色已经是此供应商的招商员!', referer(), 'error');
			}
			else 
			{
				$data['member_id'] = pdo_fetchcolumn('select id from ' . tablename('sz_yi_member') . ' where uniacid=' . $_W['uniacid'] . ' and openid=\'' . $data['openid'] . '\'');
			}
		}
		if (empty($data['commissions'])) 
		{
			message('请输入佣金比例!', referer(), 'error');
		}
		$data['uniacid'] = $_W['uniacid'];
		$data['supplier_uid'] = $uid;
		pdo_insert('sz_yi_merchants', $data);
		message('添加招商员成功!', $this->createPluginWebUrl('supplier/supplier', array('op' => 'merchant', 'uid' => $uid)), 'success');
	}
}
else if ($operation == 'merchant_delete') 
{
	$id = intval($_GPC['id']);
	if (empty($id)) 
	{
		message('未找到该招商员!', referer(), 'error');
	}
	pdo_delete('sz_yi_merchants', array('id' => $id, 'uniacid' => $_W['uniacid']));
	message('删除成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('supplier');
?>