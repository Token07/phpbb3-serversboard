<?php

/*
 * @package phpBB Extension - Servers Board
 * @copyright (c) 2016 Token07
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */


if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'TOKEN07_SERVERSBOARD_SERVERSBOARD'	=> 'Servers Board',
	'TOKEN07_SERVERSBOARD_STATUS'		=> 'Status',
	'TOKEN07_SERVERSBOARD_NAME'			=> 'Server name',
	'TOKEN07_SERVERSBOARD_SERVER'		=> 'Servers',
	'TOKEN07_SERVERSBOARD_PLAYERS'		=> 'Players',
	'TOKEN07_SERVERSBOARD_PLAYERLIST'	=> 'Player list for %s:',
	'TOKEN07_SERVERSBOARD_MAP'			=> 'Map',
	
	'TOKEN07_SERVERSBOARD_VIEWDETAILS'	=> 'View server details',
	'TOKEN07_SERVERSBOARD_TIMELONLINE'	=> 'Time online',
	'TOKEN07_SERVERSBOARD_LASTUPDATED'	=> 'Last updated: %s',
	'TOKEN07_SERVERSBOARD_CONNECT'		=> 'Connect',
	'TOKEN07_SERVERSBOARD_INVALID'		=> 'The server you selected does not exist.',
	'TOKEN07_SERVERSBOARD_PLAYER_COUNT'	=> array(
		1 => '%d player online!',
		2 => '%d players online!',
	),
));
