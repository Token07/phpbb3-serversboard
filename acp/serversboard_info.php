<?php
/**
*
* @package phpBB Extension - Servers Board
* @copyright (c) 2016 Token07
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace token07\serversboard\acp;

class serversboard_info
{
	function module()
	{
		return array(
			'filename'	=> '\token07\serversboard\acp\serversboard_module',
			'title'		=> 'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
			'modes'		=> array(
				'servers'	=> array(
					'title' => 'TOKEN07_SERVERSBOARD_ACP_MANAGE',
					'auth'	=> 'ext_token07/serversboard && acl_a_serversboard',
					'cat'	=> 'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
				),
				'settings'	=> array(
					'title' => 'GENERAL_SETTINGS',
					'auth'	=> 'ext_token07/serversboard && acl_a_serversboard',
					'cat'	=> 'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
				),
				'add'	=> array(
					'title' => 'TOKEN07_SERVERSBOARD_ACP_ADD',
					'auth'	=> 'ext_token07/serversboard && acl_a_serversboard',
					'cat'	=> 'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
				),
				'test'	=>	array(
					'title' => 'TOKEN07_SERVERSBOARD_ACP_TEST',
					'auth'	=> 'ext_token07/serversboard && acl_a_serversboard',
					'cat'	=> 'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
				),
			),
		);
	}
}
