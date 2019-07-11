<?php
require('../config.php');
require('../libs/Functions.php');
require("../libs/MysqliDb.php");

function db_instance() {
	global $dbConfig;
	$db = new Mysqlidb(
		$dbConfig['host'],
		$dbConfig['username'],
		$dbConfig['password'],
		'mangos',
		$dbConfig['port']
	);
	if(!$db) respose('数据库打开失败！');
	return $db;
}

function mysql_replace($str) {
	$str = str_replace(PHP_EOL, "\\r\\n", $str);
	$str = preg_replace('/[\r\n]*/', '', $str);
	$str = str_replace('"', '\\"', $str);
	return $str;
}

$db = db_instance();

function write_sql_file($table, $data, $out_field, $where_field) {
	
	$filename = $table . '.sql';
	
	if(file_exists($filename)) {
		unlink($filename);
	}
	
	$file = @fopen($filename, "a") or die('没有写入权限！');
	
	$head = "-- VMaNGOS Database Chinese script\r\n" .
			"-- Author: Mr.huang (QQ: 654706160)\r\n" .
			"-- Create time: " . date('Y-m-d H:i:s') . "\r\n" .
			"-- File name: {$filename}\r\n" .
			"-- Table name: {$table}\r\n\r\n" .
			"USE `mangos`;\r\n" .
			"SET NAMES 'utf-8';\r\n\r\n";
	fwrite($file, $head);
	
	foreach($data as $val) {
		
		if(is_array($out_field)) {
			$setContents = '';
			foreach($out_field as $v) {
				$content = mysql_replace($val[$v]);
				$content = trim($content);
				if(strlen($content) <= 0) {
					$content = "NULL";
				} else {
					$content = "\"{$content}\"";
				}
				if(strlen($setContents) > 0) $setContents .= ',';
				$setContents .= "`{$v}`={$content}";
			}
		} else {
			$content = mysql_replace($val[$out_field]);
			$content = trim($content);
			if(strlen($content) <= 0) {
				$setContents = "`{$out_field}`=NULL";
			}
			$setContents = "`{$out_field}`=\"{$content}\"";
		}
		
		if(is_array($where_field)) {
			$condition = '';
			foreach($where_field as $v) {
				if(strlen($condition) > 0) $condition .= ' AND ';
				$field = $val[$v];
				$condition .= "`{$v}`={$field}";
			}
		} else {
			$field = $val[$where_field];
			$condition = "`{$where_field}`={$field}";
		}
		
		$sql = "UPDATE `{$table}` SET {$setContents} WHERE {$condition};\r\n";
		fwrite($file, $sql);
	}
	
	fclose($file);
	
	echo $filename . ' 写入成功！<br/>';
}

// 导出 mangos_string.sql
$table = 'mangos_string';
$data = $db->where('content_loc4 is not null')->get($table, null, 'entry,content_loc4');
write_sql_file($table, $data, 'content_loc4', 'entry');

// 导出 locales_area.sql
$table = 'locales_area';
$data = $db->where('NameLoc4 is not null')->get($table, null, 'Entry,NameLoc4');
write_sql_file($table, $data, 'NameLoc4', 'Entry');

// 导出 locales_broadcast_text.sql
$table = 'locales_broadcast_text';
$data = $db->get($table, null, 'ID,MaleText_loc4,FemaleText_loc4');
write_sql_file($table, $data, array('MaleText_loc4', 'FemaleText_loc4'), 'ID');

// 导出 locales_creature.sql
$table = 'locales_creature';
$data = $db->get($table, null, 'entry,name_loc4,subname_loc4');
write_sql_file($table, $data, array('name_loc4', 'subname_loc4'), 'entry');

// 导出 locales_gameobject.sql
$table = 'locales_gameobject';
$data = $db->get($table, null, 'entry,name_loc4');
write_sql_file($table, $data, 'name_loc4', 'entry');

// 导出 locales_gossip_menu_option.sql
$table = 'locales_gossip_menu_option';
$data = $db->get($table, null, 'menu_id,id,option_text_loc4,box_text_loc4');
write_sql_file($table, $data, array('option_text_loc4', 'box_text_loc4'), array('menu_id', 'id'));

// 导出 locales_item.sql
$table = 'locales_item';
$data = $db->get($table, null, 'entry,name_loc4,description_loc4');
write_sql_file($table, $data, array('name_loc4', 'description_loc4'), 'entry');

// 导出 locales_page_text.sql
$table = 'locales_page_text';
$data = $db->get($table, null, 'entry,Text_loc4');
write_sql_file($table, $data, 'Text_loc4', 'entry');

// 导出 locales_points_of_interest.sql
$table = 'locales_points_of_interest';
$data = $db->get($table, null, 'entry,icon_name_loc4');
write_sql_file($table, $data, 'icon_name_loc4', 'entry');

// 导出 locales_quest.sql
$table = 'locales_quest';
$data = $db->get($table, null, 'entry,Title_loc4,Details_loc4,Objectives_loc4,OfferRewardText_loc4,EndText_loc4');
write_sql_file($table, $data, array('Title_loc4','Details_loc4','Objectives_loc4','OfferRewardText_loc4','EndText_loc4'), 'entry');

// 导出 npc_trainer_greeting.sql
$table = 'npc_trainer_greeting';
$data = $db->where('content_loc4 is not null')->get($table, null, 'entry,content_loc4');
write_sql_file($table, $data, 'content_loc4', 'entry');

// 导出 quest_greeting.sql
$table = 'quest_greeting';
$data = $db->where('content_loc4 is not null')->get($table, null, 'entry,type,content_loc4');
write_sql_file($table, $data, 'content_loc4', array('entry', 'type'));

// 导出 script_texts.sql
$table = 'script_texts';
$data = $db->where('content_loc4 is not null')->get($table, null, 'entry,content_loc4');
write_sql_file($table, $data, 'content_loc4', 'entry');

// 导出 taxi_nodes.sql
$table = 'taxi_nodes';
$data = $db->where('name5 is not null')->get($table, null, 'id,build,name5');
write_sql_file($table, $data, 'name5', array('id', 'build'));