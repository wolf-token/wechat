<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class RankingWeb extends Plugin 
{
	protected $set;
	public function __construct() 
	{
		parent::__construct('ranking');
		$this->set = $this->getSet();
	}
	public function index() 
	{
		$_obfuscate_DSIqPiwPPyI2LQcdCAQhKAk8Gxg7AhE = &$GLOBALS['_W'];
		header('location: ' . $this->createPluginWebUrl('ranking/set'));
	}
	public function upgrade() 
	{
		$this->_exec_plugin('upgrade');
	}
	public function set() 
	{
		$this->_exec_plugin('set');
	}
}
?>