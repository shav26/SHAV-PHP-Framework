<?php

/** @class SHAV_API_Application
 *	@brief Класс для описания проекта, с которым будет работать SHAV Site API.
 *	Пример использования:
 *	@code
 * $appNew = new SHAV_API_Application();	//Создаем новое приложение, если передать идентификатор существующего, то получим информацию о нем.
 * $appNew->appName = $_POST['title'];
 * $appNew->statusId = (int)$_POST['status'];
 * $appNew->userId = (int)$_POST['userId'];
 * $appNew->typeId = (int)$_POST['type'];
 * $appNew->saveToDB(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 20.02.2011
 *	@date Обновленно: */
class SHAV_API_Application extends SHAV_Object
{
	/** Идентификатор программы или проекта.*/
	public $appId = 0;

	/** Название программы или проекта.*/
	public $appName = '';

	/** Идентификатор пользователя который зарегистрировалл программу или проект*/
	public $userId = 0;
	
	/** Полное имя пользователя*/
	public $userFullName = '';
	
	/** Уникальный токен для работы с API*/
	public $token = '';
	
	/** Дата регистрации*/
	public $pubDate = 0;
	
	/** Идентификатор типа программы*/
	public $typeId = 0;
	
	/** Названия типа пограммы*/
	public $typeName = '';
	
	/** Идентификатор статуса доступа к API*/
	public $statusId = 0;
	
	/** Название статуса*/
	public $statusName = '';

	/** Конструктор класса.
	 *	@param $id - идентифкатор приложения. Если не указан то будет созданно пустое приложения, если указанно, то из базы данных будет собранна информация по данному приложению.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 20.02.2011
	 *	@date Обновленно: */
	function SHAV_API_Application($id = 0)
	{
		if((int)$id > 0)
			$this->createAppById($id);
	}

	/** Сбор данных из базы данных.
	 *	@param $id - идентификатор приложения, информацию о котором следует получить.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 20.02.2011
	 *	@date Обновленно: */
	function createAppById($id)
	{
		global $shavDB;

		$sql = 'SELECT * FROM api_apps WHERE app_id = '.(int)$id;
		$results = $shavDB->get_results($sql);

		foreach($results as $app)
		{
			$this->appId = (int)$app['app_id'];
			$this->appName = $app['app_name'];
			$this->userId = (int)$app['user_id'];
			$this->userFullName = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.(int)$this->userId);
			$this->token = $app['token'];
			$this->pubDate = (int)$app['pub_date'];
			$this->typeId = (int)$app['app_type'];
			$this->typeName = $shavDB->get_vars('SELECT app_type_name FROM api_app_types WHERE app_type_id = '.$this->typeId);
			$this->statusId = (int)$app['app_status'];
			$this->statusName = $shavDB->get_vars('SELECT bans_status_name FROM bans_status WHERE bans_status_id = '.(int)$this->statusId);
		}
	}

	/** Сохраняет данные в базу данных.
	 *	@param $send_mail - отправлять или нет оповещение на email пользователя, на которого регистрируется приложение.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 20.02.2011
	 *	@date Обновленно: */
	function saveToDB($send_mail = false)
	{
		global $shavDB;

		if((int)$this->appId <= 0)	//Создаем новый
		{
			$this->pubDate = time();
			if($this->userId <= 0)
				$this->userId = $_SESSION['users_id'];
			$this->token = $this->generateToken();

			if($send_mail == true)
			{
				$user_mail = $shavDB->get_vars('SELECT email FROM users WHERE users_id = '.(int)$this->userId);
				$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<title>Ваш токен</title>
					</head>
					<body>
					На Ваш аккаунт было зарегистрированно приложение '.$this->appName.'. Токен для связи с сайтом <a href="http://'.$_SERVER['HTTP_HOST'].'">'.$_SERVER['HTTP_HOST'].'</a>: '.$this->token.'. Спасибо за то что выбрали наши услуги.
					</body>
					</html>';
				shav_send_mail($_SERVER['HTTP_HOST'], $user_mail, $msg, 'Ваш токен.');
			}
			
			$sql = 'INSERT INTO api_apps SET app_name = "'.$this->appName.'", token = "'.$this->token.'", user_id = '.(int)$this->userId.', pub_date = '.$this->pubDate.', app_type = '.(int)$this->typeId.', app_status = '.(int)$this->statusId;
			$this->appId = $shavDB->insert_data($sql);
		}
		elseif((int)$this->appId > 0)	//Обновляем существующий
		{
			$sql = 'UPDATE api_apps SET app_name = "'.$this->appName.'", token = "'.$this->token.'", user_id = '.(int)$this->userId.', app_type = '.(int)$this->typeId.', app_status = '.(int)$this->statusId.' WHERE app_id = '.(int)$this->appId;
			$shavDB->get_results($sql);
		}
	}

	/** Удаляет данные из быза данных.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 20.02.2011
	*	@date Обновленно: */
	function deleteFromDB()
	{
		global $shavDB;

		$sql = 'DELETE FROM api_apps WHERE app_id = '.(int)$this->appId;
		$shavDB->get_results($sql);
	}
	
	/** Функция генерирует токен для приложения.
	 *	@return новый токен.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 20.02.2011
	 *	@date Обновленно: */
	private function generateToken()
	{
		$newToken = md5($this->appName.'_'.$this->userId.'_'.$this->typeId.'_'.$this->pubDate);

		return $newToken;
	}
}


/** @class SHAV_API_Query
 *	@brief Класс запроса.
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 26.02.2011
 *	@date Обновленно: */
class SHAV_API_Query extends SHAV_Object
{
	/** Идентифкатор операции.*/
	public $action = '';

	/** Название таблицы в базе данных с которой будет работать запрос.*/
	public $table = '';

	/** Токен для доступа к данным.*/
	public $token = '';

	/** Данные для работы с таблицой.*/
	public $datas = array();

	/** Конструктор сласса
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 26.02.2011
	*	@date Обновленно: */
	function SHAV_API_Query($array = array())
	{
		$this->token = $array['token'];
		$this->action = $array['action'];
		$this->table = $array['table'];
		$this->datas = array();
		foreach($array as $key=>$value)
		{
			if($key != 'action' && $key != 'table' && $key != 'token' && $key != 'where')
				$this->datas[$key] = $value;
			
			elseif($key == 'where')
				$this->datas[$key] = str_replace('$', ' AND ', str_replace('|', ' OR ', $value));
		}
	}
}


/** @class SHAV_API_Results
 *	@brief Класс для хранения результатов выполнения запросов.
 *	Пример использования:
 *	@code
 * $res = new SHAV_API_Results();
 * $res->code = 0;
 * $res->message = 'Приложения с таким токеном не существует. Проверте правильность ввода токена и повторите попытку.';
 * $res->drawCurrentData(true);	//Выводит JSON строку
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 19.02.2011
 *	@date Обновленно: */
class SHAV_API_Results extends SHAV_Object
{
	/** Массив данных, которые будут преобразованны в структуру для передачи.*/
	public $datas = '';

	/** Сообщение об выполнении.*/
	public $message = '';

	/** Код выполнения.*/
	public $code = 0;

	/** Коструктор класса
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 19.02.2011
	*	@date Обновленно: */
	function SHAV_ResultsAPI()
	{}
	
	/** Создает JSON из SQL-запроса.
	 *	@param $sql - Запрос SQL.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 19.02.2011
	 *	@date Обновленно: */
	function createJSONFromSQL($sql)
	{
		global $shavDB;
		
		if($sql != '')
		{
			$results = $shavDB->get_results($sql);

			if(count($results) > 0)
			{
				$this->datas = $results;
				$this->code = 1;
				$this->message = 'Запрос выполнен успешно.';
			}
			elseif(count($results) <= 0)
			{
				$this->datas = array();
				$this->code = 1;
				$this->message = 'Нет данных';
			}
		}
		else
		{
			$this->code = 0;
			$this->message = 'Не задан SQL-запрос.';
		}
	}
	
	/** Создает JSON из массива данных.
	 *	@param $array - массив жданных.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 19.02.2011
	*	@date Обновленно: */
	function createJSONFromArray($array)
	{
		if(sizeof($array) > 0)
		{
			$this->datas = $array;
			$this->code = 1;
			$this->message = 'Запрос выполнен успешно.';
		}
		else
		{
			$this->code = 0;
			$this->message = 'Не зананны данные.';
		}
	}
	
	/** Создает JSON из объекта.
	 *	@param $object - объект данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 19.02.2011
	 *	@date Обновленно: */
	function createJSONFromObject($object)
	{
		$this->datas = $object;
		$this->code = 1;
		$this->message = 'Запрос выполнен успешно.';
	}
	
	/** Создание JSON из сообщения и кода.
	 *	@param $code - код сообщения;
	 *	@param $msg - текст сообщения;
	 *	@param $datas - данные, которые могут быть как массив так и объект.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 19.02.2011
	 *	@date Обновленно: */
	function createJSONFromMessage($code, $msg, $datas = '')
	{
		$this->datas = $datas;
		$this->code = $code;
		$this->message = $msg;
	}
	
	/** Выводит текущее состояние класса.
	 *	@param $isDraw - выводить ил нет html-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 19.02.2011
	 *	@date Обновленно: */
	function drawCurrentData($isDraw = false)
	{
		if($isDraw == true)
			echo $this->createJSON();
		else
			return $this->createJSON();
	}
	
	/** Создает JSON для текущего класса.
	 *	@param $object - объект, который нужно преобразовать.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 19.02.2011
	 *	@date Обновленно: */
	private function createJSON($object = '')
	{
		if($object == '')
			return shav_jsonFixCyr(shav_convertArrayToJSON($this));
		else
			return shav_jsonFixCyr(shav_convertArrayToJSON($object));
	}
}


/** @class SHAV_API
 *	@brief API для работы с внешними программами. Позволяет обмениваться данными.
 *	@note Для использования необходимо использовать специальный запрос. Парметры запроса следует передавать методом POST, хотя это никак не ограничивается, Вы можете использовать и GET. Тогда в скрипте обработки необходимо использовать такой код:
 *	@code
 * $shavAPI = new SHAV_API();
 * //Код лучше не использовать. Мы рекомендуем использовать только POST.
 * if(empty($_POST) && !empty($_GET))
 *	$ _POST = $_GET;
 *
 * if($shavAPI->existsApplicatioWithToken($_POST['token']) == true)
 * {
 *	$data = new SHAV_API_Query($_POST);
 *	$shavAPI->doRequestToDB($data);
 *	$shavAPI->results->drawCurrentData(true);
 * }
 *	@endcode
 *	Ссылка выглядит так: http://you_company.com/api/site_api.php?token=dc6f35454b48080072840f03fceec3a0&action=login&table=users&where=login=admin$pass=admin
 *	<ul><li>token - идентификатор приложения для синхронизации с сервером;</li>
 *	<li>action - действия которые необходимо выполнить, может принимать такие значения (get_data, add, delete, update);</li>
 *	<li>table - имя таблицы с которой будет работать запрос (может быть любай таблица базы данных);</li>
 *	<li>where - усновия для получения, обновления или удаления данных;</li>
 *	</ul> Все остальные параметры - это те данные которые необходимо получить. В данный момент это просто поля таблицы, с которой будет производится работа.
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 15.11.2010
 *	@date Обновленно: 26.02.2011*/
class SHAV_API extends SHAV_Object
{
	/** Приложение с которым будет работать API.*/
	public $app = '';
	
	/** Результат выполнения запроса*/
	public $results = '';
	
	/** Массив всех доступных скриптов работы с API.*/
	public $scriptsList = array();

	/** Конструктор класса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	function SHAV_API()
	{
		if(empty($this->scriptsList))
			$this->getAllScripts('../api/');
	}

	/** Получаем приложения по его уникальному токену.
	 *	@param $token - токен приложения который следует проверить.
	 *	@return SHAV_API_Application приложение с токеном если онго существует, иначе NULL.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	function getApplicationByToken($token)
	{
		global $shavDB;

		if($token != '')
		{
			$sql = 'SELECT app_id FROM api_apps WHERE token = "'.$token.'"';
			$id = $shavDB->get_vars($sql);

			if((int)$id > 0)
			{
				$app = new SHAV_API_Application($id);
				return $app;
			}

			return NULL;
		}

		return NULL;
	}

	/** Проверяет существует ли такое приложение с токеном.
	 *	@param $token - идентиифкатор приложения для проверки.
	 *	@return bool статус проверки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	function existsApplicatioWithToken($token)
	{
		global $shavDB;
		
		if($token != '')
		{
			$sql = 'SELECT app_id FROM api_apps WHERE token = "'.$token.'"';
			$id = $shavDB->get_vars($sql);

			if((int)$id > 0)
				return true;
			else
				return false;
		}

		return false;
	}

	/** Выполнить запрос и получить результат на основании данных из класса SHAV_QueryAPI.
	 *	@param $data - объект, который описывает запрос к БД.
	 *	@return bool статус об окончание работы запроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	function doRequestToDB($data)
	{
		global $shavDB, $sapi_tables;
		
		if(!is_object($data))
		{
			$content = '<b class="error">ОШИБКА: Не правильно задан запрос. Запрос должен быть объектом класса SHAV_QueryParamAPI.</b>';
			return false;
		}
		
		$flag = false;
		$this->app = $this->getApplicationByToken($data->token);
		if($this->app->statusId == 2 || $this->app->statusId == 3)
		{
			$this->results = new SHAV_API_Results();
			$this->results->code = 0;
			$this->results->message = 'Ваше приложение '.$this->app->ststusName.'. Обратитесь к администратору сайта.';
			$this->results->datas = array();
			
			return false;
		}

		switch($data->action)
		{
			case 'login':
				$param = '';
				$where = ' WHERE ';
				foreach($data->datas as $key=>$value)
				{
					if($key == 'pass')
						$value = md5($value);
					
					if(is_string($value))
						$where .= $key.' = "'.$value.'" AND ';
					else
						$where .= $key.' = '.$value.' AND ';
				}

				$where = substr($where, 0, strlen($where)-5);
				
				$sql = 'SELECT * FROM users'.$where;
				$this->results = new SHAV_API_Results();
				$this->results->createJSONFromSQL($sql);
				$flag = true;
				break;
			case 'get_data':	//Получаем данные из БД
				$param = '';
				$where = '';
				foreach($data->datas as $key=>$value)
				{
					if($value == '')
						$param .= $key.', ';
					
					else if($key == 'where')
					{
						$where .= ' WHERE '.$value;
					}
				}

				if($param == '')
					$param = '*';
				else
					$param = substr($param, 0, strlen($param)-2);

				$sql = 'SELECT '.$param.' FROM '.$data->table.$where;
				$this->results = new SHAV_API_Results();
				$this->results->createJSONFromSQL($sql);
				$flag = true;
				break;
				
			case 'add':	//Добавляем данные в БД
				$param = '';

				if(!empty($data->datas))
				{
					foreach($data->datas as $key=>$value)
					{
						if($value != '')
						{
							if((int)$value > 0)
								$param .= $key.' = '.$value.', ';
							else
								$param .= $key.' = "'.htmlspecialchars($value).'", ';
						}
					}
					$param = substr($param, 0, strlen($param)-2);
					
					$sql = 'INSERT INTO '.$data->table.' SET '.$param;
					$id = $shavDB->insert_data($sql);
					if((int)$id > 0)
					{
						$this->results = new SHAV_API_Results();
						$this->results->code = 1;
						$this->results->message = 'Данные успешно добавленны.';
						$this->results->datas = array();
						$flag = true;
					}
					else
					{
						$this->results = new SHAV_API_Results();
						$this->results->code = 0;
						$this->results->message = 'Ошибка при добавлении данных в БД.';
						$this->results->datas = array();
						$flag = false;
					}
				}
				else
				{
					$this->results = new SHAV_API_Results();
					$this->results->code = 0;
					$this->results->message = 'Вы не указали параметры, которые следует добавить в БД.';
					$this->results->datas = array();
					$flag = false;
				}
				break;
				
			case 'update':	//Обновляем данные в БД
				$param = '';
				$where = '';
				foreach($data->datas as $key=>$value)
				{
					if($key != 'where')
					{
						if($value != '')
						{
							if((int)$value > 0)
								$param .= $key.' = '.$value.', ';
							else
								$param .= $key.' = "'.htmlspecialchars($value).'", ';
						}
					}
					elseif($key == 'where')
						$where .= ' WHERE '.$value;
				}
				$param = substr($param, 0, strlen($param)-2);
				$sql = 'UPDATE '.$data->table.' SET '.$param.$where;
				$shavDB->get_results($sql);
				
				$this->results = new SHAV_API_Results();
				$this->results->code = 1;
				$this->results->message = 'Данные успешно изменены.';
				$this->results->datas = array();
				$flag = true;
				break;
				
			case 'delete':	//удаляем данные из БД
				$param = '';
				if(!empty($data->datas))
				{
					foreach($data->datas as $key=>$value)
					{
						if($key == 'where')
						{
							$param = ' WHERE '.$value;
						}
					}

					$sql = 'DELETE FROM '.$data->table.$param;
					$shavDB->get_results($sql);
					
					$this->results = new SHAV_API_Results();
					$this->results->code = 1;
					$this->results->message = 'Данные успешно удаленны.';
					$this->results->datas = array();
					$flag = true;
				}
				else
				{
					$this->results = new SHAV_API_Results();
					$this->results->code = 0;
					$this->results->message = 'Вы не указали параметры условия, по которому будут удалятся данные.';
					$this->results->datas = array();
					$flag = false;
				}
				break;
				
			default:
				$content = '<b class="error">ОШИБКА: Не правильно задан тип операции.</b>';
				$flag = false;
				break;
		}

		return $flag;
	}

	/** Выводит интерфейс для работы с Site API.
	 *	@param $isDraw - Выводить или нет HTML-код.
	 *	@return HTML-код интерфейса для редактирования.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	function drawInterfaceEditor($isDraw = false)
	{
		global $shavDB;

		if(empty($_SESSION) || (int)$_SESSION['users_id'] != 1)
		{
			$error = '<b class="error">ОШИБКА: У Вас нет прав для просмотра данного раздела. Обратитесь к администратору.</b>';
			if($isDraw == true)
				echo $error;
			else
				return $error;
		}
		
		$index = 0;

		if($_POST['add'] && $_POST['title'] != '' && (int)$_POST['type'] > 0 && (int)$_POST['status'] > 0)
		{
			$appNew = new SHAV_API_Application();
			$appNew->appName = $_POST['title'];
			$appNew->statusId = (int)$_POST['status'];
			$appNew->userId = (int)$_POST['userId'];
			$appNew->typeId = (int)$_POST['type'];
			$appNew->saveToDB(true);

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/api_editor.php');
		}
		elseif($_POST['save'] && $_POST['title'] != '' && (int)$_POST['type'] > 0 && (int)$_POST['status'] > 0 && (int)$_POST['appId'] > 0)
		{
			$appNew = new SHAV_API_Application((int)$_POST['appId']);
			$appNew->appName = $_POST['title'];
			$appNew->statusId = (int)$_POST['status'];
			$appNew->userId = (int)$_POST['userId'];
			$appNew->typeId = (int)$_POST['type'];
			$appNew->saveToDB();
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/api_editor.php');
		}
		elseif($_POST['select'] && (int)$_POST['script_id'] > 0)
		{
			$index = (int)$_POST['script_id'];
		}
		elseif($_POST['add_script'] && $_POST['url'] != '' && $_POST['text'] != '')
		{
			$url = $_POST['url'];
			file_put_contents($url, $_POST['text']);
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/api_editor.php');
		}
		elseif($_POST['save_script'] && $_POST['url'] != '' && $_POST['text'] != '')
		{
			$url = $_POST['url'];
			file_put_contents($url, $_POST['text']);

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/api_editor.php');
		}

		if($_GET['action'] == 'delete' && (int)$_GET['appId'] > 0)
		{
			$app = new SHAV_API_Application((int)$_GET['appId']);
			$app->deleteFromDB();

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/api_editor.php');
		}

		if($_SESSION['isadmin'] == 1)
		{
			$url = '';
			$text = '';
			if((int)$index > 0)
				$url = $this->scriptsList[($index-1)];
			
			$sql = 'SELECT app_id FROM api_apps';
			$editScript = '<div><form method="POST" action="">'.$this->createListOfAllScripts($index).'&nbsp;'.$this->createWindowForEditAPIScript($url).'</form></div>';
			
		}else if($_SESSION['isadmin'] > 1)
		{
			$sql = 'SELECT app_id FROM api_apps WHERE user_id = '.(int)$_SESSION['users_id'];
			$editScript = '';
		}
		$results = $shavDB->get_results($sql);

		$allApps = array();
		foreach($results as $res)
		{
			$app = new SHAV_API_Application($res['app_id']);
			$allApps[] = $app;
		}

		$content  = '<div class="api_apps_list"><div style="width:100px;float:right;">'.$this->drawInterfaceForEditApp(0).'</div><table width="100%">';
		$content .= '<tr><th>Название</th><th width="200px">Токен</th><th width="150px">Дата публикации</th><th width="100px">Статус</th><th width="100px">Автор</th><th width="100px"></th></tr>';
		foreach($allApps as $app)
		{
			$style = ' style="background-color:#00FF00;"';
			if($app->statusId == 2)
				$style = ' style="background-color:#FF0000;"';
			elseif($app->statusId == 3)
				$style = ' style="background-color:#FFFF00;"';
			$content .= '<tr'.$style.'><td>'.$app->appName.'</td><td align="center">'.$app->token.'</td><td>'.date('d.m.Y H:i:s', $app->pubDate).'</td><td>'.$app->statusName.'</td><td>'.$app->userFullName.'</td><td>'.$this->drawInterfaceForEditApp($app->appId).'&nbsp;|&nbsp;<a href="?action=delete&appId='.$app->appId.'">Удалить</a></td></tr>';
		}
		$content .= '</table><div class="info_api">'.$this->drawInformation().'</div>'.$editScript.'</div>';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Выводит HTML-код формы редактирования или создания нового приложения.
	 *	@param $appId - идентифкатор существующего приложения;
	 *	@param $isDraw - выводить или нет HTML-код компонента.
	 *	@return HTML-код вормы редактирования.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function drawInterfaceForEditApp($appId = 0)
	{
		if((int)$appId > 0)
		{
			$this->app = new SHAV_API_Application($appId);
			$appId = array('name'=>'appId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>(int)$appId);
			$btn = array('name'=>'save', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить');
		}
		else
		{
			$btn = array('name'=>'add', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить');
			$appId = array('name'=>'appId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>0);
		}
		
		$typeList = new SHAV_DropList();
		$typeList->createListFromDBTable(array('name'=>'api_app_types', 'id_field'=>'app_type_id', 'name_field'=>'app_type_name'), 'type', $this->app->typeId);
		$type = $typeList->drawList();
		
		$bans_status = new SHAV_DropList();
		$bans_status->createListFromDBTable(array('name'=>'bans_status', 'id_field'=>'bans_status_id', 'name_field'=>'bans_status_name'), 'status', $this->app->statusId);
		$status = $bans_status->drawList();

		$usersList = new SHAV_DropList();
		$usersList->createListFromDBTable(array('name'=>'users', 'id_field'=>'users_id', 'name_field'=>'fio'), 'userId', (int)$_SESSION['users_id']);
		$users = $usersList->drawList();
		
		if((int)$_SESSION['isadmin'] == 1)
		{
			$statusList = array('name'=>'status', 'label_align'=>'left', 'label'=>'Status:', 'type'=>'list', 'value'=>$status);
			$userList = array('name'=>'userId', 'label_align'=>'left', 'label'=>'Пользователь:', 'type'=>'list', 'value'=>$users);
		}
		elseif((int)$_SESSION['isadmin'] > 1)
		{
			$statusList = array('name'=>'status', 'label_align'=>'left', 'label'=>'Status:', 'type'=>'hidden', 'value'=>'3');
			$userList = array('name'=>'userId', 'label_align'=>'left', 'label'=>'Пользователь:', 'type'=>'hidden', 'value'=>(int)$_SESSION['users_id']);
		}
			
			
		$recs = array(	array('name'=>'title', 'label_align'=>'left', 'label'=>'Название проекта:', 'type'=>'text', 'size'=>'25', 'value'=>$this->app->appName),
						array('name'=>'type', 'label_align'=>'left', 'label'=>'Type:', 'type'=>'list', 'value'=>$type),
						$statusList, $userList, $appId, $btn);
		
		$params = array('method'=>'POST', 'action_scrp'=>'', 'enctype'=>'', 'style_class'=>'addApp', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		
		
		$wnd = new SHAV_jqModal();
		if((int)$appId > 0)
		{
			$wnd->windowId = 'editApp_'.(int)$this->app->appId;
			$wnd->title = 'Редактирование приложения.';
			$wnd->linkName = 'Изменить';
		}
		else
		{
			$wnd->windowId = 'addApp';
			$wnd->title = 'Создать новое приложение.';
			$wnd->linkName = 'Создать';
		}
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $form->drawForm();
		$wnd->linkId = $wnd->windowId.'Trigger';
		
		return $wnd->drawModalWindow();
	}

	/** Выводить HTML-код помощи при работе с Site API.
	 *	@return HTML-код помощи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function drawInformation()
	{
		$content = '<div style="font:12px Arial;"><p>Пример ссылки для работы с Site API: <i>http://<your_domain>/api/site_api.php?<b>token</b>=<token_your_app>&<b>action</b>=<some_action>&<b>table</b>=<some_table>&<params>[&<b>where</b>=<params>]</i></p>
		Параметры запроса:<ul>
		<li><b>token</b> - идентификатор приложения для синхронизации с сервером;</li>
		<li><b>action</b> - действия которые необходимо выполнить, может принимать такие значения (login, get_data, add, delete, update);</li>
		<li><b>table</b> - имя таблицы с которой будет работать запрос (может быть любай таблица базы данных);</li>
		<li><b>where</b> - усновия для получения, обновления или удаления данных;</li>
		<li><b>Все остальные параметры</b> - это те данные которые необходимо получить. В данный момент это просто поля таблицы, с которой будет производится работа.</li></ul><p>http://<your_domain>/api/site_api.php?token=<token_your_app>&action=login&login=<логин>&pass=<пароль></p></div>';

		return $content;
	}

	/** Редактирование содержимого скрипта обработки запросов.
	 *	@param $url - ссылка на скрипт.
	 *	@return HTML-код окна для редактирования данных скрипта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function createWindowForEditAPIScript($url = '')
	{
		$text = '';
		$btn = array('name'=>'add_script', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить');

		if($url != '')
		{
			$btn = array('name'=>'save_script', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить');
			if(file_exists($url))
				$text = file_get_contents($url);
			else
				$text = '<b class="error">Ошибка! Файла '.$url.' не существует.</b>';
		}
		else
			$text = $this->defaultScriptData();

		$recs = array(	array('name'=>'url', 'label_align'=>'left', 'label'=>'Скрипт:', 'type'=>'text', 'size'=>'25', 'value'=>$url),
			array('name'=>'text', 'label_align'=>'left', 'label'=>'Содержимое:', 'type'=>'textarea', 'size'=>array('cols'=>'70', 'rows'=>'15'), 'value'=>$text),
			$btn);

		$params = array('method'=>'POST', 'action_scrp'=>'', 'enctype'=>'', 'style_class'=>'saveScript', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		
		$wnd = new SHAV_jqModal();
		if($url != '')
		{
			$wnd->windowId = 'editScript_'.(int)$this->app->appId;
			$wnd->title = 'Редактирование скрипт.';
			$wnd->linkName = 'Редактировать';
		}
		else
		{
			$wnd->windowId = 'addScript';
			$wnd->title = 'Создать новый скрипт.';
			$wnd->linkName = 'Создать';
		}
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $form->drawForm();
		$wnd->linkId = $wnd->windowId.'Trigger';
		
		return $wnd->drawModalWindow();
	}

	/** Создание выпадающего списка скриптов.
	 *	@param $index - идентификато скрипта в массив всех скриптов.
	 *	@return Возвращает HTML-код списка скриптов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function createListOfAllScripts($index = 0)
	{
		$content = '<select id="script_id" name="script_id"><option value="0">Не указан</option>';
		$i = 1;
		foreach($this->scriptsList as $url)
		{
			if($index == $i)
				$content .= '<option value="'.$i.'" selected>'.$url.'</option>';
			else
				$content .= '<option value="'.$i.'">'.$url.'</option>';

			$i++;
		}
		$content .= '</select><input type="submit" name="select" id="select" value="Выбрать" />';

		return $content;
	}

	/** Получения всех файлов в папке скриптов.
	 *	@param $folder - ссылка на папку со скриптами.
	 *	@return массив всех файлов из папки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function getAllScripts($folder)
	{
		$allFiles = shav_GetAllFilesFromFolder($folder, true);
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		
		$this->scriptsList = array();
		foreach($allFiles as $file)
		{
			$typeOfFile = finfo_file($finfo, $file);
			if($typeOfFile == 'text/x-php')
			{
				$this->scriptsList[] = $file;
			}
		}
		
		finfo_close($finfo);
	}

	/** Создает содержимое скрипта поумолчанию (заготовку).
	 *	@return PHP-код зоготовки, который можно использовать для работы.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function defaultScriptData()
	{
		$content = '<?php
		
	include_once(\'../inc/shav_config.php\');
	
	$shavAPI = new SHAV_API();

	//Код для проверки запросов в окне браузера. После окончания разработки данный код нужно удалить или закоментировать.
	/*if(empty($_POST) && !empty($_GET))
		$_POST = $_GET;*/

	//Код проверки данных запроса.
	if($_POST[\'token\'] != \'\')
	{
		$id = $shavDB->get_vars(\'SELECT app_id FROM api_apps WHERE token = "\'.$_POST[\'token\'].\'"\');
		
		if((int)$id > 0)	//Если все впорядке выполняем требуеммый запрос.
		{
			$data = new SHAV_API_Query($_POST);
			$shavAPI->doRequestToDB($data);
			$shavAPI->results->drawCurrentData(true);
		}
		else	//Ошибка если приложения с таким токеном не существует в базе.
		{
			$res = new SHAV_API_Results();
			$res->code = 0;
			$res->message = \'Приложения с таким токеном не существует. Проверте правильность ввода токена и повторите попытку.\';
			$res->drawCurrentData(true);
		}
	}
	else	//Ошибка если код указан не верно!
	{
		$res = new SHAV_API_Results();
		$res->code = 0;
		$res->message = \'Приложения с таким токеном не существует. Проверте правильность ввода токена и повторите попытку.\';
		$res->drawCurrentData(true);
	}
	
	$shavDB->db_close();
?>';

		return $content;
	}

	/** Проверяет доступ к таблице.
	 *	@return bool статус проверки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 26.02.2011*/
	private function checkTable($table)
	{
		global $sapi_tables;

		$flag = false;
		foreach($sapi_tables as $s_table)
		{
			if($table == $s_table)
			{
				$flag = true;
				break;
			}
		}

		return $flag;
	}
}
?>