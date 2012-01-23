<?php
/*Модуль авторизации пользователей для внешних приложений.
Автор: Andrew Shapovalov
Создано: 12.06.2010
Обновленно: */

include_once('../inc/shav_config.php');
global $shavDB;

if($_POST['login'] != '' && $_POST['password'] != '')
{
	$sql = 'SELECT users_id FROM users WHERE login = "'.$_POST['login'].'" AND pass = "'.md5($_POST['password']).'"';
	$results = $shavDB->get_results($sql);

	$userID = (int)$results[0]['users_id'];

	if($userID > 0)
	{
		$curUser = new SHAV_TodoUser((int)$userID);

		$sql = 'SELECT * FROM bans_status';
		$allBansStatus = $shavDB->get_results($sql);

		$res1 = array();
		foreach($allBansStatus as $ban)
			$res1[] = array("id"=>$ban['bans_status_id'], "title"=>$ban['bans_status_name']);


		$sql = 'SELECT * FROM users_type';
		$allUserType = $shavDB->get_results($sql);

		$res2 = array();
		foreach($allUserType as $type)
			$res2[] = array("id"=>$type['users_type_id'], "title"=>$type['users_status_name']);

		$sql = 'SELECT * FROM tracker_todo_status';
		$allTodoStatus = $shavDB->get_results($sql);

		$res3 = array();
		foreach($allTodoStatus as $rec)
			$res3[] = array("id"=>$rec['todo_status_id'], "title"=>$rec['todo_status_name']);

		//
		$sql = 'SELECT * FROM tracker_todo_priority';
		$allTodoPriority = $shavDB->get_results($sql);

		$res4 = array();
		foreach($allTodoPriority as $rec)
			$res4[] = array("id"=>$rec['todo_priority_id'], "title"=>$rec['todo_priority_name']);

		$data = array('user'=>$curUser, 'all_ban_status'=>$res1, 'all_user_types'=>$res2, 'allTodoStatus'=>$res3, 'allTodoPriority'=>$res4, "code"=>1, 'error'=>'Вы успешно авторезованны.');
		$json = shav_jsonFixCyr(shav_convertArrayToJSON($data));
		echo $json;
		return;
	}
	else
	{
		echo createErrorMassage(0, "Вы ввели не правильный логин или пароль.");
	}
}
else
{
	echo createErrorMassage(0, "Логин и/или пароль пустые.");
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