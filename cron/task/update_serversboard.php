<?php

/**
*
* @package phpBB Extension - Servers Board
* @copyright (c) 2016 Token07
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace token07\serversboard\cron\task;

class update_serversboard extends \phpbb\cron\task\base
{
	protected $config;
	protected $db;
	protected $serversboard_table;

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\factory $db, $serversboard_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->serversboard_table = $serversboard_table;
	}
	public function is_runnable()
	{
		return true;
	}
	public function should_run()
	{
		return $this->config['serversboard_update_last_run'] < time() - $this->config['serversboard_update_time'];
	}
	public function run()
	{
		$GameQ = new \GameQ\GameQ();
		$servers = array();
		$playerCount = 0;
		$result = $this->db->sql_query('SELECT server_type, server_ip, server_id, server_query_port FROM ' . $this->serversboard_table);
		
		while ($row = $this->db->sql_fetchrow($result))
		{
			$server = array(
				'type'	=> $row['server_type'],
				'host'	=> $row['server_ip'],
				'id'	=> $row['server_id'],
			);
			if ($server['type'] == 'teamspeak3' && empty($row['server_query_port']))
			{
				$row['server_query_port'] = 10011;
			}
			elseif ($server['type'] == 'teamspeak2' && empty($row['server_query_port']))
			{
				$row['server_query_port'] = 51234;
			}
			if ($row['server_query_port'] != NULL)
			{
				$server['options'] = array('query_port' => $row['server_query_port']);
			}
			$servers[] = $server;
		}
		
		$this->db->sql_freeresult($result);
		$GameQ->addServers($servers);
		$GameQ->setOption('timeout', 5);
		$results = $GameQ->process();

		foreach ($results as $server => $result)
		{
			$offline = (!empty($result['gq_online'])) ? $result['gq_online'] : 0;
			$newDetails = array(
				'server_status'		=> $offline,
				'server_players'	=> sprintf('%d / %d', ((!empty($result['gq_numplayers'])) ? $result['gq_numplayers'] : 0), ((!empty($result['gq_maxplayers'])) ? $result['gq_maxplayers'] : 0)),
				'server_map'		=> (isset($result['gq_mapname'])) ? $result['gq_mapname'] : '',
				'server_lastupdate'	=> time(),
				'server_join_link'	=> $result['gq_joinlink'],
			);
			if (!empty($result['gq_hostname']))
			{
				$newDetails['server_hostname'] = $result['gq_hostname'];
			}

			$players = array();

			foreach ($result['players'] AS $player)
			{
				if (empty($player['time']))
				{
					$player['time'] = 0;
				}
				// Servers doesn't always give back valid UTF-8
				if (!preg_match('//u', $player['gq_name']))
				{
					$player['gq_name'] = utf8_encode($player['gq_name']);
				}
				$players[] = array(
					'Name'	=> $player['gq_name'],
					'TimeF'	=> gmdate(($player['time'] > 3600 ? "H:i:s" : "i:s" ), $player['time']),
				);
			}

			$newDetails['server_playerlist'] = json_encode($players);
			$sql = 'UPDATE ' . $this->serversboard_table . ' SET ' . $this->db->sql_build_array('UPDATE', $newDetails) . '
				WHERE server_id = ' . (int) $server;
			$this->db->sql_query($sql);
			$playerCount += (!empty($result['gq_numplayers']) && !$result['gq_numplayers']) ? $result['gq_numplayers'] : 0;
		}
		$this->config->set('serversboard_update_last_run', time());
		$this->config->set('serversboard_player_count', $playerCount);
	}
}
