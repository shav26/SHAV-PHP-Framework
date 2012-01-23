<?php

include_once("shav_errors_msg.php");		//Системные ошибки и сообщения

/** @class SHAV_DB
 *	@brief Работа с базой данных.
 *	@note Использовать так:
 *	@code
 * $shavDB = new SHAV_DB();
 * //Конфигурируем подключение к БД
 * $shavDB->db_connect("framework", "localhost", "root", "78824982");
 * $results = $shavDB->get_results('SELECT * FROM users');	//Выполняем запрос к БД на получения списка всех пользователей
 * foreach($results as $rec)	//Выводим имена
 * 	echo 'User full name: '.$rec['fio'];
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 10.01.2009
 *	@date Обновленно: 26.02.2011*/
class SHAV_DB
{
	/** Имя базы данных.*/
	public $database = "";
	
	/** Ссылка на сервер, где находится база данных*/
	public $server = "localhost";

	/** Логин пользователя для авторизации на сервере*/
	public $user = "root";

	/** Пароль пользователя для авторизации на сервере*/
	public $pass = "";

	/** Массив всех таблиц базы данных*/
	public $tables = array();

	/** Подключается к базе данных по параметрам.
	 *	@param $database = "" - база с которой нужно работать;
	 *	@param $server = "localhost" - сервер для подключения;
	 *	@param $user = "root" - логин пользоватля;
	 *	@param $pass = "" - пароль пользователя.
	 *	@return bool Статус выполнения подключения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function db_connect($database = "", $server = "localhost", $user = "root", $pass = "")
	{
		$result = true;
		$result = @mysql_connect($server, $user, $pass) or die("<b>".ERROR_DB_0001."</b>".mysql_error());
		mysql_query("SET NAMES utf8");

		if (!$result){
			return false;
		}

		if (!@mysql_select_db($database))
			return false;


		$this->database = $database;
		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;

		$this->getAllTablesFromCurrentDB();

		return $result;
	}

	/** Закрывает соединение с БД.
	 *	@return bool Статус выполнение запроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function db_close()
	{
		$flag = mysql_close();

		return $flag;
	}
//*********************************************************************************************************************************

	/** Выполняем запрос и возврачаем массив.
	 *	@param $query - Строка запроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function get_results($query = "")
	{
		if(empty($query))
			return ERROR_DB_0002;

		if($this->database)
		{
			//Получаем данные из БД и создаем массив
			$results = @mysql_query($query) or die("<br /><b>".ERROR_DB_0001."</b>".mysql_error());
			$mass_res = array();
			while ($row = @mysql_fetch_array($results, MYSQL_BOTH))
			{
				//echo $row.'<br />';
				array_push($mass_res, $row);
			}

			//создаем нормальный массив данных из БД
			$res = array();
			foreach($mass_res as $row)
			{
				$data = array();
				foreach($row as $key => $value)
				{
					if(!is_int($key))
						$data[$key] = $value;
				}
				$res[] = $data;
			}
			return $res;
		}
		return false;
	}

	/** Получаем переменную из базы.
	 *	@param $query - запрос для сервера.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function get_vars($query = "")
	{
		if(empty($query))
			return ERROR_DB_0002;

		$words = explode(' ', $query);
		$type = $words[0];

		if($type != 'SELECT')
			return ERROR_DB_0003;


		if($this->database)
		{
			$results = @mysql_query($query) or die("<br /><b>".ERROR_DB_0001."</b>".mysql_error());
			$mass_res = array();
			while ($row = @mysql_fetch_array($results, MYSQL_BOTH))
				array_push($mass_res, $row);

			foreach($mass_res as $rec)
			{
				foreach($rec as $key => $value)
				{
					return $value;
				}
			}
		}
		return false;
	}

	/** Добавляет данные в БД при этом возвращает id добавленной записи.
	 *	@param $sql - запрос на языке SQL.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function insert_data($sql)
	{
		if(empty($sql))
			return ERROR_DB_0002;

		$str = substr($sql, 0, 6);
		if($str != 'INSERT')
			return ERROR_DB_0004;

		//Получаем название таблицы из запроса.
		$words = explode(' ', $sql);
		$table = $words[2];

		//Выполняем запрос на вставку
		$this->get_results($sql);

		//Получаем id новой записи из таблицы
		$res = mysql_insert_id();

		return $res;
	}

	/** Проводим авторизацию пользователя.
	 *	@param $login - имя пользователя;
	 *	@param $passwodr - пароль пользователя;
	 *	@param $params - Массив параметров вида: array('login_f' => 'login', 'password_f' => 'pass', 'crypt' => 'md5').
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function login($login, $password, $params = array('table' => 'users', 'login_f' => 'login', 'password_f' => 'pass', 'crypt' => 'md5'))
	{
		if($this->database)
		{
			@session_unset();
			@session_destroy();
			@session_start();

			if(!get_magic_quotes_gpc)
			{
				$login = mysql_escape_string($login);
				$password = mysql_escape_string($password);
			}

			$sql = 'SELECT * FROM '.$params['table'].' WHERE '.$params['login_f'].' = "'.$login.'" AND '.$params['password_f'].' = "'.$this->get_crypt($params['crypt'], $password).'"';
			//echo $sql;
			$results = $this->get_results($sql);

			foreach($results as $rec)
			{
				foreach($rec as $key => $value)
					if(is_string($key) && $key != '')
						$_SESSION[$key] = $value;
			}
		}
	}

	/** Проводим авторизацию пользователя
	 *	@param $id_user - Идентификатор пользователя;
	 *	@param $params = array('table' => 'users', 'id' => 'users_id') - массив с параметрами для получения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function relogin($id_user, $params = array('table' => 'users', 'id' => 'users_id'))
	{
		if($this->database)
		{
			@session_unset();
			@session_destroy();
			@session_start();

			$sql = 'SELECT * FROM '.$params['table'].' WHERE '.$params['id'].' = '.$id_user;
			$results = $this->get_results($sql);

			foreach($results as $rec)
			{
				foreach($rec as $key => $value)
					if(is_string($key) && $key != '')
						$_SESSION[$key] = $value;
			}
		}
	}

	/** Осуществляем выход пользователя
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function quit()
	{
		@session_unset();
		@session_destroy();
	}

	/** Получить список всех таблиц в БД
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function get_all_tables()
	{
		if($this->database)
		{
			return $this->tables;
		}else{
			return '<br />'.ERROR_DB_0005;
		}
	}

	/** Проверяет, есть ли таблица с указанным именем в базе данных.
	 *	@param $table - имя таблицы для проверки.
	 *	@return bool статус проверки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	function table_exists($table)
	{
		$flag = false;
		foreach($this->tables as $key=>$value)
		{
			if($key == $table)
			{
				$flag = true;
				break;
			}
		}

		return $flag;
	}

	/** Получаем зашифрованную строку из параметров.
	 *	@param $crypt - метод шифрования;
	 *	@param $str - строка для шифрования.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	private function get_crypt($crypt, $str = "")
	{
		switch($crypt)
		{
			case "md5":
				return md5($str);
				break;
		}
	}

	/** Создает хешь для пароля.
		@param $password - строка, которую следует хешировать;
		@param $type - метод хеширования.*/
	private function cryptPassword($password, $type = 'shav')
	{
		$hash = '';
		switch($type)
		{
			case "shav":
				$double_md5_hash = md5(md5($password)+$type);
				$hash = $double_md5_hash;

			default:
				$hash = $password;
				break;
		}

		return $hash;
	}

	/** Получает все таблицы из текущей базы данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.01.2009
	 *	@date Обновленно: 26.02.2011*/
	private function getAllTablesFromCurrentDB()
	{
		$this->tables = array();
		$sql = 'SHOW TABLES FROM '.$this->database;
		$results = $this->get_results($sql);

		foreach($results as $rec)
		{
			$table_name = $rec['Tables_in_'.$this->database];
			$sql = 'SHOW COLUMNS FROM '.$table_name;
			$res = $this->get_results($sql);
			$fields = array();
			foreach($res as $r)
			{
				$fields[] = $r['Field'];
			}
			$this->tables[$table_name] = $fields;
		}
	}
}
?>
