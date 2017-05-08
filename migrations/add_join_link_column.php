<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

// The order of the array was ignored (column dropped and not readded)
//  in my test so migration was split.
class add_join_link_column extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\token07\serversboard\migrations\add_query_port_column'];
    }

    public function update_schema()
    {
        return [
            'add_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_join_link'    => ['VCHAR:255', null],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_join_link',
                ],
            ],
        ];
    }
}
