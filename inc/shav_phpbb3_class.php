<?php

define('IN_PHPBB', true);

//Установка главной директории форума
if(strstr($_SERVER['REQUEST_URI'], '/admin/'))
	define('PHPBB_ROOT_PATH', '../forum/');
elseif(strstr($_SERVER['REQUEST_URI'], '/'))
	define('PHPBB_ROOT_PATH', './forum/');
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';

//Установка файла для работы с API форума
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path.'includes/functions_user.'.$phpEx);

//Запускаем сессию для работы с авторизацией
$user->session_begin();
$auth->acl($user->data);
$user->setup('ucp');

//Массив результатов работы авторизации
$login = array();

/** @class SHAV_PHPBB3
 *	@brief Класс для работы с форумом phpbb3.
 *	Пример использования:
 *	@code
 * $param = array(	'reg_frm'=>'',				//HTML-код формы региятрации
 * 					'reg_with_site'=>true,		//Использовать общую форму регистрации.
 * 					'with_forum_avatar'=>true,	//Использовать аватар с форума на сайте.
 * 					'after_auth'=>'',			//Сообщение после авторизации.
 * 					'with_list'=>true,			//Выводить статистику
 * 					'with_legend'=>true,		//Выводить легенду посещений
 * 					'login_frm'=>''				//HTML-код формы авторизации
 * 				);
 * $phpBB = new SHAV_PHPBB3($param);
 * $phpBB->show_data();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 10.12.2009
 *	@date Обновленно: 06.02.2011*/
class SHAV_PHPBB3 extends SHAV_Object
{
	/** HTML-код формы региятрации.*/
	public $regForm = '';

	/** Использовать общую форму регистрации*/
	public $usingRegWithSite = false;

	/** Использовать аватар с форума на сайте*/
	public $usingForumAvatar = false;

	/** Сообщение после авторизации*/
	public $afterAuth = '';

	/** Выводить статистику*/
	public $usingForumList = false;

	/** Выводить легенду посещений*/
	public $usingForumLegand = false;

	/** HTML-код формы авторизации*/
	public $loginForm = '';

	/** Конструктор класса, позволет вывести дополнительно легенду и статистику посещения.
	 *	@param $config_prm - массив настроек.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.12.2009
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_PHPBB3($config_prm = array())
	{
		$this->regForm = $config_prm['reg_frm'];
		$this->usingRegWithSite = $config_prm['reg_with_site'];
		$this->usingForumAvatar = $config_prm['with_forum_avatar'];
		$this->afterAuth = $config_prm['after_auth'];
		$this->usingForumList = $config_prm['with_list'];
		$this->usingForumLegand = $config_prm['with_legend'];
		$this->loginForm = $config_prm['login_frm'];
	}

	/** Выводит форму логина или данные об успешной авторизации.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.12.2009
	 *	@date Обновленно: 06.02.2011*/
	function show_data()
	{
		global $user, $auth, $config, $shavDB;

		//Если нажата кнопка "выход"
		if(($_GET['action'] == 'logout' || isset($_POST['logout'])) && $user->data['user_id'] != ANONYMOUS)
		{
			$user->session_kill();
			$shavDB->quit();
		}

		//Проводим регистрацию пользователя
		if($_POST['reg'] && $_SESSION['users_id'] <= 0 && $user->data['user_id'] == ANONYMOUS)
		{
			global $shavDB;

			if($_POST['login'] != '' && $_POST['pass'] != '' && $_POST['email'] != '')
			{
				if($this->usingRegWithSite == true)
				{
					$sql = 'INSERT INTO users SET fio = "'.$_POST['fio'].'", login = "'.$_POST['login'].'", pass = "'.md5($_POST['pass']).'", email = "'.$_POST['email'].'", ban_id = 1';
					$id = $shavDB->insert_data($sql, 'users_id');

					if($id > 0)
						$this->regUser($_POST['login'], $_POST['pass'], $_POST['email']);
				}
				else
					$this->regUser($_POST['login'], $_POST['pass'], $_POST['email']);

				$_GET['action'] = '';
			}

			$_GET['action'] = 'reg';
		}

		//Если нажали зарегистрироваться
		if($_GET['action'] == 'reg' && $user->data['user_id'] == ANONYMOUS)
		{
			return $this->regForm;
		}

		//Если нажата кнопка "вход"
		if(isset($_POST['go']) && $user->data['user_id'] == ANONYMOUS)
		{
			$username = request_var('login', '', true);
			$password = request_var('pass', '', true);
			$autologin	= (!empty($_POST['autologin'])) ? true : false;

			$login = $auth->login($username, $password, $autologin);
			$shavDB->login($_POST['login'], $_POST['pass']);

		}

		//Отправляем header для установки cookie
		header('Content-type: text/html; charset=UTF-8');
		header('Cache-Control: private, no-cache="set-cookie"');
		header('Expires: 0');
		header('Pragma: no-cache');

		//Проверяем авторизацию пользователя. Если успешно выводим информацию
		if((!empty($login) && $login['status'] == LOGIN_SUCCESS) || $user->data['user_id'] != ANONYMOUS)
		{
			// Reset permissions data if user has just logged in
			if(!empty($login))
				$auth->acl($user->data);

			$user_fio = $_SESSION['fio'];
			if($_SESSION['fio'] == '')
				$user_fio = $_SESSION['login'];

			//DrawObject($user->data);
			$path = $_SESSION['avatar'];
			if($this->usingForumAvatar == true)
			{
				$file = $user->data['user_avatar'];
				$path = 'http://'.$_SERVER['HTTP_HOST'].'/forum/download/file.php?avatar='.$file;
			}

			$content = shav_createContentsByTags(array('$fio$' => $_SESSION['fio'], '$forum_full$' => get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour']), '$avatar$' => $path), $this->afterAuth);

			if($this->usingForumList == true)
			{
				$online_list = array();
				$online_list = $this->online_list();
				if(!empty($online_list))
				{
					$content .= "<hr>{$user->lang['WHO_IS_ONLINE']}</hr><p>{$online_list['TOTAL_USERS_ONLINE']} ({$online_list['L_ONLINE_EXPLAIN']})<br />{$online_list['RECORD_USERS']}<br /> <br />{$online_list['LOGGED_IN_USER_LIST']}";
		    	}
			}

		    $legend = '';
			if($this->usingForumLegand == true)
			{
				$legend = $this->display_legend();
				$content .= "<br /><p>{$user->lang['LEGEND']}: $legend</p>";
			}
		}
		else
		{
			//Если пользователю не удалось войти в систему, то показываем ошибку и
			if(isset($login['error_msg']) && $login['error_msg'])
			{
			    $err = $user->lang[$login['error_msg']];
			    // Assign admin contact to some error messages
			    if ($login['error_msg'] == 'LOGIN_ERROR_USERNAME' || $login['error_msg'] == 'LOGIN_ERROR_PASSWORD')
			    {
					$err = (!$config['board_contact']) ? sprintf($user->lang[$login['error_msg']], '', '') : sprintf($user->lang[$login['error_msg']], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>');
			    }

				$content = $err . '<br />';
			}

			//Выводим форму авторизации
			$content = $this->loginForm;
		}

		return $content;
	}

	/** Выводит список посещения
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.12.2009
	 *	@date Обновленно: 06.02.2011*/
	private function online_list()
	{
		global $db, $config, $user, $auth, $phpEx;

		$l_online_users = $online_userlist = $l_online_record = '';

		if ($config['load_online'] && $config['load_online_time'])
		{
			$logged_visible_online = $logged_hidden_online = $guests_online = $prev_user_id = 0;
			$prev_session_ip = $reading_sql = '';

			if (!empty($_REQUEST['f']))
			{
	    		$f = request_var('f', 0);

	   			$reading_sql = ' AND s.session_page '.$db->sql_like_expression("{$db->any_char}_f_={$f}x{$db->any_char}");
	    	}

	   		//Получаем количество гостей
	    	if (!$config['load_online_guests'])
	    	{
				if ($db->sql_layer === 'sqlite')
		    		$sql = 'SELECT COUNT(session_ip) as num_guests FROM (SELECT DISTINCT s.session_ip FROM '.SESSIONS_TABLE.' s WHERE s.session_user_id = '.ANONYMOUS.' AND s.session_time >= ' . (time() - ($config['load_online_time'] * 60)) .$reading_sql.')';
				else
		   			 $sql = 'SELECT COUNT(DISTINCT s.session_ip) as num_guests FROM ' . SESSIONS_TABLE . ' s WHERE s.session_user_id = '.ANONYMOUS.' AND s.session_time >= '.(time() - ($config['load_online_time'] * 60)).$reading_sql;

				$result = $db->sql_query($sql);
				$guests_online = (int) $db->sql_fetchfield('num_guests');
				$db->sql_freeresult($result);
	   		}

	    	$sql = 'SELECT u.username, u.username_clean, u.user_id, u.user_type, u.user_allow_viewonline, u.user_colour, s.session_ip, s.session_viewonline FROM '.USERS_TABLE.' u, '.SESSIONS_TABLE.' s WHERE s.session_time >= '.(time() - (intval($config['load_online_time']) * 60)).$reading_sql.((!$config['load_online_guests']) ? ' AND s.session_user_id <> '.ANONYMOUS : '').' AND u.user_id = s.session_user_id ORDER BY u.username_clean ASC, s.session_ip ASC';
	    	$result = $db->sql_query($sql);

	    	while ($row = $db->sql_fetchrow($result))
			{
				//Зарегистрированные пользователя
				if ($row['user_id'] != ANONYMOUS)
				{
					//Пропускаем несколь сессий для обного пользователя
					if ($row['user_id'] != $prev_user_id)
					{
						if ($row['session_viewonline'])
							$logged_visible_online++;
						else
						{
				 			$row['username'] = '<em>' . $row['username'] . '</em>';
							$logged_hidden_online++;
						}

						if (($row['session_viewonline']) || $auth->acl_get('u_viewonline'))
						{
							$user_online_link = get_username_string(($row['user_type'] <> USER_IGNORE) ? 'full' : 'no_profile', $row['user_id'], $row['username'], $row['user_colour']);
							$online_userlist .= ($online_userlist != '') ? ', ' . $user_online_link : $user_online_link;
						}
		    		}

		   			$prev_user_id = $row['user_id'];
				}
				else
				{
					//Пропускаем несколь сессий для обного пользователя
		    		if ($row['session_ip'] != $prev_session_ip)
						$guests_online++;
				}

				$prev_session_ip = $row['session_ip'];
	    	}

			$db->sql_freeresult($result);

		    if (!$online_userlist)
				$online_userlist = $user->lang['NO_ONLINE_USERS'];

	   		if (empty($_REQUEST['f']))
				$online_userlist = $user->lang['REGISTERED_USERS'] . ' ' . $online_userlist;
	    	else
	    	{
				$l_online = ($guests_online == 1) ? $user->lang['BROWSING_FORUM_GUEST'] : $user->lang['BROWSING_FORUM_GUESTS'];
				$online_userlist = sprintf($l_online, $online_userlist, $guests_online);
	    	}

	    	$total_online_users = $logged_visible_online + $logged_hidden_online + $guests_online;

	    	if ($total_online_users > $config['record_online_users'])
	    	{
				set_config('record_online_users', $total_online_users, true);
				set_config('record_online_date', time(), true);
	    	}

	    	//Создаем массив тех, кто в сети
	   		$vars_online = array(	'ONLINE'=> array('total_online_users', 'l_t_user_s'),
									'REG'	=> array('logged_visible_online', 'l_r_user_s'),
									'HIDDEN'=> array('logged_hidden_online', 'l_h_user_s'),
			     					'GUEST'	=> array('guests_online', 'l_g_user_s'));

			foreach ($vars_online as $l_prefix => $var_ary)
			{
				switch (${$var_ary[0]})
				{
				    case 0:
						${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_ZERO_TOTAL'];
						break;

				    case 1:
						${$var_ary[1]} = $user->lang[$l_prefix . '_USER_TOTAL'];
						break;

				    default:
						${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_TOTAL'];
						break;
				}
			}
			unset($vars_online);

			$l_online_users = sprintf($l_t_user_s, $total_online_users);
			$l_online_users .= sprintf($l_r_user_s, $logged_visible_online);
			$l_online_users .= sprintf($l_h_user_s, $logged_hidden_online);
			$l_online_users .= sprintf($l_g_user_s, $guests_online);

			$l_online_record = sprintf($user->lang['RECORD_ONLINE_USERS'], $config['record_online_users'], $user->format_date($config['record_online_date']));

			$l_online_time = ($config['load_online_time'] == 1) ? 'VIEW_ONLINE_TIME' : 'VIEW_ONLINE_TIMES';
			$l_online_time = sprintf($user->lang[$l_online_time], $config['load_online_time']);
		}
		else
			$l_online_time = '';

		return array(	'TOTAL_USERS_ONLINE'	=> $l_online_users,
						'LOGGED_IN_USER_LIST'	=> $online_userlist,
						'RECORD_USERS'		=> $l_online_record,
						'L_ONLINE_EXPLAIN'	=> $l_online_time,);
	}

	/** Выводит легенду
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.12.2009
	 *	@date Обновленно: 06.02.2011*/
	private function display_legend()
	{
		global $db, $config, $user, $auth, $phpbb_root_path, $phpEx;

		if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
		    	$sql = 'SELECT group_id, group_name, group_colour, group_type FROM '.GROUPS_TABLE.' WHERE group_legend = 1 ORDER BY group_name ASC';
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type FROM '.GROUPS_TABLE.' g LEFT JOIN '.USER_GROUP_TABLE.' ug ON (g.group_id = ug.group_id AND ug.user_id = '.$user->data['user_id'].' AND ug.user_pending = 0) WHERE g.group_legend = 1 AND (g.group_type <> '.GROUP_HIDDEN.' OR ug.user_id = '.$user->data['user_id'].') ORDER BY g.group_name ASC';
		}
		$result = $db->sql_query($sql);

		$legend = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#'.$row['group_colour'].'"' : '';

			if ($row['group_name'] == 'BOTS')
				$legend .= (($legend != '') ? ', ' : '').'<span'.$colour_text.'>'.$user->lang['G_BOTS'].'</span>';
			else
		    	{
				$legend .= (($legend != '') ? ', ' : '').'<a'.$colour_text.' href="'.append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g='.$row['group_id']).'">'.(($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_'.$row['group_name']] : $row['group_name']).'</a>';
			}
		}
		$db->sql_freeresult($result);

		return $legend;
	}

	/** Выводит форму регистрации пользоватле на сайте и на форуме
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.12.2009
	 *	@date Обновленно: 06.02.2011*/
	private function regUser($login, $password, $email)
	{
		global $db;

		$sql = 'SELECT group_id FROM '.GROUPS_TABLE." WHERE group_name = '".$db->sql_escape('REGISTERED')."' AND group_type = ".GROUP_SPECIAL;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$group_id = $row['group_id'];

		$timezone = '+2';
		$language = 'ru';
		$user_type = USER_NORMAL;
		$user_actkey = md5(rand(0, 100) . time());
		$user_actkey = substr($user_actkey, 0, rand(8, 12));
		$user_ip = $user->ip;
		$registration_time = time();
		$user_inactive_time = time();
		$user_row = array(	'username'              => $login,
							'user_password'         => phpbb_hash($password),
							'user_email'            => $email,
							'group_id'              => (int) $group_id,
							'user_timezone'         => (float) $timezone,
							'user_dst'              => $is_dst,
							'user_lang'             => $language,
							'user_type'             => $user_type,
							'user_actkey'           => $user_actkey,
							'user_ip'               => $user_ip,
							'user_regdate'          => $registration_time,
							'user_inactive_reason'  => $user_inactive_reason,
							'user_inactive_time'    => $user_inactive_time,);

		$user_id = user_add($user_row);
	}
}
?>