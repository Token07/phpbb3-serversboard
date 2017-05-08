<?php

/*
 * @package phpBB Extension - Servers Board
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */


namespace token07\serversboard\migrations;

class server_type_column_fix extends \phpbb\db\migration\migration
{
	static public function depends_on()
    {
        return array('\token07\serversboard\migrations\release_1_0_0');
	}
	public function update_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_type',
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'serversboard'	=> array(
					'server_type'	=> array('UINT', 1),
				),
			),
		);
	}
}