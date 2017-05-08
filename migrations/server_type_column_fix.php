<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

class server_type_column_fix extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\token07\serversboard\migrations\release_1_0_0'];
    }

    public function update_schema()
    {
        return [
            'drop_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_type',
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'add_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_type'    => ['UINT', 1],
                ],
            ],
        ];
    }
}
