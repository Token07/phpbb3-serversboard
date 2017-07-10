<?php

/*
 * @package phpBB Extension - Servers Board
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	protected $helper;
	protected $template;
	protected $config;
	protected $db;
	protected $serversboard_table;
	protected $collapse_cat;
	protected $serversboard_cc_name = 'serversboard';

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'				=> 'load_language_on_setup',
			'core.user_setup_after'			=> 'navbar_setup',
			'core.index_modify_page_title'	=> 'load_serversboard',
			'core.permissions'				=> 'add_permission',
		);
	}

	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\config\config $config, \phpbb\db\driver\factory $db, \phpbb\user $user, $serversboard_table, \phpbb\collapsiblecategories\operator\operator $collapse_cat = null)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->serversboard_table = $serversboard_table;
		$this->collapse_cat = $collapse_cat;
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'token07/serversboard',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function navbar_setup()
	{
		$this->template->assign_vars(array(
			'TOKEN07_SERVERSBOARD_NAVBAR_LINK_ENABLE'	=> $this->config['serversboard_navbar_link_enable'],
			'TOKEN07_SERVERSBOARD_NAVBAR_TEXT' 			=> $this->user->lang('TOKEN07_SERVERSBOARD_PLAYER_COUNT', (int) $this->config['serversboard_player_count']),
			'TOKEN07_SERVERSBOARD_URL'					=> $this->helper->route("token07_serversboard_controller")
		));

		if ($this->collapse_cat !== null)
		{
			$this->template->assign_vars(array(
				'SERVERSBOARD_HIDDEN'		=> in_array($this->serversboard_cc_name, $this->collapse_cat->get_user_categories()),
				'SERVERSBOARD_COLLAPSE_URL'	=> $this->helper->route('phpbb_collapsiblecategories_main_controller', array(
					'forum_id' => $this->serversboard_cc_name,
					'hash' => generate_link_hash("collapsible_{$this->serversboard_cc_name}")))
				));
		}
	}

	public function load_serversboard($page_title)
	{
		if ($this->config['serversboard_enable'])
		{
			$this->template->assign_var('TOKEN07_SERVERSBOARD_ENABLE', true);
			$result = $this->db->sql_query('SELECT server_id, server_status, server_hostname, server_ip, server_players, server_map, server_join_link, server_show_gametracker FROM ' . $this->serversboard_table . ' ORDER BY server_order');
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->setTemplateVars($row);
			}
		}
	}

	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['a_serversboard'] = array('lang' => 'ACL_A_SERVERSBOARD', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	private function setTemplateVars($row)
	{
		$tmp = array(
			'STATUS'		=> $row['server_status'],
			'HOSTNAME'		=> $row['server_hostname'],
			'IP'			=> $row['server_ip'],
			'PLAYERS'		=> $row['server_players'],
			'MAP'			=> $row['server_map'],
			'JOINLINK'		=> $row['server_join_link'],
			'GAMETRACKER'	=> $row['server_show_gametracker'],
		);
		$proto = substr($row['server_join_link'], 0, strpos($row['server_join_link'], ':'));
		switch ($proto)
		{
			case 'steam':
				$tmp['ICON'] = 'steam';
			break;
			case 'teamspeak':
			case 'ts3server':
				$tmp['ICON'] = 'teamspeak';
			break;
			case 'minecraft':
				$tmp['ICON'] = 'minecraft';
			break;
		}
		if (!$row['server_status'])
		{
			$row['server_players'] = '0 / 0';
			$row['server_playerlist'] = '[]';
		}
		$tmp['LINK'] = $this->helper->route("token07_serversboard_viewdetails", array('id' => $row['server_id']));
		$this->template->assign_block_vars('serverlist', $tmp);
	}
}
