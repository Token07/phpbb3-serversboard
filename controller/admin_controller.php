<?php

/**
*
* @package phpBB Extension - Servers Board
* @copyright (c) 2016 Token07
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace token07\serversboard\controller;

class admin_controller
{
	protected $config;
	protected $request;
	protected $template;
	protected $user;
	protected $db;
	protected $phpbb_log;
	protected $serversboard_table;
	protected $u_action;

	public function __construct(\phpbb\config\config $config, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\factory $db, \phpbb\log\log $phpbb_log, $serversboard_table)
	{
		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_log = $phpbb_log;
		$this->serversboard_table = $serversboard_table;
		
		$this->user->add_lang_ext('token07/serversboard', 'acp/serversboard_acp');
	}
	public function set_action($u_action)
	{
		$this->u_action = $u_action;
	}
	public function list_servers()
	{
		$this->generate_server_list();
	}
	public function add_server()
	{
		global $phpbb_container;

		add_form_key('token07/serversboard');
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('token07/serversboard'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			$server_ip = $this->request->variable('token07_serversboard_ip', '');
			$server_port = $this->request->variable('token07_serversboard_port', 0);
			$server_name = $this->request->variable('token07_serversboard_hostname', '');
			$server_protocol = $this->request->variable('token07_serversboard_servertype', '');
			$server_queryport = $this->request->variable('token07_serversboard_queryport', '');

			// Validate IP and port
			if (!filter_var($server_ip, FILTER_VALIDATE_IP))
			{
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_INVALIDIP') . adm_back_link($this->u_action . "&amp;server_ip=$server_ip&amp;server_port=$server_port"), E_USER_WARNING);
			}
			if ($server_port <= 0 || $server_port >= 65535)
			{
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_INVALIDPORT') . adm_back_link($this->u_action . "&amp;server_ip=$server_ip&amp;server_port=$server_port"), E_USER_WARNING);
			}
			
			// Find the highest id number 
			$result = $this->db->sql_query('SELECT MAX(server_order) AS max FROM ' . $this->serversboard_table);
			if (!$row = $this->db->sql_fetchrow($result))
			{
				$max = 1;
			}
			else
			{
				$max = $row['max']+1;
			}
			$this->db->sql_freeresult($result);

			$server_ip = $server_ip . ':' . $server_port;
			$server_name = $server_name;
			$columns = array(
				'server_ip'			=> $server_ip,
				'server_order'		=> $max,
				'server_hostname'	=> $server_name,
				'server_players'	=> '0 / 0',
				'server_playerlist'	=> '[]',
				'server_lastupdate'	=> 0,
				'server_query_port'	=> (empty($server_queryport)) ? NULL : $server_queryport,
				'server_type'		=> $server_protocol,
			);
			$sql = 'INSERT INTO ' . $this->serversboard_table . ' ' . $this->db->sql_build_array('INSERT', $columns);
			$this->db->sql_query($sql);

			$task = $phpbb_container->get('token07.serversboard.cron.task.update_serversboard');
			$task->run($this->db->sql_nextid());
			
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'TOKEN07_SERVERSBOARD_ACP_LOG_ADDED', time(), array($server_ip));
			trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_ADDED'). adm_back_link($this->u_action));
		}
		$this->generate_protocol_list();
	}
	public function delete_server()
	{
		if (confirm_box(true))
		{
			$server_id = $this->request->variable('server_id', 0);
			$result = $this->db->sql_query('SELECT server_ip FROM ' . $this->serversboard_table . ' WHERE server_id = ' . (int) $server_id);
			if ($row = $this->db->sql_fetchrow($result))
			{
				$this->db->sql_query('DELETE FROM ' . $this->serversboard_table . ' WHERE server_id = ' . (int) $server_id);
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'TOKEN07_SERVERSBOARD_ACP_LOG_DELETE', time(), array($row['server_ip']));
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_DELETED') . adm_back_link($this->u_action));
			}
			trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_NO_SERVER') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		elseif (isset($_POST['cancel']))
		{
			redirect($this->u_action);
		}
		$fields = build_hidden_fields(array(
			'action' => 'delete',
			'server_id'	=> $this->request->variable('server_id', 0),
		));
		confirm_box(false, $this->user->lang('TOKEN07_SERVERSBOARD_ACP_CONFIRMDEL'), $fields);
	}
	public function move_server($action)
	{
		$status = $this->move($this->request->variable('server_id', 0), $action == 'move_up' ? 1 : -1);
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array('success' => $status));
			return;
		}
		$this->generate_server_list();
	}
	public function edit_server($server_id)
	{
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('token07/serversboard'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			$ip = $this->request->variable('token07_serversboard_ip', '');
			$port = $this->request->variable('token07_serversboard_port', 0);
			$hostname = $this->request->variable('token07_serversboard_hostname', '', true);
			$protocol = $this->request->variable('token07_serversboard_servertype', '');
			$query_port = $this->request->variable('token07_serversboard_queryport', 0);
			$server_ip = $ip . ":" . $port;
			$server_id = $this->request->variable('server_id', -1);
			$server_show_gt = $this->request->variable('token07_serversboard_gametracker', true);
			$server_show_times = $this->request->variable('token07_serversboard_timeonline', false);
			if (empty($query_port) || $query_port == 0)
			{
				$query_port = NULL;
			}
			$data = array(
				'server_hostname' 				=> $hostname,
				'server_ip'						=> $server_ip,
				'server_type'					=> $protocol,
				'server_query_port'				=> $query_port,
				'server_show_gametracker'		=> $server_show_gt,
				'server_show_time_online'		=> $server_show_times,
			);
			$sql = 'UPDATE ' . $this->serversboard_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE server_id = ' . $server_id;
			$this->db->sql_query($sql);
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'TOKEN07_SERVERSBOARD_ACP_LOG_UPDATED', time(), array($server_ip));
			trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_UPDATED'). adm_back_link($this->u_action));
		}

		$this->tpl_name = 'serversboard_add';
		$this->page_title = $this->user->lang('TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD');
		$server_id = $this->request->variable('server_id', -1);
		if ($server_id == -1)
		{
			trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_NO_SERVER') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		$result = $this->db->sql_query('SELECT server_hostname, server_ip, server_query_port, server_type, server_query_port, server_show_gametracker, server_show_time_online FROM ' . $this->serversboard_table . ' WHERE server_id = ' . $server_id);
		if ($row = $this->db->sql_fetchrow($result))
		{
			add_form_key('token07/serversboard');
			$this->template->assign_vars(array(
				'SERVER_ID'			=> $server_id,
				'SERVER_HOSTNAME'	=> htmlentities($row['server_hostname']),
				'SERVER_IP'			=> substr($row['server_ip'], 0, strpos($row['server_ip'], ':')),
				'SERVER_PORT'		=> substr($row['server_ip'], strpos($row['server_ip'], ':') + 1),
				'SERVER_PROTOCOL'	=> htmlentities($row['server_type']),
				'SERVER_QUERY_PORT'	=> $row['server_query_port'],
				'SERVER_SHOW_GT'	=> $row['server_show_gametracker'],
				'SERVER_SHOW_TIME'	=> $row['server_show_time_online'],
			));
			$this->generate_protocol_list();
			$this->template->assign_var('EDITING_SERVER', true);
		}
		else
		{
			trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_NO_SERVER') . adm_back_link($this->u_action), E_USER_WARNING);
		}
	}
	public function settings()
	{
		add_form_key('token07/serversboard');
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('token07/serversboard'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			$this->config->set('serversboard_enable', $this->request->variable('token07_serversboard_enable', 1));
			$this->config->set('serversboard_navbar_link_enable', $this->request->variable('token07_serversboard_navbar_link_enable', 0));
			$this->config->set('serversboard_update_time', $this->request->variable('token07_serversboard_interval', 1));
			
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'TOKEN07_SERVERSBOARD_ACP_LOG_UPDATE', time());
			trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}
		$this->template->assign_vars(array(
			'TOKEN07_SERVERSBOARD_ENABLE'				=> $this->config['serversboard_enable'],
			'TOKEN07_SERVERSBOARD_INTERVAL'				=> $this->config['serversboard_update_time'],
			'TOKEN07_SERVERSBOARD_NAVBAR_LINK_ENABLE'	=> $this->config['serversboard_navbar_link_enable'],
		));
	}
	public function protocol_test()
	{
		if ($this->request->is_set_post('submit'))
		{
			$server_ip = $this->request->variable('server_ip', '');
			$server_port = $this->request->variable('server_port', 0);
			$server_protocol = $this->request->variable('server_protocol', '');
			$server_query_port = $this->request->variable('server_query_port', 0);
			if (empty($server_ip) || empty($server_port) || empty($server_protocol))
			{
				trigger_error('FORM_INVALID');
			}
			$socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
			if (!$socket)
			{
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_OPEN', socket_strerror(socket_last_error($socket))), E_USER_WARNING);
			}
			if (!@socket_connect($socket, $server_ip, $server_port))
			{
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_OPEN', socket_strerror(socket_last_error($socket))), E_USER_WARNING);
			}
			if (!@socket_write($socket,"\x00"))
			{
				trigger_error($this->user->lang('TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_OPEN', socket_strerror(socket_last_error($socket))), E_USER_WARNING);
			}
			$gameQ = new \GameQ\GameQ();
			$server_config = [
				'type'	=> $server_protocol,
				'host'	=> "$server_ip:$server_port",
			];
			if ($server_query_port != 0)
			{
				$server_config['options'] = ['query_port' => $server_query_port];
			}
			$gameQ->addServer($server_config);
			$results = $gameQ->process();
			$this->template->assign_vars(array(
				'SERVER_IP'			=> $server_ip,
				'SERVER_PORT'		=> $server_port,
				'SERVER_PROTOCOL'	=> $server_protocol,
				'SERVER_QUERY_PORT'	=> ($server_query_port != 0) ? $server_query_port : '',
			));
			foreach ($results["$server_ip:$server_port"] as $k => $v)
			{
				if (is_array($v))
				{
					$usePre = true;
					$v = var_export($v, true);
				}
				else
				{
					$usePre = false;
				}
				$this->template->assign_var('GAMEQ_STATUS', $results["$server_ip:$server_port"]['gq_online']);
				$this->template->assign_block_vars('serverdata', array(
					'KEY' 		=> $k,
					'VALUE'		=> htmlentities($v),
					'USE_PRE'	=> $usePre,
				));
			}
			$this->template->assign_var('TEST_COMPLETE', true);
		}
		else
		{
			$this->generate_protocol_list();
		}
	}
	private function generate_protocol_list()
	{
		$protocols = $this->get_supported_protocols();
		$curProto = '';
		$baseProtos = array();
		$sortExceptions = array('bf3', 'quake3', 'samp', 'ase', 'starmade', 'lhmp'); // Servers that are actually games
		foreach ($protocols AS $protocol)
		{
			if ( ($protocol['short'] != $protocol['protocol']) || ($protocol['short'] == $protocol['protocol'] && in_array($protocol['short'], $sortExceptions)) )
			{
				if ($protocol['protocol'] != $curProto)
				{
					$curProto = $protocol['protocol'];
					$this->template->assign_block_vars('serversboard_base_protocols', array(
						'CATEGORY'	=> $protocol['protocol'],
					));
				}
				$this->template->assign_block_vars('serversboard_base_protocols.protocols', array(
					'NAME'		=> $protocol['name'],
					'VALUE'		=> $protocol['short'],
				));
			}
			else
			{
				$baseProtos[] = $protocol;
			}
		}
		$this->template->assign_block_vars('serversboard_base_protocols', array(
			'CATEGORY'	=> $this->user->lang('TOKEN07_SERVERSBOARD_ACP_OTHER'),
		));
		foreach ($baseProtos AS $protocol)
		{
			$this->template->assign_block_vars('serversboard_base_protocols.protocols', array(
					'NAME'		=> $protocol['name'],
					'VALUE'		=> $protocol['protocol'],
			));
		}
	}
	private function generate_server_list()
	{
		$result = $this->db->sql_query('SELECT server_id, server_order, server_ip, server_hostname, server_lastupdate FROM ' . $this->serversboard_table . ' ORDER BY server_order ASC');
		while ($row = $this->db->sql_fetchrow($result))
		{
			$tmp = array(
				'NAME'			=> htmlentities($row['server_hostname']),
				'IP'			=> $row['server_ip'],
				'LASTUPDATE'	=> $this->user->format_date($row['server_lastupdate']),
				'U_DELETE'		=> "{$this->u_action}&amp;action=delete&amp;server_id={$row['server_id']}",
				'U_EDIT'		=> "{$this->u_action}&amp;action=edit&amp;server_id={$row['server_id']}",
				'U_MOVE_UP'		=> "{$this->u_action}&amp;action=move_up&amp;server_id={$row['server_id']}",
				'U_MOVE_DOWN'	=> "{$this->u_action}&amp;action=move_down&amp;server_id={$row['server_id']}",
			);
			$this->template->assign_block_vars('serverlist', $tmp);
		}
	}
	// Modified from includes/acp/acp_groups.php
	private function move($id, $delta)
	{
		$delta = (int) $delta;
		if (!$delta)
		{
			return false;
		}

		$move_up = ($delta > 0) ? true : false;
		// Get the current position.
		$sql = 'SELECT server_order FROM ' . $this->serversboard_table . ' WHERE server_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		if (!$data = $this->db->sql_fetchrow($result))
		{
			return false;
		}
		$current_value = (int) $data['server_order'];

		if (!$move_up)
		{
			$delta = abs($delta) + 1;
		}
		$sql = 'SELECT server_order
			FROM ' . $this->serversboard_table . '
			WHERE server_order' . (($move_up) ? ' < ' : ' > ') . $current_value . '
			ORDER BY server_order' . (($move_up) ? ' DESC' : ' ASC');
			$result = $this->db->sql_query_limit($sql, $delta);
	
		$sibling_count = 0;
		$sibling_limit = $delta;

		// Reset the delta, as we recalculate the new real delta
		$delta = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$sibling_count++;
			$delta = $current_value - $row['server_order'];

			if (!$move_up && $sibling_count == $sibling_limit)
			{
				// Remove the additional sibling we added previously
				$delta++;
			}
		}
		$this->db->sql_freeresult($result);

		if ($delta)
		{
			// First we move all items between our current value and the target value up/down 1,
			// so we have a gap for our item to move.
			$sql = 'UPDATE ' . $this->serversboard_table . '
				SET server_order = server_order' . (($move_up) ? ' + 1' : ' - 1') . '
				WHERE server_order' . (($move_up) ? ' >= ' : ' <= ') . ($current_value - $delta) . '
					AND server_order' . (($move_up) ? ' < ' : ' > ') . $current_value;
			$this->db->sql_query($sql);

			// And now finally, when we moved some other items and built a gap,
			// we can move the desired item to it.
			$sql = 'UPDATE ' . $this->serversboard_table . '
				SET server_order = server_order ' . (($move_up) ? ' - ' : ' + ') . abs($delta) . '
				WHERE server_id = ' . (int) $id;
			$this->db->sql_query($sql);

			$this->db->sql_transaction('commit');
			return true;
		}
	}
	private function get_supported_protocols()
	{
		// Modified from vendor/austinb/gameq/examples/list.php
		$protocols = array();
		$protocols_path = __DIR__ . "/../vendor/austinb/gameq/src/GameQ/Protocols/";

		// Grab the dir with all the classes available
		$dir = dir($protocols_path);

		$protocols = [];

		// Now lets loop the directories
		while (false !== ($entry = $dir->read()))
		{
			if (!is_file($protocols_path . $entry))
			{
				continue;
			}

			// Lets get some info on the class
			$reflection = new \ReflectionClass('\\GameQ\\Protocols\\' . pathinfo($entry, PATHINFO_FILENAME));

			// Check to make sure we can actually load the class
			if (!$reflection->IsInstantiable()) {
				continue;
			}
			$class = $reflection->newInstance();
			$protocols[ $class->name() ] = [
				'class' => '\\GameQ\\Protocols\\' . pathinfo($entry, PATHINFO_FILENAME),
				'name'  => $class->nameLong(),
				'short'	=> $class->name(), // Sorting causes key name to get lost
				'parent' => '\\' . get_parent_class($class),
				'protocol'	=> $class->getProtocol(),
			];
			unset($class);
		}
		// Sort each server type by the protocol
		usort($protocols, function($a, $b) { return $a['protocol'] > $b['protocol']; } );

		return $protocols;
	}
}
