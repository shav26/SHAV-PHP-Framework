<?php
include_once("../inc/shav_config.php");
global $shavDB;

if($_POST['name'] != '')
{
	if($_POST['add'])
	{
		$sql = 'INSERT INTO bans_status SET bans_status_name = "'.$_POST['name'].'"';
		$shavDB->insert_data($sql, 'bans_status_id');
	}
	elseif($_POST['save'] && (int)$_POST['id_status'] > 0)
	{
		$sql = 'UPDATE bans_status SET bans_status_name = "'.$_POST['name'].'" WHERE bans_status_id = '.(int)$_POST['id_status'];
		$shavDB->get_results($sql);
	}
}

if($_GET['action'] == 'del' && (int)$_GET['id_status'] > 0)
{
	$sql = 'DELETE FROM bans_status WHERE bans_status_id = '.(int)$_GET['id_status'];
	$shavDB->get_results($sql);

	$_GET['action'] = '';
}

header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/');
?>