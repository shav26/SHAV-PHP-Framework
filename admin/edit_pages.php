<?php
include_once("../inc/shav_config.php");
global $shavDB;

if($_POST['name'] != '')
{
	if($_POST['show'])
		$show = 1;
	elseif(empty($_POST['show']))
		$show = 0;

	if($_POST['add'])
	{
		$sql = 'INSERT INTO '.$_POST['table_page'].' SET prn_id = '.$_POST['page_prn'].', p_url = "'.$_POST['p_url'].'", p_title = "'.$_POST['name'].'", is_show = '.$show.', icons = "'.$_POST['icon'].'", pages_date_add = "'.date("Y-m-d H:s:i").'"';
		$shavDB->insert_data($sql, 'pages_id');
	}
	elseif($_POST['save'] && (int)$_POST['pages_id'] > 0)
	{
		$sql = 'UPDATE '.$_POST['table_page'].' SET prn_id = '.$_POST['page_prn'].', p_url = "'.$_POST['p_url'].'", p_title = "'.$_POST['name'].'", is_show = '.$show.', icons = "'.$_POST['icon'].'", pages_date_add = "'.date("Y-m-d H:s:i").'" WHERE pages_id = '.(int)$_POST['pages_id'];
		$shavDB->get_results($sql);
	}
}

if($_GET['action'] == 'del' && (int)$_GET['id_page'] > 0 && $_GET['table'] != '')
{
//	$icon = $db->get_vars('SELECT icons FROM '.$_GET['table'].' WHERE pages_id = '.(int)$_GET['id_page']);
	$sql = 'DELETE FROM '.$_GET['table'].' WHERE pages_id = '.(int)$_GET['id_page'];
	$shavDB->get_results($sql);
//	unlink($icon);

	$_GET['action'] = '';
}

header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/');
?>