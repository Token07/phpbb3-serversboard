<?php

/**
*
* @package phpBB Extension - Servers Board
* @copyright (c) 2016 Token07
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace token07\serversboard\acp;

class serversboard_module
{
	var $u_action;
	function main($id, $mode)
	{
		global $phpbb_container;

		$admin_controller = $phpbb_container->get('token07.serversboard.admin_controller');
		$user = $phpbb_container->get('user');
		$request = $phpbb_container->get('request');
		$admin_controller->set_action($this->u_action);
		$user->add_lang_ext('token07/serversboard', 'acp/serversboard_acp');

		switch ($mode)
		{
			case 'servers':
				$this->tpl_name = 'serversboard_manage';
				$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
				$action = $request->variable('action', '');
				
				if (!empty($action))
				{
					$this->tpl_name = 'serversboard_manage';
					$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
					switch ($action)
					{
						case 'delete':
							$admin_controller->delete_server();
						break;
						case 'move_up':
						case 'move_down':
							$admin_controller->move_server($action);
						break;
						case 'edit':
							$this->tpl_name = 'serversboard_add';
							$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
							$server_id = $request->variable('server_id', -1);
							$admin_controller->edit_server($server_id);
						break;
					}
				}
				else
				{
					$this->tpl_name = 'serversboard_manage';
					$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
					$admin_controller->list_servers();
				}
			break;
			case 'settings':
				$this->tpl_name = 'serversboard_settings';
				$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
				$admin_controller->settings();
			break;
			case 'add':
				$this->tpl_name = 'serversboard_add';
				$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_ADD');
				$admin_controller->add_server();
			break;
			case 'test':
				$this->tpl_name = 'serversboard_test';
				$this->page_title = $user->lang('TOKEN07_SERVERSBOARD_ACP_TEST');
				$admin_controller->protocol_test();
		}
	}
}
