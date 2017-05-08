<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace token07\serversboard\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
    public function update_data()
    {
        return [
            [
                'config.add', ['serversboard_enable', 0],
            ],
            [
                'config.add', ['serversboard_update_time', 300],
            ],
            [
                'config.add', ['serversboard_update_last_run', 0],
            ],
            [
                'config.add', ['serversboard_navbar_link_enable', 1],
            ],
            [
                'module.add', [
                    'acp',
                    'ACP_CAT_DOT_MODS',
                    'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
                ],
            ],
            [
                'module.add', [
                    'acp',
                    'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD',
                    [
                        'module_basename'    => '\token07\serversboard\acp\serversboard_module',
                        'modes'              => ['servers', 'settings', 'add', 'test'],
                    ],
                ],
            ],
        ];
    }

    public function update_schema()
    {
        return [
            'add_tables'    => [
                $this->table_prefix.'serversboard' => [
                    'COLUMNS'    => [
                        'server_id'            => ['UINT', null, 'auto_increment'],
                        'server_type'          => ['UINT', 1],
                        'server_order'         => ['UINT', 1],
                        'server_ip'            => ['VCHAR:60', null],
                        'server_status'        => ['UINT', null],
                        'server_hostname'      => ['VCHAR:255', null],
                        'server_map'           => ['VCHAR:32', null],
                        'server_players'       => ['TEXT', '0 / 0'],
                        'server_playerlist'    => ['TEXT', '[]'],
                        'server_lastupdate'    => ['TIMESTAMP', 0],
                    ],
                    'PRIMARY_KEY'    => 'server_id',
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables' => [
                $this->table_prefix.'serversboard',
            ],
        ];
    }
}
