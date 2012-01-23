<?php
/** Функции для работы с админ-панелью сайта
@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
@date Созданно: 04.12.2009
@date Обновленно: 05.02.2011*/

/** Создает окно для добавления пользователей.
	@return HTML-код окна для создания пользователя.*/
function createAddUserWindow()
{
	$typeList = new SHAV_DropList();
	$typeList->createListFromDBTable(array('name' => 'users_type', 'id_field' => 'users_type_id', 'name_field' => 'users_status_name'), 'type');
	$type = $typeList->drawList();

	$bans_status = new SHAV_DropList();
	$bans_status->createListFromDBTable(array('name' => 'bans_status', 'id_field' => 'bans_status_id', 'name_field' => 'bans_status_name'), 'status');
	$status = $bans_status->drawList();

	$recs = array(	array('name' => 'fio', 'label_align' => 'left', 'label' => 'Full name:', 'type' => 'text', 'size' => '25', 'value' => ''),
			array('name' => 'login', 'label_align' => 'left', 'label' => 'Login:', 'type' => 'text', 'size' => '25', 'value' => ''),
			array('name' => 'password', 'label_align' => 'left', 'label' => 'Password:', 'type' => 'password', 'size' => '25', 'value' => ''),
			array('name' => 'email', 'label_align' => 'left', 'label' => 'Email:', 'type' => 'text', 'size' => '25', 'value' => ''),
			array('name' => 'type', 'label_align' => 'left', 'label' => 'Type:', 'type' => 'list', 'value' => $type),
			array('name' => 'status', 'label_align' => 'left', 'label' => 'Status:', 'type' => 'list', 'value' => $status),
			array('name' => 'add', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Добавить'));

	$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_users.php', 'enctype' => '', 'style_class' => 'addUser', 'title' => '', 'content_frm' => $recs);
	$form = new SHAV_Form();
	$form->createFromArray($params);

	$content  = '<table width="100%">';
	$content .= '<tr>';
	$content .= '<td><img src="images/icon/users.png" /></td>';
	$content .= '<td>'.$form->drawForm().'</td>';
	$content .= '</tr>';
	$content .= '</table>';

	$user_window = shav_createModalWindow('admin_user_add', 'Добавить профиль', 'Добавление нового пользователя', $content);

	return $user_window;
}

/** Создает окно для редактирования пользователей.
	@param $user_id Идентификатор пользователя.
	@return HTML-код окна для редактирования пользователя.*/
function createEditUserWindow($user_id)
{
	global $shavDB;

	$sql = 'SELECT * FROM users WHERE users_id = '.$user_id;
	$results = $shavDB->get_results($sql);
//	echo $sql;

	$typeList = new SHAV_DropList();
	$typeList->createListFromDBTable(array('name' => 'users_type', 'id_field' => 'users_type_id', 'name_field' => 'users_status_name'), 'type', (int)$results[0]['isadmin']);
	$type = $typeList->drawList();

	$bans_status = new SHAV_DropList();
	$bans_status->createListFromDBTable(array('name' => 'bans_status', 'id_field' => 'bans_status_id', 'name_field' => 'bans_status_name'), 'status', (int)$results[0]['ban_id']);
	$status = $bans_status->drawList();

	$recs = array(	array('name' => 'fio', 'label_align' => 'left', 'label' => 'Full name:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['fio']),
			array('name' => 'login', 'label_align' => 'left', 'label' => 'Login:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['login']),
			array('name' => 'password', 'label_align' => 'left', 'label' => 'Password:', 'type' => 'password', 'size' => '25', 'value' => ''),
			array('name' => 'email', 'label_align' => 'left', 'label' => 'Email:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['email']),
			array('name' => 'type', 'label_align' => 'left', 'label' => 'Type:', 'type' => 'list', 'value' => $type),
			array('name' => 'status', 'label_align' => 'left', 'label' => 'Status:', 'type' => 'list', 'value' => $status),
			array('name' => 'id_user', 'label_align' => 'left', 'label' => '', 'type' => 'hidden', 'value' => $results[0]['users_id']),
			array('name' => 'save', 'label_align' => 'left', 'label' => '', 'type' => 'submit', 'value' => 'Сохранить'));

	$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_users.php', 'enctype' => '', 'style_class' => 'editUser', 'title' => '', 'content_frm' => $recs);
	$form = new SHAV_Form();
	$form->createFromArray($params);

	$content  = '<table width="100%">';
	$content .= '<tr>';
	$content .= '<td><img src="images/icon/users.png" /></td>';
	$content .= '<td>'.$form->drawForm().'</td>';
	$content .= '</tr>';
	$content .= '</table>';

	//$user_window = shav_createModalWindow('admin_user_edit', '<img src="images/icon/add.png" />', 'Добавление нового пользователя', $content);

	return $content;//$user_window;
}

/** Создаем окно добавления нового типа пользователя.
	@return HTML-код окна для создания нового типа пользователя.*/
function createUserTypeAddWondow()
{
	$recs = array(	array('name' => 'name', 'label_align' => 'left', 'label' => 'Название:', 'type' => 'text', 'size' => '25', 'value' => ''),
			array('name' => 'add', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Добавить'));

	$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_type.php', 'enctype' => '', 'style_class' => 'statusFrm', 'title' => '', 'content_frm' => $recs);
	$form = new SHAV_Form();
	$form->createFromArray($params);

	$sts_window = shav_createModalWindow('admin_type_add', 'Добавить тип', 'Добавление нового типа пользователя', $form->drawForm());

	return $sts_window;
}

/** Создает окно со всеми пользователями.
	@return Таблица всех пользователей.*/
function createAllUsersWindow()
{
	global $shavDB;

	$sql = 'SELECT * FROM users u, bans_status bs WHERE u.ban_id = bs.bans_status_id';
	$results = $shavDB->get_results($sql);
	$content  = '<table width="100%" border="1">';
	$content .= '<tr align="center">';
	$content .= '<td width="40%">Full name</td>';
	$content .= '<td width="25%">Email</td>';
	$content .= '<td width="10%">Status</td>';
	$content .= '<td width="15%"></td>';
	$content .= '</tr>';
	foreach($results as $rec)
	{
		$content .= '<tr>';
		$content .= '<td>'.$rec['fio'].'</td>';
		$content .= '<td>'.$rec['email'].'</td>';

		$content .= '<td>'.$rec['bans_status_name'].'</td>';
		$content .= '<td><a href="/admin/index.php?action=edit&id_user='.$rec['users_id'].'"><img src="images/icon/add.png" /></a><a href="/admin/edit_users.php?action=del&id_user='.$rec['users_id'].'"><img src="images/icon/delete.png" /></a></td>';
		$content .= '</tr>';
	}
	$content .= '</table>';

	if($_GET['action'] == 'edit' && (int)$_GET['id_user'] > 0)
	{
		$content = createEditUserWindow($_GET['id_user']);
	}

	return $content;
}

/** Создает окно для добавления статуса.
	@return HTML-код окна для добавления статуса.*/
function createStatusAddWindow()
{
	$recs = array(	array('name' => 'name', 'label_align' => 'left', 'label' => 'Название:', 'type' => 'text', 'size' => '25', 'value' => ''),
			array('name' => 'add', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Добавить'));

	$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_status.php', 'enctype' => '', 'style_class' => 'statusFrm', 'title' => '', 'content_frm' => $recs);
	$form = new SHAV_Form();
	$form->createFromArray($params);

	$sts_window = shav_createModalWindow('admin_status_add', 'Добавить статус', 'Добавление нового статуса', $form->drawForm());

	return $sts_window;
}

/** Создает модальное окно для добавление новой странички.
	@param $table_name - название таблицы для редактирования;
	@param $id - идентификатор страницы.
	@return HTML-код окна для создания новой странички.*/
function createAddPageWindow($table_name = 'admin_pages', $id = 0)
{
	global $shavDB;

	if((int)$id > 0)
	{
		$results = $shavDB->get_results('SELECT * FROM '.$table_name.' WHERE pages_id = '.$id);
		$btn = array('name' => 'save', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Сохранить');
	}else
		$btn = array('name' => 'add', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Добавить');

	if($results[0]['is_show'] == 0)
		$results[0]['is_show'] = 1;
	else
		$results[0]['is_show'] = 0;

	$prn_page = new SHAV_DropList();
	$prn_page->createListFromDBTable(array('name' => $table_name, 'id_field' => 'pages_id', 'name_field' => 'p_title'), 'page_prn', $results[0]['prn_id']);
	$page = $prn_page->drawList();

	$recs = array(	array('name' => 'name', 'label_align' => 'left', 'label' => 'Заголовок:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['p_title']),
			array('name' => 'page_prn', 'label_align' => 'left', 'label' => 'Родитель:', 'type' => 'list', 'size' => '25', 'value' => $page),
			array('name' => 'p_url', 'label_align' => 'left', 'label' => 'Ссылка:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['p_url']),
			array('name' => 'show', 'label_align' => 'left', 'label' => 'Отображать в меню:', 'type' => 'checkbox', 'size' => '25', 'value' => $results[0]['is_show']),
			array('name' => 'icon', 'label_align' => 'left', 'label' => 'Иконка странички:', 'type' => 'text', 'size' => '25', 'value' => $results[0]['icons']),
			array('name' => 'table_page', 'label_align' => 'left', 'label' => '', 'type' => 'hidden', 'size' => '25', 'value' => $table_name),
			array('name' => 'pages_id', 'label_align' => 'left', 'label' => '', 'type' => 'hidden', 'size' => '25', 'value' => $id),
			$btn);

	$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_pages.php', 'enctype' => '', 'style_class' => 'pagesAddFrm', 'title' => '', 'content_frm' => $recs);
	$form = new SHAV_Form();
	$form->createFromArray($params);

	$content  = '<table width="100%">';
	$content .= '<tr>';
	$content .= '<td><img src="images/icon/pages.png" /></td>';
	$content .= '<td>'.$form->drawForm().'</td>';
	$content .= '</tr>';
	$content .= '</table>';

	if((int)$id > 0)
		return $content;

	if($table_name == 'admin_pages')
		$sts_window = shav_createModalWindow('admin_page_add', 'Добавить страничку', 'Добавление странички админ. раздела', $content);
	elseif($table_name == 'pages')
		$sts_window = shav_createModalWindow('site_page_add', 'Добавить страничку сайта', 'Добавление странички сайта', $content);

	return $sts_window;
}

/** Выводит все существующие странички.
	@param $table имя таблицы, данные из которой будут показаны.
	@return Выводит список всех страниц.*/
function createAllPages($table = 'admin_pages')
{
	global $shavDB;

	$sql = 'SELECT * FROM '.$table;
	$results = $shavDB->get_results($sql);
	$content  = '<table width="100%" border="1">';
	$content .= '<tr align="center">';
	$content .= '<td width="60%">Page title</td>';
	$content .= '<td width="25%">Icon</td>';
	$content .= '<td width="15%"></td>';
	$content .= '</tr>';
	foreach($results as $rec)
	{
		$content .= '<tr>';
		$content .= '<td>'.$rec['p_title'].'</td>';
		$content .= '<td><img src="'.$rec['icons'].'" /></td>';
		$content .= '<td><a href="/admin/index.php?action=edit&id_page='.$rec['pages_id'].'&table='.$table.'"><img src="images/icon/add.png" /></a><a href="/admin/edit_pages.php?action=del&id_page='.$rec['pages_id'].'&table='.$table.'"><img src="images/icon/delete.png" /></a></td>';
		$content .= '</tr>';
	}
	$content .= '</table>';

	if($_GET['action'] == 'edit' && (int)$_GET['id_page'] > 0 && $_GET['table'] != '')
	{
		$content = createAddPageWindow($_GET['table'], $_GET['id_page']);
	}

	return $content;
}
?>