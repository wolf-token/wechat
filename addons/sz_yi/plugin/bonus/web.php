<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class BonusWeb extends Plugin 
{
	protected $set;
	public function __construct() 
	{
		parent::__construct('bonus');
		$this->set = $this->getSet();
	}
	public function index() 
	{
		$_obfuscate_DS0fLhYTNQ8PMx8tLCM8Ch9cNEAwEhE = &$GLOBALS['_W'];
		if (cv('bonus.agent')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/agent'));
			exit();
			return NULL;
		}
		if (cv('bonus.notice')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/notice'));
			exit();
			return NULL;
		}
		if (cv('bonus.set')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/set'));
			exit();
			return NULL;
		}
		if (cv('bonus.level')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/level'));
			exit();
			return NULL;
		}
		if (cv('bonus.cover')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/cover'));
			exit();
			return NULL;
		}
		if (cv('bonus.send')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/send'));
			exit();
			return NULL;
		}
		if (cv('bonus.sendall')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/sendall'));
			exit();
			return NULL;
		}
		if (cv('bonus.order')) 
		{
			header('location: ' . $this->createPluginWebUrl('bonus/order'));
			exit();
		}
	}
	public function upgrade() 
	{
		$this->_exec_plugin('upgrade');
	}
	public function agent() 
	{
		$this->_exec_plugin('agent');
	}
	public function level() 
	{
		$this->_exec_plugin('level');
	}
	public function send() 
	{
		$this->_exec_plugin('send');
	}
	public function sendall() 
	{
		$this->_exec_plugin('sendall');
	}
	public function notice() 
	{
		$this->_exec_plugin('notice');
	}
	public function cover() 
	{
		$this->_exec_plugin('cover');
	}
	public function set() 
	{
		$this->_exec_plugin('set');
	}
	public function detail() 
	{
		$this->_exec_plugin('detail');
	}
	public function order() 
	{
		$this->_exec_plugin('order');
	}
}
?>