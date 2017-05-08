<?php

/**
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */
if (!defined('IN_PHPBB')) {
    exit;
}

if (empty($lang) || !is_array($lang)) {
    $lang = [];
}

$lang = array_merge($lang, [
    'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD'            => 'Servers Board',
    'TOKEN07_SERVERSBOARD_ACP_ADD'                     => 'Add server',
    'TOKEN07_SERVERSBOARD_ACP_EDIT'                    => 'Edit server',
    'TOKEN07_SERVERSBOARD_ACP_DISPINDEX'               => 'Display servers board on index',
    'TOKEN07_SERVERSBOARD_ACP_UPDATEFREQ'              => 'Server data update frequency (seconds)',
    'TOKEN07_SERVERSBOARD_ACP_SERVERIP'                => 'Server IP',
    'TOKEN07_SERVERSBOARD_ACP_SERVERPORT'              => 'Server port',
    'TOKEN07_SERVERSBOARD_ACP_HOSTNAME'                => 'Hostname',
    'TOKEN07_SERVERSBOARD_ACP_HOSTNAME2'               => 'Host name',
    'TOKEN07_SERVERSBOARD_ACP_SERVERTYPE'              => 'Server type',
    'TOKEN07_SERVERSBOARD_ACP_INVALIDIP'               => 'The IP address entered is invalid.',
    'TOKEN07_SERVERSBOARD_ACP_INVALIDPORT'             => 'The port number entered is invalid.',
    'TOKEN07_SERVERSBOARD_ACP_CONFIRMDEL'              => 'Are you sure you want to delete this server?',
    'TOKEN07_SERVERSBOARD_ACP_DELETED'                 => 'The server was successfully deleted.',
    'TOKEN07_SERVERSBOARD_ACP_ADDED'                   => 'The server was successfully added.',
    'TOKEN07_SERVERSBOARD_ACP_UPDATED'                 => 'The server was successfully updated.',
    'TOKEN07_SERVERSBOARD_ACP_NAVBAR_LINK'             => 'Add link to servers board in navigation bar',
    'TOKEN07_SERVERSBOARD_ACP_NO_SERVER'               => 'The specified server does not exist.',
    'TOKEN07_SERVERSBOARD_ACP_QUERY_PORT'              => 'Query port',
    'TOKEN07_SERVERSBOARD_ACP_QUERY_PORT_EXPLAIN'      => 'Required for Teamspeak servers and may be for certain game servers. Leave blank if not required or to use default value.',
    'TOKEN07_SERVERSBOARD_ACP_OTHER'                   => 'Other',
    'TOKEN07_SERVERSBOARD_ACP_SHOW_GAMETRACKER'        => 'Show GameTracker link for this server',
    'TOKEN07_SERVERSBOARD_ACP_SHOW_TIME_ONLINE'        => 'Show the "Time Online" column in player list for this server:',
    'TOKEN07_SERVERSBOARD_ACP_ICON_PATH'               => 'Servers Board icon storage path:',
    'TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_CREATE'         => 'Could not create socket. (Error message: %s)',
    'TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_OPEN'           => 'Could not open socket. This probably indicates a firewall issue. (Error Message: %s)',
    'TOKEN07_SERVERSBOARD_ACP_SOCK_ERR_WRITE'          => 'Could not write to socket. This probably indicates a firewall issue. (Error Message: %s)',
    'TOKEN07_SERVERSBOARD_ACP_CONNECT_TEST_FAIL'       => 'Connection to server unsuccessful. Please check IP, port, protocol, and query port settings.',
    'TOKEN07_SERVERSBOARD_ACP_CONNECT_TEST_PASS'       => 'Connection to server successful.',
    ]
);
