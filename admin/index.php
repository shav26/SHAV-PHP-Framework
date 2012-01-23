<?php
include_once('../inc/shav_config.php');
global $shavDB;

//Подключаем нужные JavaScript'ы
$shavJS->shav_FishEye();
$shavJS->shav_tiniMCE();
$shavJS->shav_jqModal();
$shavJS->shav_loadSlider();
$shavJS->shav_jQuerySideBar(array('idcomponent'=>'.sidebar', 'show_text'=>'Show', 'hide_text'=>'Hide'));

//Создаем меню
$menu = new SHAV_Menu();
$menu->createMenuFromArray($admin_pages, 3);
$jQueryMenu = $menu->content;
//Выходим
if($_GET['action'] == 'logout')
{
	$shavDB->quit();
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}

//Проверяем авторизацию
if($_POST['login'] != '' && $_POST['pass'] != '' && $_POST['signin'])
{
	$shavDB->login($_POST['login'], $_POST['pass']);
}

include_once('def_admin_fnc.php');

if(!empty($_SESSION) && (int)$_SESSION['isadmin'] >= 0)
{
	$content = '<div align="center"><h2><i>Version:&nbsp;'.SHAV_VERSION.'</i></h2></div>';
	$page_title = 'Добро пожаловать';
	//Показываем пользователей
	if($_GET['action'] == 'show_users' || ($_GET['action'] == 'edit' && (int)$_GET['id_user'] > 0))
	{
		$content = createAllUsersWindow();
		$page_title = 'Зарегистрированные пользователи';
	}
	//Показываем статусы пользователей
	elseif($_GET['action'] == 'show_status' || ($_GET['action'] == 'edit' && (int)$_GET['id_status'] > 0))
	{
		$results = $shavDB->get_results('SELECT * FROM bans_status');
		$content = '<form method="POST" action="/admin/edit_status.php" ><table width="100%"><tr><td width="10%">id</td><td width="60%">Название</td><td width="30%"></td></tr>';
		foreach($results as $rec)
		{
			if((int)$_GET['id_status'] == $rec['bans_status_id'])
			{
				$content .= '<tr><td width="10%">'.$rec['bans_status_id'].'<input type="hidden" name="id_status" id="id_status" value="'.$rec['bans_status_id'].'" /></td>';
				$content .= '<td width="60%"><input type="text" name="name" id="name" value="'.$rec['bans_status_name'].'" /></td>';
				$content .= '<td width="30%"><input type="submit" name="save" id="save" value="Save" /></td></tr>';
			}
			else
			{
				$content .= '<tr><td width="10%">'.$rec['bans_status_id'].'</td><td width="60%">'.$rec['bans_status_name'].'</td><td width="30%"><a href="/admin/index.php?action=edit&id_status='.$rec['bans_status_id'].'"><img src="images/icon/add.png" /></a><a href="/admin/edit_status.php?action=del&id_status='.$rec['bans_status_id'].'"><img src="images/icon/delete.png" /></a></td></tr>';
			}
		}
		$content .= '</table></form>';
		$page_title = 'Редактирование статусов пользователей';
	}
	//Показываем типы пользователей
	elseif($_GET['action'] == 'show_type' || ($_GET['action'] == 'edit' && (int)$_GET['id_type'] > 0))
	{
		$results = $shavDB->get_results('SELECT * FROM users_type');
		$content = '<form method="POST" action="/admin/edit_type.php" ><table width="100%"><tr><td width="10%">id</td><td width="60%">Название</td><td width="30%"></td></tr>';
		foreach($results as $rec)
		{
			if((int)$_GET['id_type'] == $rec['users_type_id'])
			{
				$content .= '<tr><td width="10%">'.$rec['users_type_id'].'<input type="hidden" name="id_type" id="id_type" value="'.$rec['users_type_id'].'" /></td>';
				$content .= '<td width="60%"><input type="text" name="name" id="name" value="'.$rec['users_status_name'].'" /></td>';
				$content .= '<td width="30%"><input type="submit" name="save" id="save" value="Save" /></td></tr>';
			}
			else
			{
				$content .= '<tr><td width="10%">'.$rec['users_type_id'].'</td><td width="60%">'.$rec['users_status_name'].'</td><td width="30%"><a href="/admin/index.php?action=edit&id_type='.$rec['users_type_id'].'"><img src="images/icon/add.png" /></a><a href="/admin/edit_type.php?action=del&id_type='.$rec['users_type_id'].'"><img src="images/icon/delete.png" /></a></td></tr>';
			}
		}
		$content .= '</table></form>';
		$page_title = 'Редактирование статусов пользователей';
	}
	//Показываем странички для админки и сайта.
	elseif(($_GET['action'] == 'show_pages' && $_GET['table'] != '') || ($_GET['action'] == 'edit' && (int)$_GET['id_page'] > 0 && $_GET['table'] != ''))
	{
		$content = createAllPages($_GET['table']);
		$page_title = 'Странички';
	}

	$tags = array('#TITLE#'=>$title.': '.$page_title, '#DESCRIPTION#'=>'Тестовый сайт с использованием SHAV PHP Freamwork', '#KEYWORDS#'=>'SHAV PHP Freamwork', '#JAVA_SCRIPTS#'=>$shavJS->drawJS(), '#HEADER#'=>shav_createContentsByTags(array('#PAGE_TITLE#'=>$page_title), $header), '#FOOTER#'=>$footer, '#CONTENT#'=>$content, '#LEFT_PANEL#'=>$left, '#RIGHT_PANEL#'=>$right, '#TITLE_PAGE#'=>$title);

	//Выводим страницу
	$page = new SHAV_Page();
	$page->createPageFromFileWithTags('../tmpls/admin/admin_index_panel.html', $tags);
	$page->drawPage();
}
?>