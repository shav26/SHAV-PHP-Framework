<?php
include_once('../inc/shav_config.php');
global $shavDB;

if(empty($_SESSION) || $_SESSION['users_id'] <= 0)
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin');

//*******************************************************************************
// ФУНКЦИИ ДЛЯ РАБОТЫ С ПРОЕКТАМИ

//Создаем новый проект
if($_POST['add'])
{
	$prj = new SHAV_Project();
	$prj->title = htmlspecialchars($_POST['title']);
	$prj->description = htmlspecialchars($_POST['desc']);
	$prj->startDate = strtotime($_POST['date_start']);
	$prj->deadLine = strtotime($_POST['data_end']);
	$prj->authorId = $_POST['author'];
	$prj->users = array();
	foreach($_POST['users_ids'] as $userId)
	{
		$user = new SHAV_TodoUser($userId);

		if($user->id != $_SESSION['users_id'])
			$user->accessId = 2;
		
		$prj->users[] = $user;
	}

	$prj->saveToDB();
}

//Редактирование проекта
if($_POST['save'] && (int)$_POST['prj_id'] > 0)
{
	$prj = new SHAV_Project((int)$_POST['prj_id']);
	$prj->title = htmlspecialchars($_POST['title']);
	$prj->description = htmlspecialchars($_POST['desc']);
	$prj->startDate = strtotime($_POST['date_start']);
	$prj->deadLine = strtotime($_POST['data_end']);
	$prj->authorId = $_POST['author'];
	$prj->users = array();
	foreach($_POST['users_ids'] as $userId)
	{
		$user = new SHAV_TodoUser($userId);
		
		if($user->id != $_SESSION['users_id'])
			$user->accessId = 2;
		
		$prj->users[] = $user;
	}

	$prj->saveToDB();
}

//Удаление проекта
if($_POST['delete'] && (int)$_POST['prj_id'] > 0)
{
	$prj = new SHAV_Project((int)$_POST['prj_id']);
	$prj->deleteFromDB();
}

//*******************************************************************************
// ФУНКЦИИ ДЛЯ РАБОТЫ С ЗАДАЧАМИ
//Добавить новую задачу
if($_POST['add_todo'])
{
	$todo = new SHAV_Todo();
	$todo->title = htmlspecialchars($_POST['title']);
	$todo->description = htmlspecialchars($_POST['desc']);
	$todo->timeBegin = strtotime($_POST['date_start']);
	$todo->statusId = (int)$_POST['todo_status'];
	$todo->priorityId = (int)$_POST['todo_priority'];
	$todo->deadLine = strtotime($_POST['data_end']);
	$todo->authorId = (int)$_POST['author'];
	$todo->projectId = (int)$_POST['prj_id'];
	$todo->saveToDB();
}

//Изменить задачу
if($_POST['save_todo'] && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);
	$todo->title = htmlspecialchars($_POST['title']);
	$todo->description = htmlspecialchars($_POST['desc']);
	$todo->timeBegin = strtotime($_POST['date_start']);
	$todo->statusId = (int)$_POST['todo_status'];
	$todo->priorityId = (int)$_POST['todo_priority'];
	$todo->deadLine = strtotime($_POST['data_end']);
	$todo->authorId = (int)$_POST['author'];
	$todo->saveToDB();
}

//Изменение статуса задачи если пользователь не админ
if($_POST['save_status_todo'] && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);
	$todo->statusId = (int)$_POST['todo_status'];
	$todo->saveToDB();
}

//Удалить задачу
if($_POST['delete_todo'] && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);
	$todo->deleteFromDB();
}


//*******************************************************************************
// ФУНКЦИИ ДЛЯ РАБОТЫ С КОММЕНТАРИЯМИ

//Добавить комментарий к заданию
if($_POST['add_comment'])
{
	$comment = new SHAV_Comment();
	$comment->text = htmlspecialchars($_POST['text']);
	$comment->authorId = (int)$_POST['author'];
	$comment->addDate = strtotime($_POST['date_start']);
	$comment->todoId = (int)$_POST['todo_id'];
	$comment->saveToDB();
}

//Изменить комментарий к заданию
if($_POST['edit_comment'] && (int)$_POST['comment_id'] > 0)
{
	$comment = new SHAV_Comment((int)$_POST['comment_id']);
	$comment->text = htmlspecialchars($_POST['text']);
	$comment->saveToDB();
}

//Удалить комментарий к заданию
if($_POST['delete_comment'] && (int)$_POST['comment_id'] > 0)
{
	$comment = new SHAV_Comment((int)$_POST['comment_id']);
	$comment->deleteFromDB();
}


//*******************************************************************************
// ФУНКЦИИ ДЛЯ ЗАГРУЗКИ ФАЙЛОВ

if($_POST['add_file'] && (int)$_POST['todo_id'] > 0)
{
	$todo = new SHAV_Todo((int)$_POST['todo_id']);

	$upload_dir = '../uploads/todo_'.$todo->id;

	if(!is_dir($upload_dir))
	{
		mkdir($upload_dir);
	}
	
	if(is_array($_FILES['files']['name']))
	{
		echo 'asd';
		$count = count($_FILES['files']['name']); $i = 0;
		while($i < $count)
		{
			$newFile = new SHAV_TodoFile($upload_dir.'/'.$_FILES['files']['name'][$i]);
			$newFile->objId = $todo->id;
			$todo->files[] = $newFile;
			move_uploaded_file($_FILES['files']['tmp_name'][$i], $upload_dir.'/'.$_FILES['files']['name'][$i]);
			$i++;
		}
	}

	$todo->saveToDB();
}

//*******************************************************************************

header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/traker.php');
?>