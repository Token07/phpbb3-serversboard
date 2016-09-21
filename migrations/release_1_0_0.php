<?php

/**
*
* @package phpBB Extension - Servers Board
* @copyright (c) 2016 Token07
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace token07\serversboard\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array(
				'config.add', array('serversboard_enable', 0)
			),
			array(
				'config.add', array('serversboard_update_time', 300)
			),
			array(
				'config.add', array('serversboard_update_last_run', 0)
			),
			array(
				'config.add', array('serversboard_navbar_link_enable', 1)
			),
			array(
				'module.add', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD'
				)
			),
			array(
				'module.add', array(
					'acp',
					'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
					array(
						'module_basename'	=> '\token07\serversboard\acp\serversboard_module',
						'modes'	=> array('servers', 'settings', 'add', 'test'),
					),
				)
			),
		);
	}
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'serversboard' => array(
					'COLUMNS'	=> array(
						'server_id'			=> array('UINT', NULL, 'auto_increment'),
						'server_type'		=> array('UINT', 1),
						'server_order'		=> array('UINT', 1),
						'server_ip'			=> array('VCHAR:60', NULL),
						'server_status'		=> array('UINT', NULL),
						'server_hostname'	=> array('VCHAR:255', NULL),
						'server_map'		=> array('VCHAR:32', NULL),
						'server_players'	=> array('TEXT', '0 / 0'),
						'server_playerlist'	=> array('TEXT', '[]'),
						'server_lastupdate'	=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'server_id',
				)
			)
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'serversboard',
			)
		);
	}
}
