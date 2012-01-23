<?php
/*Подуль для работы с TODO-трекером посредством JSON ответов.
Автор: Andrew Shapovalov
Созданно: 12.06.2010
Обновленно: 25.06.2010*/

include_once('../inc/shav_config.php');
global $shavDB;

/*
//Данный участок кода позволяет проверять работу запросов изпользуя строку браузера, при этом название параметров слдует соблюдать.
if(empty($_POST) && !empty($_GET))
{
	$_POST = $_GET;
}*/


if((int)$_POST['user_id'] <= 0)
{
	echo createErrorMassage(0, 'Вы не авторизированны! Пожалуйста, авторизируйтесь!');
	return;
}

$isadmin = $shavDB->get_vars('SELECT isadmin FROM users WHERE users_id = '.(int)$_POST['user_id']);

if($_POST['action'] == 'get_projects')	//Получаем  все проекты
{
	if($isadmin > 1)
		$sql = 'SELECT ttp.todo_prj_id FROM tracker_todo_projects ttp, tracker_todo_users ttu WHERE ttu.user_id = '.(int)$_POST['user_id'].' AND ttu.projetc_id = ttp.todo_prj_id ORDER BY date_end ASC';
	elseif($isadmin == 1)
		$sql = 'SELECT todo_prj_id FROM tracker_todo_projects';
	$results = $shavDB->get_results($sql);

	$array = array();
	foreach($results as $rec)
	{
		$prj = new SHAV_Project($rec['todo_prj_id']);
		$array[] = $prj->convertToArray();
	}

	if(!empty($array))
		echo shav_jsonFixCyr(shav_convertArrayToJSON(array('projects'=>$array, 'code'=>1, 'error'=>'Полученны все доступные проекты.')));
	else
		echo createErrorMassage(0, 'Нет проектов.');
}
elseif($_POST['action'] == 'get_users')	//Получаем всех пользователей для редактирования (Администраторский раздел клиента)
{
	if($isadmin != 1)
	{
		echo createErrorMassage(0, 'У вас не достаточно прав для получения информации. Для получения этой информации вам нужны права администратора.');
		return;
	}

	$sql = 'SELECT users_id FROM users';
	$results = $shavDB->get_results($sql);

	$array = array();
	foreach($results as $user)
	{
		$u = new SHAV_TodoUser($user['users_id']);
		$array[] = $u;
	}

	echo shav_jsonFixCyr(shav_convertArrayToJSON(array("users"=>$array, 'code'=>1, 'error'=>'Полученны все зарегистрированные пользователи.')));
}
//Добовление/редактирование и удаление данных
//Добавления пользователя
elseif($_POST['action'] == 'add_user' && $isadmin == 1)
{
	$user_login = $_POST['login'];
	$user_pass = md5($_POST['pass']);
	$user_fio = $_POST['fio'];
	$user_email = $_POST['email'];
	$user_status = (int)$_POST['sts_id'];
	$user_ban = (int)$_POST['ban_sts_id'];

	$sql = 'SELECT users_id FROM users WHERE login = "'.$user_login.'" AND pass = "'.$user_pass.'"';
	$id = $shavDB->get_vars($sql);

	if((int)$id <= 0)
	{
		$sql = 'INSERT INTO users SET login = "'.$user_login.'", pass = "'.$user_pass.'", fio = "'.$user_fio.'", email = "'.$user_email.'", ban_id = '.$user_ban.', isadmin = '.$user_status;
		$shavDB->insert_data($sql);

		echo createErrorMassage(1, 'Пользователь успешно зарегистрирован.');
	}
	else
	{
		echo createErrorMassage(0, 'Пользователь с такими данными уже зарегистрирован.');
	}
}
//Изменить данные пользователя
elseif($_POST['action'] == 'save_user' && (int)$_POST['id_user'] > 0 && $isadmin == 1)
{
	$user_login = $_POST['login'];
	$user_pass = md5($_POST['pass']);
	$user_fio = $_POST['fio'];
	$user_email = $_POST['email'];
	$user_status = (int)$_POST['sts_id'];
	$user_ban = (int)$_POST['ban_sts_id'];

	if($_POST['pass'] != "")
		$sql = 'UPDATE users SET login = "'.$user_login.'", pass = "'.$user_pass.'", fio = "'.$user_fio.'", email = "'.$user_email.'", ban_id = '.$user_ban.', isadmin = '.$user_status.' WHERE users_id = '.(int)$_POST['id_user'];
	else
		$sql = 'UPDATE users SET login = "'.$user_login.'", fio = "'.$user_fio.'", email = "'.$user_email.'", ban_id = '.$user_ban.', isadmin = '.$user_status.' WHERE users_id = '.(int)$_POST['id_user'];
	$shavDB->get_results($sql);

	echo createErrorMassage(1, 'Данные пользователя успешно измененны.');
}
//Удаление пользователя
elseif($_POST['action'] == 'del_user' && (int)$_POST['id_user'] > 0 && $isadmin == 1)
{
	$sql = 'SELECT todo_prj_id FROM tracker_todo_projects';
	$results = $shavDB->get_results($sql);

	$user = new SHAV_TodoUser((int)$_POST['id_user']);

	foreach($results as $rec)
	{
		$user->deleteUserFromProjectById((int)$rec['todo_prj_id']);
	}

	$sql = 'DELETE FROM users WHERE users_id = '.(int)$_POST['id_user'];
	$shavDB->get_results($sql);

	echo createErrorMassage(1, 'Данные пользователя успешно удаленны.');
}
//Добавляем новый проект.
elseif($_POST['action'] == 'add_project')
{
	$prj = new SHAV_Project();
	$prj->title = htmlspecialchars($_POST['title']);
	$prj->description = htmlspecialchars($_POST['desc']);
	$prj->startDate = (int)$_POST['date_start'];
	$prj->deadLine = (int)$_POST['date_end'];
	$prj->authorId = $_POST['author'];
	$prj->users = array();
	$usersId = split(",", $_POST['users_ids']);
	foreach($usersId as $userId)
	{
		$user = new SHAV_TodoUser((int)$userId);

		if($user->id != (int)$_POST['user_id'])
			$user->accessId = 2;

		$prj->users[] = $user;
	}

	$prj->saveToDB();

	echo createErrorMassage(1, 'Данные были успешно изменены.');
}
//Изменяем существующий проект
elseif($_POST['action'] == 'save_project' && (int)$_POST['prj_id'] > 0)
{
	$prj = new SHAV_Project((int)$_POST['prj_id']);
	$prj->title = htmlspecialchars($_POST['title']);
	$prj->description = htmlspecialchars($_POST['desc']);
	$prj->startDate = (int)$_POST['date_start'];
	$prj->deadLine = (int)$_POST['date_end'];
	$prj->authorId = $_POST['author'];
	$prj->users = array();
	$usersId = split(",", $_POST['users_ids']);
	foreach($usersId as $userId)
	{
		$user = new SHAV_TodoUser((int)$userId);

		if($user->id != (int)$_POST['user_id'])
			$user->accessId = 2;

		$prj->users[] = $user;
	}
	$prj->saveToDB();

	echo createErrorMassage(1, 'Данные были успешно изменены.');
}
//Удаление проекта
elseif($_POST['action'] == 'del_project' && (int)$_POST['prj_id'] > 0)
{
	$prj = new SHAV_Project((int)$_POST['prj_id']);
	$prj->deleteFromDB();


	echo createErrorMassage(1, 'Проект был успешно удален.');
}
//Добавление нового задание
elseif($_POST['action'] == 'add_todo' && (int)$_POST['project_id'] > 0)
{
	$todo = new SHAV_Todo();
	$todo->title = htmlspecialchars($_POST['title']);
	$todo->description = htmlspecialchars($_POST['desc']);
	$todo->timeBegin = (int)$_POST['date_start'];
	$todo->statusId = (int)$_POST['todo_status'];
	$todo->priorityId = (int)$_POST['todo_priority'];
	$todo->deadLine = (int)$_POST['data_end'];
	$todo->authorId = (int)$_POST['author'];
	$todo->projectId = (int)$_POST['project_id'];
	$todo->saveToDB();

	echo createErrorMassage(1, 'Задача была успешно добавленна.');
}
//Изменение задание
elseif($_POST['action'] == 'save_todo' && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);
	$todo->title = htmlspecialchars($_POST['title']);
	$todo->description = htmlspecialchars($_POST['desc']);
	$todo->timeBegin = (int)$_POST['date_start'];
	$todo->statusId = (int)$_POST['todo_status'];
	$todo->priorityId = (int)$_POST['todo_priority'];
	$todo->deadLine = (int)$_POST['data_end'];
	$todo->authorId = (int)$_POST['author'];
	$todo->projectId = (int)$_POST['project_id'];
	$todo->saveToDB();

	echo createErrorMassage(1, 'Задача была успешно изменена.');
}
//Удаление задачи
elseif($_POST['action'] == 'del_todo' && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);
	$todo->deleteFromDB();

	echo createErrorMassage(1, 'Задача была успешно удалена.');
}
//Добавление нового комментария
elseif($_POST['action'] == 'add_comment' && (int)$_POST['todo_id'] > 0)
{
	$comment = new SHAV_Comment();
	$comment->text = htmlspecialchars($_POST['text']);
	$comment->authorId = (int)$_POST['author'];
	$comment->addDate = time();
	$comment->todoId = (int)$_POST['todo_id'];
	$comment->saveToDB();

	echo createErrorMassage(1, 'Комментарий был успешно добавлен.');
}
//Изменение комментария
elseif($_POST['action'] == 'save_comment' && (int)$_POST['comment_id'] > 0)
{
	$comment = new SHAV_Comment((int)$_POST['comment_id']);
	$comment->text = htmlspecialchars($_POST['text']);
	$comment->saveToDB();

	echo createErrorMassage(1, 'Задача была успешно изменена.');
}
//Удаление комментария
elseif($_POST['action'] == 'del_comment' && (int)$_POST['comment_id'] > 0)
{
	$comment = new SHAV_Comment((int)$_POST['comment_id']);
	$comment->deleteFromDB();

	echo createErrorMassage(1, 'Задача была успешно удалена.');
}
else
{
	DrawObject($_POST);
	echo createErrorMassage(0, 'Ошибка запросса!!!');
}



//******************************************************************************
//*************************** ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
function createErrorMassage($code, $msg = 'Сообщение об ошибке')
{
	$array = array('error'=>$msg, 'code'=>$code, 'user_id'=>(int)$_POST['user_id']);
	$str = shav_jsonFixCyr(shav_convertArrayToJSON($array));

	return $str;
}
?>