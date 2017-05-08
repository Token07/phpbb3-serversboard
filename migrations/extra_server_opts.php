<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

class extra_server_opts extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\token07\serversboard\migrations\add_permissions'];
    }

    public function update_schema()
    {
        return [
            'add_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_show_gametracker'    => ['BOOL', 1],
                    'server_show_time_online'    => ['BOOL', 1],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns'    => [
                $this->table_prefix.'serversboard'    => [
                    'server_show_gametracker',
                    'server_show_timeonline',
                ],
            ],
        ];
    }
}
