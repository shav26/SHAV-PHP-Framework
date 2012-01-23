<?php
/** @class SHAV_Search
 *	@brief Класс для поиска контента по сайту используя БД.
 *	Пример использования:
 *	@code
 * $sercher = new SHAV_Search();
 * if(empty($_POST))
 *	echo '<div width="200px">'.$sercher->drawSearchForm().'</div>';
 * elseif($_POST['search_btn'])
 * {
 *	$sercher->doSearch('users', array('fio'), 'users_id');
 *	echo $sercher->drawResults();
 * }
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.07.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Search extends SHAV_Object
{
	/** Строка название.*/
	public $title = 'Найти:';

	/** Метод передачи данных из формы в скрипт результатов.*/
	public $method = 'POST';
	
	/** Идентификатор формы поиска.*/
	public $id = 'search_form';
	
	/** Идентификатор строки поиска.*/
	public $id_field = 'search_text';
	
	/** Описание компонента для вывода результатов поиска.*/
	public $search_results = 'search_results';
	
	/** Сообщение показывается если нет данных по искомому тексту.*/
	public $no_results = 'По вашему запросу нет данных';
	
	/** Название кнопки.*/
	public $btnTitle = 'Поиск';
	
	/** HTML-содержимое компонента.*/
	public $content = '';
	
	/** Скрипт для вывода рузельтатов поиска.*/
	public $actionScript = '';
	
	/** Скрипт для создания ссылок на статьи и т.д.*/
	public $link_data = 'articls.php';

	/** Конструктор.
	 *	@param $titleText - заголовок для строки поиска;
	 *	@param $btn - заголовок для кнопки поиска.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.07.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Search($titleText = '', $btn = '')
	{
		if($titleText != '') $this->title = $titleText;

		if($btn != '') $this->btnTitle = $btn;
	}

	/** Вывод формы поиска.
	 *	@param $isDraw - выводить или нет html-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.07.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawSearchForm($isDraw = false)
	{
		$content  = '<div class="search_field"><form method="'.$this->method.'" id="'.$this->id.'" name="'.$this->id.'" action="'.$this->actionScript.'">';
		$content .= '<table width="100%">';
		$content .= '<tr><td>'.$this->title.'</td><td><input type="text" id="'.$this->id_field.'" name="'.$this->id_field.'" value="" /><input type="submit" id="search_btn" name="search_btn" value="'.$this->btnTitle.'" /></td></tr>';
		$content .= '</table></form>';
		$content .= '</div>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Выполняет поиск по БД используя таблицу и поля в этой таблице.
	 *	@param $table - название таблицы в БД, в которой будет производится поиск;
	 *	@param $fields - массив полей из таблицы $table, по которым будет производится поиск (первым элементов должно быть поле для заголовков);
	 *	@param $id_field - название поля, которое описавает идентификаторы объектов для создания ссылок;
	 *	@param $text - строка, которую необходимо найти.
	 *	@return HTML-код с результатами поиска.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.07.2010
	 *	@date Обновленно: 06.02.2011*/
	function doSearch($table, $fields, $id_field, $text = '')
	{
		global $shavDB;

		if($text == '')
			$text = $_POST[$this->id_field];

		//Создаем условие поиска в БД
		$where = '';
		if(is_array($fields))
		{
			$i = 0; $n = count($fields);
			foreach($fields as $field)
			{
				if($i < $n-1)
					$where .= $field.' LIKE "%'.$text.'%" OR ';
				elseif($i >= $n-1)
					$where .= $field.' LIKE "%'.$text.'%"';
			}
		}

		$sql = 'SELECT * FROM '.$table.' WHERE '.$where;
		$results = $shavDB->get_results($sql);

		if(count($results) <= 0)
			$this->content = '<div class="'.$this->search_results.'"><p>'.$this->no_results.'</p></div>';

		else
		{
			$this->content  = '<div class="'.$this->search_results.'">';
			foreach($results as $res)
				$this->content .= '<p><a href="'.$link_data.'?'.$id_field.'='.$res[$id_field].'">'.$res[$fields[0]].'</a></p>';

			$this->content .= '</div>';
		}
	}

	/** Вывод результатов поиска.
	 *	@param $isDraw выводить или вернуть html-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.07.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawResults($isDraw = false)
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
}
?>