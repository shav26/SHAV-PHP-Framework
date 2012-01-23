<?php
include_once("../inc/shav_config.php");
global $shavDB;

if($_POST['fio'] != '' && $_POST['login'] != '' && $_POST['email'] != '')
{
	if($_POST['add'] && $_POST['password'] != '')	//Добавляем пользователя
	{
		$sql = 'INSERT INTO users SET fio = "'.$_POST['fio'].'", login = "'.$_POST['login'].'", pass = "'.md5($_POST['password']).'", email = "'.$_POST['email'].'", ban_id = '.$_POST['status'].', isadmin = '.$_POST['type'];

		$shavDB->insert_data($sql, 'users_id');
	}
	elseif($_POST['save'] && (int)$_POST['id_user'] > 0)	//Редактируем пользователя
	{
		if($_POST['password'] == '')
		{
			$sql = 'UPDATE users SET fio = "'.$_POST['fio'].'", login = "'.$_POST['login'].'", email = "'.$_POST['email'].'", ban_id = '.$_POST['status'].', isadmin = '.$_POST['type'].' WHERE users_id = '.(int)$_POST['id_user'];
		}
		else
		{
			$sql = 'UPDATE users SET fio = "'.$_POST['fio'].'", login = "'.$_POST['login'].'", pass = "'.md5($_POST['password']).'", email = "'.$_POST['email'].'", ban_id = '.$_POST['status'].', isadmin = '.$_POST['type'].' WHERE users_id = '.(int)$_POST['id_user'];
		}

	$shavDB->get_results($sql);
	}
}

if($_GET['action'] == 'del' && (int)$_GET['id_user'] > 0)	//Удаляем пользователя
{
	$sql = 'DELETE FROM users WHERE users_id = '.(int)$_GET['id_user'];
	$shavDB->get_results($sql);
}

header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/');
?>