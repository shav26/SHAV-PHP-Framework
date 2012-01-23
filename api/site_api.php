<?php

include_once('../inc/shav_config.php');

$shavAPI = new SHAV_API();

/*Пример использования:
 http://test_framework.local/api/test_api.php?token=1dfjgdkfjgerbtyertyerte56dfgkldgi&action=get_data&table=api_app_types&app_type_name&app_type_id=1
 Параметры запроса:
	token - идентификатор приложения для синхронизации с сервером;
	action - действия которые необходимо выполнить, может принимать такие значения (get_data, add, delete, update);
	table - имя таблицы с которой будет работать запрос (может быть любай таблица базы данных);
	where - усновия для получения, обновления или удаления данных;
	Все остальные параметры - это те данные которые необходимо получить. В данный момент это просто поля таблицы, с которой будет производится работа.*/

if(empty($_POST) && !empty($_GET))
	$_POST = $_GET;

if($_POST['token'] != '')
{
	$id = $shavDB->get_vars('SELECT app_id FROM api_apps WHERE token = "'.$_POST['token'].'"');

	if((int)$id > 0)
	{
		$data = new SHAV_QueryAPI($_POST);
		$shavAPI->doRequestToDB($data);
		$shavAPI->results->drawCurrentData(true);
	}
	else
	{
		$res = new SHAV_ResultsAPI();
		$res->code = 0;
		$res->message = 'Приложения с таким токеном не существует. Проверте правильность ввода токена и повторите попытку.';
		$res->drawCurrentData(true);
	}
}
else
{
	$res = new SHAV_ResultsAPI();
	$res->code = 0;
	$res->message = 'Не указан токен приложения. Пожайлуста, укажите правильный токен для Вашего приложения.';
	$res->drawCurrentData(true);
}

$shavDB->db_close();
?>