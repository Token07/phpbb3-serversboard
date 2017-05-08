<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

// The order of the array was ignored (column dropped and not readded)
//  in my test so migration was split.
class server_type_column_fix_pt2 extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\token07\serversboard\migrations\server_type_column_fix'];
    }

    public function update_schema()
    {
        return [
            'add_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_type'    => ['VCHAR:30', 'source'],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_type',
                ],
            ],
        ];
    }
}
