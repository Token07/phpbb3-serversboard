<?php

/*
 * @package phpBB Extension - Servers Board
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

// The order of the array was ignored (column dropped and not readded)
//  in my test so migration was split.
class add_join_link_column extends \phpbb\db\migration\migration
{
	static public function depends_on()
    {
        return array('\token07\serversboard\migrations\add_query_port_column');
	}
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_join_link'	=> array('VCHAR:255', NULL),
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_join_link',
				),
			),
		);
	}
}