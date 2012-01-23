<?php
global $shavJS;

$title = 'Администраторский раздел';

//СОЗДАНИЕ ЛЕВОЙ ПАНЕЛИ
if((int)$_SESSION['isadmin'] == 1)
{
	$user_info  = '<table width="100%">';
	$user_info .= '<tr><td>Ф.И.О.:</td><td>'.$_SESSION['fio'].'</td></tr>';
	$user_info .= '<tr><td>Статуст:</td><td><b>Администратор</b></td></tr>';
	$user_info .= '<tr><td>Сообщений:</td><td><b class="error">Недоступно</b></td></tr>';
	$user_info .= '</table>';
	$info_blok = shav_createSideBar('sidebar', 'Пользователи', $user_info);
	//$user_info .= '<tr><td>Статуст:</td><td><b>Администратор</b></td></tr>';
	
	//Создаем пункты для редактирования пользователей
	$user_add_window = '<fieldset>'.createAddUserWindow();
	$user_edit_window = '<div><a href="/admin/index.php?action=show_users">Редактирование профелей</a></div></fieldset><br />';
	//Создаем пункты редавтирования типов пользователей
	$user_type_add_window = '<fieldset>'.createUserTypeAddWondow();
	$user_type_edit_window = '<div><a href="/admin/index.php?action=show_type">Редактирование типов</a></div></fieldset><br />';
	//Создаем пункты редактирования статуса
	$status_add_window = '<fieldset>'.createStatusAddWindow();
	$status_edit = '<div><a href="/admin/index.php?action=show_status">Редактировать статусы</a></div></fieldset>';
	$user = shav_createSideBar('sidebar', 'Пользователи', $user_add_window.$user_edit_window.$user_type_add_window.$user_type_edit_window.$status_add_window.$status_edit);
	
	//Создаем пункты для добавления страничек
	$page_admin_add_window = createAddPageWindow();
	$page_admin_edit = '<div><a href="/admin/index.php?action=show_pages&table=admin_pages">Редактирование страниц</a></div>';
	$page_site_add_window = createAddPageWindow('pages');
	$page_site_edit = '<div><a href="/admin/index.php?action=show_pages&table=pages">Редактирование страниц сайта</a></div>';
	$pages = shav_createSideBar('sidebar', 'Странички', $page_admin_add_window.$page_admin_edit.$page_site_add_window.$page_site_edit);
	
	//Создаем левую панель
	$left = $info_blok.$user.$pages.$tmp;
	$right = '<p>Содержимое правой панели смотри в файле admin/def_admin.php</p>';
}
elseif(!empty($_SESSION) && (int)$_SESSION['users_id'] > 1)
{
	//Создаем информацию о пользователе:
	$user_info  = '<table width="100%">';
	$user_info .= '<tr><td>Ф.И.О.:</td><td>'.$_SESSION['fio'].'</td></tr>';
	$user_info .= '<tr><td>Статуст:</td><td><b>Пользователь</b></td></tr>';
	$user_info .= '<tr><td>Сообщений:</td><td><b class="error">Недоступно</b></td></tr>';
	$user_info .= '</table>';
	$info_blok = shav_createSideBar('sidebar', 'Пользователи', $user_info);
	//$user_info .= '<tr><td>Статуст:</td><td><b>Администратор</b></td></tr>';
	
	$left = $info_blok;
}

//$content .= shav_createSlider('slider2', 'test', 'Зарегистрировать').'<input type="text" name="test" id="test" value="" />';

//Создаем страницу админки.
if(!empty($_SESSION) && (int)$_SESSION['users_id'] > 0)
{
	$header = '<div class="logo"><a href="/"><img src="/images/logo2.png" /></a></div><div style="float:left;"><div align="center" style="font:28px Arial;width:700px;height:50px;">#PAGE_TITLE#</div><div class="menu_pos">'.$jQueryMenu.'</div></div><div style="float:right;"><a href="/admin/index.php?action=logout">[X]</a></div>';
	$footer = '<div align="center">Автор: Andrew Shapovalov; 3 декабря 2009<br />Все права защещены!!!<br /><a href="http://lmwshav.org.ua">Сайт автора</a></div>';
}
//Выводим форму авторизации
elseif(empty($_SESSION) || $_SESSION['users_id'] <= 0)
{
	//Создаем страницу админки.
	$header = '<div class="logo"><a href="/"><img src="/images/logo2.png" /></a></div><div style="float:left;"><div align="center" style="font:28px Arial;width:728px;height:50px;">'.$title.'</div><div class="menu_pos">'./*$jQueryMenu.*/'</div></div>';
	$footer = '<div align="center">Автор: Andrew Shapovalov; 3 декабря 2009<br />Все права защещены!!!<br /><a href="http://lmwshav.org.ua">Сайт автора</a></div>';
	
	$params_login = array('login' => 'Логин:', 'pass' => 'Пароль:', 'reg' => '', 'action_scrp' => '', 'reg_scrp' => '', 'button' => 'Войти');
	$content = '<div align="center"><div style="width:160px;">'.shav_createLoginForm($params_login).'</div></div>';
	
	$page = new SHAV_Page();
	$tags = array('#TITLE#'=>$title, '#DESCRIPTION#'=>'', '#KEYWORDS#'=>'', '#JAVA_SCRIPTS#'=>$shavJS->drawJS(), '#HEADER#'=>$header, '#FOOTER#'=>$footer, '#CONTENT#'=>$content, '#LEFT_PANEL#'=>'', '#RIGHT_PANEL#'=>'');
	$page->createPageFromFileWithTags('../tmpls/admin/admin_index_panel.html', $tags);
	
	$page->drawPage();
	return;
}

?>