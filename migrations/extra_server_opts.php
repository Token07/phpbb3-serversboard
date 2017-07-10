<?php

/*
 * @package phpBB Extension - Servers Board
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

class extra_server_opts extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\token07\serversboard\migrations\add_permissions');
	}
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_show_gametracker'	=> array('BOOL', 1),
					'server_show_time_online'	=> array('BOOL', 1),
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_show_gametracker',
					'server_show_timeonline',
				),
			),
		);
	}
}