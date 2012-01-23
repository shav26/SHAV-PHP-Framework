<?php
include_once("../inc/shav_config.php");
global $shavDB;

if($_POST['name'] != '')
{
	if($_POST['add'])
	{
		$sql = 'INSERT INTO users_type SET users_status_name = "'.$_POST['name'].'"';
		$shavDB->insert_data($sql, 'users_type_id');
	}
	elseif($_POST['save'] && (int)$_POST['id_status'] > 0)
	{
		$sql = 'UPDATE users_type SET users_status_name = "'.$_POST['name'].'" WHERE users_type_id = '.(int)$_POST['id_type'];
		$shavDB->get_results($sql);
	}
}

if($_GET['action'] == 'del' && (int)$_GET['id_type'] > 0)
{
	$sql = 'DELETE FROM users_type WHERE users_type_id = '.(int)$_GET['id_type'];
	$shavDB->get_results($sql);

	$_GET['action'] = '';
}

header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/');
?>