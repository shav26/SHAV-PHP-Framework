<?php
/** @class SHAV_Todo
 *	@brief Класс описывающий задание для проекта.
 *	Пример использования:
 *	@code
 * $id = 157; //Идентификатор задачи
 * $todo = new SHAV_Todo($id);
 * $todo->saveToDB();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 12.06.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Todo extends SHAV_Object
{
	/** Идентификатор задания.*/
	public $id = 0;
	
	/** Заголовок задания.*/
	public $title = '';
	
	/** Описание задания.*/
	public $description = '';
	
	/** Время создания (начала) задания.*/
	public $timeBegin = 0;
	
	/** Время окончания задания.*/
	public $deadLine = 0;
	
	/** Время добавления задачи в БД.*/
	public $createDate = 0;
	
	/** Идентификатор автора.*/
	public $authorId = 0;
	
	/** Полное имя автора.*/
	public $fioAuthor = '';
	
	/** Идентификатор проект к которому привязанно задание.*/
	public $projectId = 0;
	
	/** Идентификатор приоритета.*/
	public $priorityId = 0;
	
	/** Название приоритета.*/
	public $priorityTitle = '';
	
	/** Код статуса задания.*/
	public $ststusId = 0;
	
	/** Название статуса задания (Начато, завершенно, новое).*/
	public $statusTitle = '';
	
	/** Список коментариев.*/
	public $comments = array();
	
	/** Прикрепленные файлы*/
	public $files = array();

	/** Конструктор класса.
	 *	@param $id - идентификатор задачи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Todo($id = 0)
	{
		if((int)$id <= 0) return;

		$this->createFromDBByID($id);
	}

	/** Берет информацию из БД по идентификатору задания.
	 *	@param $id - идентификатор задачи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromDBByID($id)
	{
		global $shavDB;

		$sql = 'SELECT * FROM tracker_todos WHERE todo_id = '.(int)$id;
		$results = $shavDB->get_results($sql);

		foreach($results as $rec)
		{
			$this->id = $rec['todo_id'];
			$this->title = htmlspecialchars_decode($rec['title']);
			$this->description = htmlspecialchars_decode($rec['todo_desc']);
			$this->timeBegin = $rec['time_start'];
			$this->deadLine = $rec['time_end'];
			$this->createDate = $rec['date_add'];
			$this->authorId = $rec['author_id'];
			$this->statusId = $rec['status_id'];
			$this->statusTitle = $shavDB->get_vars('SELECT todo_status_name FROM tracker_todo_status WHERE todo_status_id = '.$this->statusId);
			$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
			$this->projectId = $rec['prj_id'];
			$this->priorityId = $rec['priority_id'];
			$this->priorityTitle = $shavDB->get_vars('SELECT todo_priority_name FROM tracker_todo_priority WHERE todo_priority_id = '.$this->priorityId);

			$this->files = array();
			if($rec['resourses'] != '')
				$this->files = $this->getAllFilesByStr($rec['resourses']);

			$this->comments = array();

			$res = $shavDB->get_results('SELECT comment_id FROM tracker_todo_comments WHERE todo_id = '.(int)$this->id);
			foreach($res as $r)
			{
				$comment = new SHAV_Comment();
				$comment->createFromDBByID($r['comment_id']);
				$this->comments[] = $comment;
			}
		}
	}

	/** Создает объект из массива параметров.
	 *	@param $res - массив параметров.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromArray($res)
	{
		if(empty($res))
		{
			echo MSG_ARRAYS_0002;
			return;
		}

		$this->id = $res['todo_id'];
		$this->title = $res['title'];
		$this->description = htmlspecialchars($res['todo_desc']);
		$this->timeBegin = $res['time_start'];
		$this->deadLine = $res['time_end'];
		$this->authorId = $res['author_id'];
		$this->createDate = $res['date_add'];
		$this->statusId = $res['statusId'];
		$this->statusTitle = $shavDB->get_vars('SELECT todo_status_name FROM tracker_todo_status WHERE todo_status_id = '.$this->statusId);
		$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
		$this->projectId = $res['prj_id'];
		$this->comments = $res['comments'];
		$this->files = $res['files'];
		$this->priorityId = $res['priorityId'];
		$this->priorityTitle = $shavDB->get_vars('SELECT todo_priority_name FROM tracker_todo_priority WHERE todo_priority_id = '.$this->priorityId);
	}

	/** Сохраняет текущее задание в БД.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function saveToDB()
	{
		global $shavDB;

		$resources = '';
		$k = 1; $j = 1;
		foreach($this->files as $file)
		{
			if(shav_getOptionFileType($file->fType) == '$image')
			{
				$resources .= shav_getOptionFileType($file->fType).$k.'$ '.$file->url.',';
				$k++;
			}
			elseif(shav_getOptionFileType($file->fType) == '$file')
			{
				$resources .= shav_getOptionFileType($file->fType).$j.'$ '.$file->url.',';
				$j++;
			}
		}
		$resources = substr($resources, 0, strlen($resources) - 1);

		if((int)$this->id <= 0)
		{
			$sql = 'INSERT INTO tracker_todos SET prj_id = '.$this->projectId.', title = "'.htmlspecialchars($this->title).'", todo_desc = "'.htmlspecialchars($this->description).'", resourses = "'.$resources.'", date_add = '.time().', time_start = '.$this->timeBegin.', time_end = '.$this->deadLine.', author_id = '.$this->authorId.', status_id = '.$this->statusId.', priority_id = '.$this->priorityId;
			$this->id = $shavDB->insert_data($sql);

			foreach($this->comments as $comment)
			{
				if(is_object($comment))
					$comment->saveToDB();
			}
		}
		else
		{
			$sql = 'UPDATE tracker_todos SET prj_id = '.$this->projectId.', title = "'.htmlspecialchars($this->title).'", todo_desc = "'.htmlspecialchars($this->description).'", resourses = "'.$resources.'", time_start = '.$this->timeBegin.', time_end = '.$this->deadLine.', author_id = '.$this->authorId.', status_id = '.$this->statusId.', priority_id = '.$this->priorityId.' WHERE todo_id = '.(int)$this->id;
			$shavDB->get_results($sql);

			foreach($this->comments as $comment)
			{
				if(is_object($comment))
					$comment->saveToDB();
			}
		}
	}

	/** Удалить из БД.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function deleteFromDB()
	{
		global $shavDB;

		$sql = 'DELETE FROM tracker_todos WHERE todo_id = '.(int)$this->id;
		$shavDB->get_results($sql);

		foreach($this->comments as $comment)
		{
			if(is_object($comment))
				$comment->deleteFromDB();
		}

		foreach($this->files as $file)
		{
			if(is_object($file))
				$file->deleteFile();
		}
	}

	/** Конвертирует обект в массив.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToArray()
	{
		$res = array();
		$res['todo_id'] = $this->id;
		$res['title'] = $this->title;
		$res['todo_desc'] = $this->description;
		$res['time_start'] = $this->timeBegin;
		$res['time_end'] = $this->deadLine;
		$res['author_id'] = $this->authorId;
		$res['date_add'] = $this->createDate;
		$res['statusId'] = $this->statusId;
		$res['fio'] = $this->fioAuthor;
		$res['prj_id'] = $this->projectId;
		$res['files'] = $this->files;
		$res['comments'] = $this->comments;
		$res['priorityId'] = $this->priorityId;
		$res['statusTitle'] = $this->statusTitle;
		$res['priorityTitle'] = $this->statusTitle;

		return $res;
	}

	/** Конвертировать в JSON.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToJSON()
	{
		$array = $this->convertToArray();
		$json = shav_convertArrayToJSON($array);

		return $json;
	}

	/** Получает все файлы для текущей задачи.
	 *	@param $str - строка настроек.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	private function getAllFilesByStr($str)
	{
		if($str == '')
		{
			echo '<b class="error">ОШИБКА: Не правильно передан параметр. Проверте параметр. <i>Значение: '.$str.'</i></b>';
			return;
		}

		$results = shav_options_pars($str);
		$files = array();
		foreach($results as $rec)
		{
			$file = new SHAV_TodoFile($rec['VALUE']);
			$file->objId = $this->id;
			$file->id = str_replace('$', '', $rec['NAME']);
			$file->id = str_replace('file', '', $file->id);
			$file->id = str_replace('image', '', $file->id);
			$files[] = $file;
		}

		return $files;
	}
}


/** @class SHAV_Project
 *	@brief Класс описывающий проект.
 *	Пример использования:
 *	@code
 * $id = 157; //Идентификатор проекта
 * $prj = new SHAV_Project($id);
 * $prj->saveToDB();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 12.06.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Project extends SHAV_Object
{
	/** Идентификатор проекта.*/
	public $id = 0;
	
	/** Заголовок проекта.*/
	public $title = '';
	
	/** Описание проекта.*/
	public $description = '';
	
	/** Идентификатор автора прокета.*/
	public $authorId = 0;
	
	/** Полное имя автора.*/
	public $fioAuthor = '';
	
	/** Дата добавления проекта в БД.*/
	public $createDate = 0;
	
	/** Дата начала проекта.*/
	public $startDate = 0;
	
	/** Дата окончания проекта.*/
	public $deadLine = 0;
	
	/** Список всех заданий проекта.*/
	public $todos = array();
	
	/** Список всех пользователей для проекта.*/
	public $users = array();

	/** Конструктор.
	 *	@param $id - идентификатор проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Project($id = 0)
	{
		if((int)$id <= 0) return;

		$this->createFromDBByID($id);
	}

	/** Создает проект из БД по идентификатору.
	 *	@param $id - идентификатор проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromDBByID($id)
	{
		global $shavDB;

		$sql = 'SELECT * FROM tracker_todo_projects WHERE todo_prj_id = '.(int)$id;
		$results = $shavDB->get_results($sql);

		foreach($results as $rec)
		{
			$this->id = $rec['todo_prj_id'];
			$this->title = htmlspecialchars_decode($rec['title_prj']);
			$this->description = htmlspecialchars_decode($rec['desc_prj']);
			$this->authorId = $rec['prj_author_id'];
			$this->createDate = $res['date_add'];
			$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
			$this->startDate = $rec['date_start'];
			$this->deadLine = $rec['date_end'];
			$this->users = array();
			$this->users = $this->getAllUsersForProject();

			$sql = 'SELECT todo_id FROM tracker_todos WHERE prj_id = '.(int)$rec['todo_prj_id'].' ORDER BY time_end ASC';
			$res = $shavDB->get_results($sql);
			$this->todos = array();

			foreach($res as $r)
			{
				$todo = new SHAV_Todo();
				$todo->createFromDBByID($r['todo_id']);
				$this->todos[] = $todo;
			}
		}
	}

	/** Создает объект из массива.
	 *	@param $array - массив параметров.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromArray($array)
	{
		if(empty($array))
		{
			echo MSG_ARRAYS_0002;
			return;
		}

		$this->id = $array['todo_prj_id'];
		$this->title = $array['title_prj'];
		$this->description = $array['desc_prj'];
		$this->authorId = $array['prj_author_id'];
		$this->createDate = $res['date_add'];
		$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
		$this->startDate = $array['date_start'];
		$this->deadLine = $array['date_end'];
		$this->todos = $array['todos'];
		$this->users = $array['users'];
	}

	/** Конвертировать в JSON.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToJSON()
	{
		$array = $this->convertToArray();
		$json = shav_convertArrayToJSON($array);

		return $json;
	}

	/** Сохраняет данные в БД.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function saveToDB()
	{
		global $shavDB;

		if((int)$this->id <= 0)
		{
			$sql = 'INSERT INTO tracker_todo_projects SET title_prj = "'.htmlspecialchars($this->title).'", desc_prj = "'.htmlspecialchars($this->description).'", prj_author_id = '.$this->authorId.', date_add = '.time().', date_start = '.$this->startDate.', date_end = '.$this->deadLine;
			$this->id = $shavDB->insert_data($sql);

			//Добавлям всех пользователей назначенных для данного проетка
			foreach($this->users as $user)
			{
				$user->addToProjectById($this->id);
			}

			//Добовляем все задания для проекта в БД
			foreach($this->todos as $todo)
			{
				$todo->saveToDB();
			}
		}
		else
		{
			$sql = 'UPDATE tracker_todo_projects SET title_prj = "'.htmlspecialchars($this->title).'", desc_prj = "'.htmlspecialchars($this->description).'", prj_author_id = '.$this->authorId.', date_start = '.$this->startDate.', date_end = '.$this->deadLine.' WHERE todo_prj_id = '.(int)$this->id;
			$shavDB->get_results($sql);

			$results = $shavDB->get_results('SELECT * FROM tracker_todo_users WHERE projetc_id = '.(int)$this->id);

			//Удаляем данные о порльзователе из проекта
			$k = 0;
			if(count($this->users) < count($results))
			{
				foreach($results as $rec)
				{
					$k = 0;$delUser = '';
					foreach($this->users as $user)
					{
						if($user->users_id == (int)$rec['user_id'])
						{
							$k = 0;
							break;
						}
						else
						{
							$delUser = new SHAV_User((int)$rec['user_id']);
							$k++;
						}
					}

					if($k > 0 && is_object($delUser))
						$delUser->deleteUserFromProjectById($this->id);
				}
			}
			elseif(count($this->users) > count($results))
			{
				foreach($this->users as $user)
				{
					$k = 0;
					foreach($results as $rec)
					{
						if($user->users_id == (int)$rec['user_id'])
						{
							$k = 0;
							break;
						}
						else
							$k++;
					}

					if($k > 0 || count($results) <= 0)
						$user->addToProjectById($this->id);
				}
			}

			//Сохроняем изменения в заданиях
			if(empty($this->todos)) return;

			foreach($this->todos as $todo)
			{
				if(is_object($todo))
					$todo->saveToDB();
			}
		}
	}

	/** Удалить из БД текущий проект.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function deleteFromDB()
	{
		global $shavDB;

		$sql = 'DELETE FROM tracker_todo_users WHERE projetc_id = '.(int)$this->id;
		$shavDB->get_results($sql);

		$sql = 'DELETE FROM tracker_todo_projects WHERE todo_prj_id = '.(int)$this->id;
		$shavDB->get_results($sql);

		//Удаляем все задания проекта
		foreach($this->todos as $todo)
		{
			if(is_object($todo))
				$todo->deleteFromDB();
		}
	}

	/** Получить массив последних n залдачь для проекта.
	 *	@param $count - количество задач, которые необходимо получить.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function getLastTodo($count)
	{
		$res = array();
		$i = 1;
		foreach($this->todos as $todo)
		{
			$res[] = $todo;

			if($i == $count)
				break;
		}

		return $res;
	}

	/** Возвращает пользователя по его id.
	 *	@param id - идентификатор пользователя.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function getUserById($id)
	{
		if((int)$id <= 0) return;

		foreach($this->users as $user)
		{
			if($user->users_id == $id)
				return $user;
		}

		return '';
	}

	/** Вывод списка задачь для проекта.
	 *	@param $count = 0 - количество выводимых задач для проекта;
	 *	@param $isDraw = false - выводить или нет html-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawTodosList($count = 0, $isDraw = false)
	{
		$content = '';
		if($count > 0)
		{
			$todosArr = getLastTodo($count);

			$content = '<div id="todo_list"><ul>';
			foreach($todosArr as $todo)
			{
				$sideBar = shav_createSideBar('sidebar', '<a href="?action=view_prj&id='.$this->id.'&todo_id='.$todo->id.'">'.$todo->title.'</a>', $todo->description);
				$content .= '<li>'.$sideBar.'</li>';
			}
			$content .= '</ul></div>';
		}
		else
		{
			$content = '<div id="todo_list"><ul>';
			foreach($this->todos as $todo)
			{
				$sideBar = shav_createSideBar('sidebar', '<a href="?action=view_prj&id='.$this->id.'&todo_id='.$todo->id.'">'.$todo->title.'</a>', $todo->description);
				$content .= '<li>'.$sideBar.'</li>';
			}
			$content .= '</ul></div>';
		}

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Конвертирует объект в массив.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToArray()
	{
		$res = array();
		$res['todo_prj_id'] = $this->id;
		$res['title_prj'] = $this->title;
		$res['desc_prj'] = $this->description;
		$res['prj_author_id'] = $this->authorId;
		$res['time_start'] = $this->startDate;
		$res['date_add'] = $this->createDate;
		$res['time_end'] = $this->deadLine;
		$res['authorFio'] = $this->fioAuthor;
		$res['todos'] = $this->todos;
		$res['fio'] = $this->fioAuthor;
		$res['users'] = $this->users;

		return $res;
	}

	/** Получаем пользователей для текущего проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	private function getAllUsersForProject()
	{
		global $shavDB;

		$sql = 'SELECT user_id FROM tracker_todo_users WHERE projetc_id = '.$this->id;
		$results = $shavDB->get_results($sql);

		$array = array();
		foreach($results as $rec)
		{
			$user = new SHAV_User($rec['user_id']);
			$array[] = $user;
		}

		return $array;
	}
}


/** @class SHAV_Comment
 *	@brief Класс описывающий комментарии.
 *	Пример использования:
 *	@code
 * $id = 157; //Идентификатор комментария
 * $com = new SHAV_Comment($id);
 * $com->saveToDB();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 12.06.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Comment extends SHAV_Object
{
	/** Идентификатор коментария.*/
	public $id = 0;
	
	/** Текст коментария.*/
	public $text = '';
	
	/** Идентификатор автора.*/
	public $authorId = 0;
	
	/** Полное имя автора.*/
	public $fioAuthor = '';
	
	/** Дата добавления в БД.*/
	public $addDate = 0;
	
	/** Идентификатор задания.*/
	public $todoId = 0;

	/** Конструцктор.
	 *	@param $id - идентификатор комментария.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Comment($id = 0)
	{
		if((int)$id <= 0) return;

		$this->createFromDBByID($id);
	}

	/** Создает объект из БД по его идентификатору.
	 *	@param $id - идентификатор комментария.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromDBByID($id)
	{
		global $shavDB;

		$sql = 'SELECT * FROM tracker_todo_comments WHERE comment_id = '.(int)$id;
		$results = $shavDB->get_results($sql);

		foreach($results as $rec)
		{
			$this->id = $rec['comment_id'];
			$this->text = htmlspecialchars_decode($rec['text']);
			$this->authorId = $rec['author_id'];
			$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
			$this->addDate = $rec['date_add'];
			$this->todoId = $rec['todo_id'];
		}
	}

	/** Создаем объект из массива.
	 *	@param $res - массив параметров.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function createFromArray($res)
	{
		global $shavDB;

		if(empty($res))
		{
			echo MSG_ARRAYS_0002;
			return;
		}

		$this->id = $rec['comment_id'];
		$this->text = $rec['text'];
		$this->authorId = $rec['author_id'];
		$this->fioAuthor = $shavDB->get_vars('SELECT fio FROM users WHERE users_id = '.$this->authorId);
		$this->addDate = $rec['date_add'];
		$this->todoId = $rec['todo_id'];
	}

	/** Сохранение в БД.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function saveToDB()
	{
		global $shavDB;

		if((int)$this->id <= 0)
		{
			$sql = 'INSERT INTO tracker_todo_comments SET text = "'.htmlspecialchars($this->text).'", author_id = '.$this->authorId.', date_add = '.$this->addDate.', todo_id = '.$this->todoId;
			$this->id = $shavDB->insert_data($sql);
		}
		else
		{
			$sql = 'UPDATE tracker_todo_comments SET text = "'.htmlspecialchars($this->text).'" WHERE comment_id = '.(int)$this->id;
			$shavDB->get_results($sql);
		}
	}

	/** Удалить из БД.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function deleteFromDB()
	{
		global $shavDB;

		$sql = 'DELETE FROM tracker_todo_comments WHERE comment_id = '.(int)$this->id;
		$shavDB->get_results($sql);
	}

	/** Конвертирует в массив.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToArray()
	{
		$res = array();
		$res['comment_id'] = $this->id;
		$res['text'] = $this->text;
		$res['author_id'] = $this->authorId;
		$res['date_add'] = $this->addDate;
		$res['fio'] = $this->fioAuthor;
		$rec['todo_id'] = $this->todoId;

		return $res;
	}

	/** Конвертировать в JSON.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function convertToJSON()
	{
		$array = $this->convertToArray();
		$json = shav_convertArrayToJSON($array);

		return $json;
	}
}


/** @class SHAV_TodoFile
 *	@brief Класс для хранения данных о ресурсах для компонентов (файлы, картинки).
 *	Пример использования:
 *	@code
 * $file = new SHAV_TodoFile('/uploads/file.txt');
 * $file->drawLink(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 12.06.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_TodoFile extends SHAV_Object
{
	/** Идентификатор файла.*/
	public $id = 0;
	
	/** Полный путь к файлу.*/
	public $url = '';
	
	/** Имя файла (file.exe).*/
	public $fName = '';
	
	/** Тип файла (.exe и т.д.).*/
	public $fType = '';
	
	/** Идентификатор компонента, к которому привязан файл.*/
	public $objId = 0;

	/** Конструктор.
	 *	@param $url - путь к файлу.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_TodoFile($url = '')
	{
		if($url == '') return;

		$this->url = $url;
		$this->fName = basename($this->url);
		$this->fType = substr($this->fName, -4);
		$this->id = 0;
		$this->objId = 0;
	}

	/** Выводит ссылку на файл.
	 *	@param $isDraw - выоыдить или нет html-код ссылки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawLink($isDraw = false)
	{
		$content = '<a href="'.$this->url.'">'.$this->fName.'</a>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Удаляет файл.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 06.02.2011*/
	function deleteFile()
	{
		if(file_exists($this->url))
			unlink($this->url);
	}
}


/** @class SHAV_TodoTraker
 *	@brief Класс для реализации функции трекера.
 *	Пример использования:
 *	@code
 * $tracker = new SHAV_TodoTraker();
 * $tracker->title = 'Менеджер проектов';
 * $tracker->createTraker();
 * $tracker->drawTraker(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 12.06.2010
 *	@date Обновленно: 27.02.2011*/
class SHAV_TodoTraker extends SHAV_Object
{
	/** Заголовок трекера.*/
	public $title = '';
	
	/** Ящик на сервере для отправки уведомлений.*/
	public $server_mail = '';
	
	/** Ссылка на трекер.*/
	public $url = '';
	
	/** Все проекты трекера.*/
	public $projects = array();
	
	/** Содержимое трекера в HTML.*/
	public $content = '';
	
	/** Количество задачь для проекта на главной страничке трекера.*/
	public $lastTodosCount = 5;
	
	/** Формат жаты для вывода.*/
	public $dateFormat = 'd.m.Y H:i:s';

	/** Конструктор.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	function SHAV_TodoTraker()
	{
		if(empty($_SESSION) || $_SESSION['users_id'] <= 0)
		{
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin');
		}
	}

	/** Создаем трекер.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	function createTraker()
	{
		global $shavDB;

		$this->title = 'SHAV TODO Traker';
		$this->url = '';
		$this->content = '';

		if($_SESSION['isadmin'] > 1)
			$sql = 'SELECT ttp.todo_prj_id FROM tracker_todo_projects ttp, tracker_todo_users ttu WHERE ttu.user_id = '.(int)$_SESSION['users_id'].' AND ttu.projetc_id = ttp.todo_prj_id ORDER BY date_end ASC';
		elseif($_SESSION['isadmin'] == 1)
			$sql = 'SELECT todo_prj_id FROM tracker_todo_projects';

		$results = $shavDB->get_results($sql);
		$this->projects = array();

		foreach($results as $rec)
		{
			$prj = new SHAV_Project($rec['todo_prj_id']);
			$this->projects[] = $prj;
		}
	}

	/** Отправка уведомлений для пользователей.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	function sendMailToUser()
	{
		global $shavDB;

		if($server_mail == '') return;

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>#TITLE#</title>
		</head>
		<body>
			<p>Здравствуйте, <b>#USER_FIO#!</b>!</p>
			<p>На сайте <a href="http://'.$_SERVER['HTTP_HOST'].'">'.$_SEREVER['HTTP_HOST'].'</a> были произведенны изменения. Вас добавили в один или несколько проектов. Чтобы посмотреть изменения воспользуйтесь вашим логином и паролем через сайт.</p>
			<p>С уважением, Администрация сайта <a href="http://'.$_SERVER['HTTP_HOST'].'">'.$_SEREVER['HTTP_HOST'].'</a>.</p>
		</body>
		</html>';

		$sql = 'SELECT todo_prj_id FROM projects WHERE date_add > '.time(date("yyy-mm-dd"));
		$results = $shavDB->get_results($sql);

		foreach($results as $pr)
		{
			$prj = new SHAV_Project($pr['todo_prj_id']);

			foreach($prj->users as $user)
			{
				$tags = array('#TITLE#'=>$prj->title, '#USER_FIO#'=>$user->fio);
				$html = shav_createContentsByTags($tags, $html);
				shav_send_mail($server_mail, $user->email, $html, $prj->title);
			}
		}
	}

	/** Выводим страничку трекера.
	 *	@param $isDraw - выводить или нет html-код странички.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	function drawTraker($idDraw = false)
	{
		//Анализ параметров GET-запросов
		if($_GET['action'] == 'view_prj' && (int)$_GET['id'] > 0)//Просмотр подробного вида проектов
		{
			$curPrj = $this->getProjetcByID($_GET['id']);

			$users = '';
			foreach($curPrj->users as $u)
			{
				$users .= '<b>'.$u->fio.'</a>, ';
			}
			$users = substr($users, 0, strlen($users)-2);

			$prj_content  = '<div class="project">';
			$prj_content .= '<div class="desc">'.htmlspecialchars_decode($curPrj->description).'</div>';
			$prj_content .= '<div class="info"><table width="100%"><tr><td>Дата&nbsp;начала:&nbsp;'.date($this->dateFormat, $curPrj->startDate).'</td><td>Дата&nbsp;окончания:&nbsp;'.date($this->dateFormat, $curPrj->deadLine).'</td><td>Автор:&nbsp;<b>'.$curPrj->fioAuthor.'</b></td></tr><tr><td colspan="3">Пользователи: '.$users.'</td></tr></table></div>';
			$prj_content .= '<div class="todo_list"><h4>Задачи:</h4>';
			if(!empty($curPrj->todos) && count($curPrj->todos) > 0)//Получяаем список всех задачь для выбранного проекта
			{
				$prj_content .= '<table width="100%">';
				foreach($curPrj->todos as $todo)
				{
					if($todo->statusId == 4)
						$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:line-through;color:#ccc;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
					elseif($todo->statusId == 1)
						$link = '<a href="?action=view_todo&id='.$todo->id.'"style="text-decoration:none;color:green;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
					elseif($todo->statusId == 2)
						$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:none;color:yellow;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
					elseif($todo->statusId == 3)
						$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:none;color:red;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';

					$prj_content .= '<tr><td width="15%" align="center">'.date($this->dateFormat, $todo->timeBegin).'</td><td width="80%">'.$link.'</td></tr>';
				}
				$prj_content .= '</table></div>';
			}
			else
			{
				$prj_content .= '<p>В этом проекте нет задачь</p></div>';
			}

			$prj_content = $this->createDataView(htmlspecialchars_decode($curPrj->title), $prj_content.'</div>');
			if($curPrj->getUserById($_SESSION['users_id'])->accessId == 1 || $_SESSION['isadmin'] == 1)
				$prj_content .= '<div style="width:220px;float:right;">['.$this->createFormEditProject($curPrj).']&nbsp;['.$this->createFormAddNewTodo($curPrj).']</div>';

			$prj_list = $prj_content;
		}
		elseif($_GET['action'] == 'view_todo' && (int)$_GET['id'] > 0)//Просмотр подробного вида задачи
		{
			$curTodo = new SHAV_Todo((int)$_GET['id']);
			$prj_list  = '<div class="project">';
			$prj_list .= '<div class="desc">'.htmlspecialchars_decode($curTodo->description).'</div>';
			$prj_list .= '<div class="info"><table width="100%"><tr><td>Дата&nbsp;начала:&nbsp;'.date($this->dateFormat, $curTodo->timeBegin).'</td><td>Дата&nbsp;окончания:&nbsp;'.date($this->dateFormat, $curTodo->deadLine).'</td></tr><tr><td>Автор:&nbsp;<b>'.$curTodo->fioAuthor.'</b></td><td>Статус:&nbsp;<b>'.$curTodo->statusTitle.'</b></td><td>Приоритет:&nbsp;<b>'.$curTodo->priorityTitle.'</b></td></tr></table></div>';
			$comments_list .= '<div class="todo_list">';
			if(!empty($curTodo->comments) && count($curTodo->comments) > 0)//Получаем список всех комментариев для выбранной задачи
			{
				$comments_list .= '<table width="100%">';
				foreach($curTodo->comments as $comment)
				{
					$comments_list .= '<tr><td width="80%"><div class="todo_comment"><table width="100%"><tr><td><table width="100%"><tr><td width="20%">Автор:&nbsp;<b>'.$comment->fioAuthor.'</b></td><td width="25%">Добавленно:&nbsp;'.date($this->dateFormat, $comment->addDate).'</td><td>['.$this->createFormEditComment($comment).']</td></tr></table></td></tr><tr><td>'.htmlspecialchars_decode($comment->text).'</td></tr></table></div></td></tr>';
				}
				$comments_list .= '</table>';
			}
			else
			{
				$comments_list .= '<p>К этой задаче нет комментариев</p>';
			}

			$comments_list .= '</div>';

			$prj_list .= $this->createDataView("<h4>Комментарии:</h4>", $comments_list);

			if(!empty($curTodo->files) && count($curTodo->files) > 0)
			{
				$prj_list .= '<div class="todo_list"><table width="100%"><tr>';
				$i = 1;
				foreach($curTodo->files as $file)
				{
					if($i >= 6)
					{
						$prj_list .= '</tr><tr>';
						$i = 1;
					}

					$prj_list .= '<td>'.$file->drawLink().'</td>';

					$i++;
				}
				$prj_list .= '</tr></table></div>';
			}

			$prj_list = $this->createDataView(htmlspecialchars_decode($curTodo->title), $prj_list.'</div>');
			
			if($_SESSION['isadmin'] == 1)
				$prj_list .= '<div style="width:400px;float:right;">['.$this->createFormEditTodo($curTodo).']&nbsp;['.$this->createFormAddNewComment($curTodo).']&nbsp;['.$this->createFormUploadFile($curTodo).']</div>';
			else
				$prj_list .= '<div style="width:400px;float:right;">['.$this->createEditTodoFormForUser($curTodo).']&nbsp;['.$this->createFormAddNewComment($curTodo).']&nbsp;['.$this->createFormUploadFile($curTodo).']</div>';

		}
		else	//Просто показываем все проекты и некоторые задачи для проектов
		{
			//Создаем список всех проектов
			$prj_list  = '<div><div style="float:right;width:100px;">'.$this->createFormAddNewProject().'</div></div>';
			if($_SESSION['isadmin'] > 1)
				$prj_list  = '';
			$prj_list .= '<div id="project_list">';
			if(empty($this->projects))
			{
				$prj_list .= '<p>В данный момент зарегистрированных проектов нет!</p></div>';
			}
			else
			{
				$prj_list .= '<ul>';
				foreach($this->projects as $prj)
				{
					//Создаем список последник n задачь для конкретного проекта
					$todosList = '<div id="todo_list"><table width="100%">';
					$lastTodos = $prj->getLastTodo($this->lastTodosCount);
					foreach($lastTodos as $todo)
					{
						if($todo->statusId == 4)
							$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:line-through;color:#ccc;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
						elseif($todo->statusId == 1)
							$link = '<a href="?action=view_todo&id='.$todo->id.'"style="text-decoration:none;color:green;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
						elseif($todo->statusId == 2)
							$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:none;color:yellow;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
						elseif($todo->statusId == 3)
							$link = '<a href="?action=view_todo&id='.$todo->id.'" style="text-decoration:none;color:red;">'.htmlspecialchars_decode($todo->title).'&nbsp;&nbsp;&nbsp;[<b>'.$todo->statusTitle.'</b>]</a>';
						
						$todosList .= '<tr><td width="120px" align="center">'.date($this->dateFormat, $todo->deadLine).'</td><td>'.$link.'</td></tr>';
					}
					$todosList .= '</table></div>';
					
					
					$prj_list .= $this->createDataView('<a href="?action=view_prj&id='.$prj->id.'">'.htmlspecialchars_decode($prj->title).'</a>', $todosList);
					/*$prj_list .= '<li><div class="project"><h1><a href="?action=view_prj&id='.$prj->id.'">'.htmlspecialchars_decode($prj->title).'</h1><div>'.$todosList.'</div></div></li>';*/
				}
				$prj_list .= '</ul></div>';
			}
		}

		if($isDraw == true)
			echo $prj_list;
		else
			return $prj_list;
	}

	/** Получить проект по идентификатору из массива.
	 *	@param $id - идентификатор проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	function getProjetcByID($id)
	{
		if((int)$id <= 0)
			return;

		foreach($this->projects as $prj)
		{
			if($prj->id == $id)
				return $prj;
		}

		return;
	}

	/** Создает модальное окно для добавления нового проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormAddNewProject()
	{
		$usersList = new SHAV_DropList();
		$usersList->isMultiple = true;
		$usersList->size = 5;
		$usersList->createListFromDBTable(array('name' => 'users', 'id_field' => 'users_id', 'name_field' => 'fio'), 'users_ids[]', (int)$_SESSION['users_id']);
		$users = $usersList->drawList();

		$recs = array(	array('name'=>'title', 'label_align'=>'left', 'label'=>'Заголовок проекта:', 'type'=>'text', 'size'=>'25', 'value'=>''),
		array('name'=>'desc', 'label_align'=>'left', 'label'=>'Описание:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value'=>''),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$_SESSION['users_id']),
		array('name'=>'date_start', 'label_align'=>'left', 'label'=>'Дата начала:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat)),
		array('name'=>'data_end', 'label_align'=>'left', 'label'=>'Дата окончания:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat)),
		array('name'=>'users_ids', 'label_align'=>'left', 'label'=>'Пользоватиели проекта:', 'type'=>'list', 'size'=>'25', 'value'=>$users),
		array('name'=>'add', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'add_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'project_add';
		$wnd->title = 'Добавить нового проекта';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Добавить проект';

		return $wnd->drawModalWindow();
	}

	/** Создает модальное окно для редактирование проекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormEditProject($project)
	{
		if(!is_object($project))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormEditProject($project)</i></b>';
			return;
		}

		$selectUsersId = array();
		foreach($project->users as $user)
		{
			$selectUsersId[] = $user->users_id;
		}

		$usersList = new SHAV_DropList();
		$usersList->isMultiple = true;
		$usersList->size = 5;
		$usersList->createListFromDBTable(array('name' => 'users', 'id_field' => 'users_id', 'name_field' => 'fio'), 'users_ids[]', $selectUsersId);
		$users = $usersList->drawList();

		$recs = array(	array('name'=>'title', 'label_align'=>'left', 'label'=>'Заголовок проекта:', 'type'=>'text', 'size'=>'25', 'value'=>$project->title),
		array('name'=>'desc', 'label_align'=>'left', 'label'=>'Описание:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value' => $project->description),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$project->authorId),
		array('name'=>'date_start', 'label_align'=>'left', 'label'=>'Дата начала:', 'type' => 'text', 'size'=>'25', 'value'=>date($this->dateFormat, $project->startDate)),
		array('name'=>'data_end', 'label_align'=>'left', 'label'=>'Дата окончания:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat, $project->deadLine)),
		array('name'=>'users_ids', 'label_align'=>'left', 'label'=>'Пользоватиели проекта:', 'type'=>'list', 'size'=>'25', 'value'=>$users),
		array('name'=>'prj_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$project->id),
		array('name'=>'delete', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Удалить'),
		array('name'=>'save', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'project_save_'.$project->id;
		$wnd->title = 'Изменить проект';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Редактировать';

		return $wnd->drawModalWindow();
	}

	/** Создает форму для добавления задачи в проект.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormAddNewTodo($project)
	{
		if(!is_object($project))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormAddNewTodo($project)</i></b>';
			return;
		}

		$todoStatus = new SHAV_DropList();
		$todoStatus->createListFromDBTable(array('name' => 'tracker_todo_status', 'id_field' => 'todo_status_id', 'name_field' => 'todo_status_name'), 'todo_status', 1);
		$status = $todoStatus->drawList();

		$todoPriority = new SHAV_DropList();
		$todoPriority->createListFromDBTable(array('name' => 'tracker_todo_priority', 'id_field' => 'todo_priority_id', 'name_field' => 'todo_priority_name'), 'todo_priority', 2);
		$priority = $todoPriority->drawList();

		$recs = array(	array('name'=>'title', 'label_align'=>'left', 'label'=>'Заголовок задачи:', 'type'=>'text', 'size'=>'25', 'value'=>''),
		array('name'=>'desc', 'label_align'=>'left', 'label'=>'Описание:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value'=>''),
		array('name'=>'todo_status', 'label_align'=>'left', 'label'=>'Статус задания:', 'type'=>'list', 'size'=>'25', 'value'=>$status),
		array('name'=>'todo_priority', 'label_align'=>'left', 'label'=>'Приоритет задания:', 'type'=>'list', 'size'=>'25', 'value'=>$priority),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$_SESSION['users_id']),
		array('name'=>'date_start', 'label_align'=>'left', 'label'=>'Дата начала:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat)),
		array('name'=>'data_end', 'label_align'=>'left', 'label'=>'Дата окончания:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat)),
		array('name'=>'prj_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$project->id),
		array('name'=>'add_todo', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'todo_add';
		$wnd->title = 'Добавление задачи к проекту';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Добавить задачу';

		return $wnd->drawModalWindow();
	}

	/** Добавляет форму загрузки файлов для задачи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormUploadFile($todo)
	{
		if(!is_object($todo))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormUploadFile($todo)</i></b>';
			return;
		}

		$recs = array(	array('name'=>'files[]', 'label_align'=>'left', 'label'=>'Файл 1: ', 'type'=>'file', 'size'=>'25', 'value'=>''),
							array('name'=>'files[]', 'label_align'=>'left', 'label'=>'Файл 2: ', 'type'=>'file', 'size'=>'25', 'value'=>''),
						array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$_SESSION['users_id']),
		array('name'=>'todo_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$todo->id),
		array('name'=>'add_file', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>FILE_UPLOAD, 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'file_add';
		$wnd->title = 'Добавление файла к задачи';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Добавить файл к задачи';

		return $wnd->drawModalWindow();
	}

	/** Создает форму для редактирования задачи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormEditTodo($todo)
	{
		if(!is_object($todo))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormEditTodo($todo)</i></b>';
			return;
		}

		$todoStatus = new SHAV_DropList();
		$todoStatus->createListFromDBTable(array('name' => 'tracker_todo_status', 'id_field' => 'todo_status_id', 'name_field' => 'todo_status_name'), 'todo_status', $todo->statusId);
		$status = $todoStatus->drawList();

		$todoPriority = new SHAV_DropList();
		$todoPriority->createListFromDBTable(array('name' => 'tracker_todo_priority', 'id_field' => 'todo_priority_id', 'name_field' => 'todo_priority_name'), 'todo_priority', $todo->priorityId);
		$priority = $todoPriority->drawList();

		$recs = array(	array('name'=>'title', 'label_align'=>'left', 'label'=>'Заголовок задачи:', 'type'=>'text', 'size'=>'25', 'value'=>$todo->title),
		array('name'=>'desc', 'label_align'=>'left', 'label'=>'Описание:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value'=>$todo->description),
		array('name'=>'todo_status', 'label_align'=>'left', 'label'=>'Статус задания:', 'type'=>'list', 'size'=>'25', 'value'=>$status),
		array('name'=>'todo_priority', 'label_align'=>'left', 'label'=>'Приоритет задания:', 'type'=>'list', 'size'=>'25', 'value'=>$priority),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$todo->authorId),
		array('name'=>'date_start', 'label_align'=>'left', 'label'=>'Дата начала:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat, $todo->timeBegin)),
		array('name'=>'data_end', 'label_align'=>'left', 'label'=>'Дата окончания:', 'type'=>'text', 'size'=>'25', 'value'=>date($this->dateFormat, $todo->deadLine)),
		array('name'=>'todo_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$todo->id),
		array('name'=>'delete_todo', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Удалить'),
		array('name'=>'save_todo', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'todo_save_'.$todo->id;
		$wnd->title = 'Редактирование задачи';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Редактировать';

		return $wnd->drawModalWindow();
	}

	/** Создает форму для редактирования задачи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createEditTodoFormForUser($todo)
	{
		if(!is_object($todo))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createEditTodoFormForUser($todo)</i></b>';
			return;
		}

		$todoStatus = new SHAV_DropList();
		$todoStatus->createListFromDBTable(array('name' => 'tracker_todo_status', 'id_field' => 'todo_status_id', 'name_field' => 'todo_status_name'), 'todo_status', $todo->statusId);
		$status = $todoStatus->drawList();

		$recs = array(array('name'=>'todo_status', 'label_align'=>'left', 'label'=>'Статус задания:', 'type'=>'list', 'size'=>'25', 'value'=>$status),
					array('name'=>'todo_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$todo->id),
					array('name'=>'save_status_todo', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		//Создаем окно
		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'todo_status_change_'.$todo->id;
		$wnd->title = 'Изменение статуса задачи';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Изменить статус';

		return $wnd->drawModalWindow();
	}


	/** Создает форму для добавления комментария.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormAddNewComment($todo)
	{
		if(!is_object($todo))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormAddNewComment($todo)</i></b>';
			return;
		}

		$recs = array(array('name'=>'text', 'label_align'=>'left', 'label'=>'Ваш комментарий:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value' => ''),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$_SESSION['users_id']),
		array('name'=>'date_start', 'label_align'=>'left', 'label'=>'Дата начала:', 'type'=>'hidden', 'size'=>'25', 'value'=>date($this->dateFormat)),
		array('name'=>'todo_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$todo->id),
		array('name'=>'add_comment', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'comment_add';
		$wnd->title = 'Добавление комментария';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Добавить комментарий';

		return $wnd->drawModalWindow();
	}

	/** Создает форму для редактирования комментариев к заданию.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createFormEditComment($comment)
	{
		if(!is_object($comment))
		{
			echo '<b class="error">Переданный параметр не является объектом. проверти правильность передачи параметра в функцию: <i>function createFormEditComment($comment)</i></b>';
			return;
		}

		$recs = array(array('name'=>'text', 'label_align'=>'left', 'label'=>'Ваш комментарий:', 'type'=>'textarea', 'size'=>array('cols'=>'50', 'rows'=>'10'), 'value' => $comment->text),
		array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$comment->authorId),
		array('name'=>'comment_id', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$comment->id),
		array('name'=>'delete_comment', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Удалить'),
		array('name'=>'edit_comment', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить'));

		$params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		$content = $form->drawForm();

		$wnd = new SHAV_jqModal();
		$wnd->windowId = 'comment_edit_'.(int)$comment->id;
		$wnd->title = 'Редактирование комментария';
		$wnd->wndSize = array('width'=>'620px');
		$wnd->windowContent = $content;
		$wnd->linkId = $wnd->windowId.'Trigger';
		$wnd->linkName = 'Изменить';

		return $wnd->drawModalWindow();
	}

	/** Создать вид компонента.
	 *	@param $title - загаловок комопнента;
	 *	@param $text - содержимое комопнента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 12.06.2010
	 *	@date Обновленно: 27.02.2011*/
	private function createDataView($title, $text)
	{
		$content  = '<div class="frame"><div class="header_frame"><h1>'.$title.'</h1></div><div class="content_frame">'.$text.'</div></div>';

		return $content;
	}
}
?>