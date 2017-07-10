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
	'TOKEN07_SERVERSBOARD_ACP_SERVERSBOARD'	=> 'Servers Board',
	'TOKEN07_SERVERSBOARD_ACP_MANAGE'		=> 'Manage servers',
	'TOKEN07_SERVERSBOARD_ACP_ADD'			=> 'Add server',
	'TOKEN07_SERVERSBOARD_ACP_TEST'			=> 'Server connection test',
	'TOKEN07_SERVERSBOARD_ACP_LOG_ADDED'	=> '<strong>Added server to Servers Board</strong><br />» %s',
	'TOKEN07_SERVERSBOARD_ACP_LOG_UPDATED'	=> '<strong>Updated server on Servers Board</strong><br />» %s',
	'TOKEN07_SERVERSBOARD_ACP_LOG_DELETE'	=> '<strong>Deleted server from Servers Board</strong><br />» %s',
	'TOKEN07_SERVERSBOARD_ACP_LOG_UPDATE'	=> '<strong>Updated Servers Board configuration</strong>',
));
