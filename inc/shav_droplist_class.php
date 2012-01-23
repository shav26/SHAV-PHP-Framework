<?php

/** @class SHAV_DropList
 *	@brief Класс выподающего списка.
 *	Пример использования:
 *	@code
 * $typeList = new SHAV_DropList();
 * $typeList->createListFromDBTable(array('name' => 'users_type', 'id_field' => 'users_type_id', 'name_field' => 'users_status_name'), 'type');
 * $type = $typeList->drawList();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 14.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_DropList extends SHAV_Object
{
	/** HTML-код компонента.*/
	public $content = '';
	
	/** Идентификатор компонента.*/
	public $idList = '';
	
	/** Имя списка.*/
	public $nameList = '';
	
	/** Стиль списка.*/
	public $syleList = '';
	
	/** Является ли данный список списком с множественным выбором.*/
	public $isMultiple = false;
	
	/** Включенны или нет список.*/
	public $isDisable = 'false';
	
	/** Размер компонента*/
	public $size = 0;
	
	/** Текста для значения по умолчанию.*/
	public $defaultTitle = 'Choose...';
	
	/** Параметр для действия onAction.*/
	public $action = '';
	
	/** Массив данных.
	 *	@code
	 * array('value' => '0', 'title' => 'Choose...', 'isSelect' => 'false'), array('value' => '1', 'title' => 'Some string', 'isSelect' => 'false')
	 *	@endcode*/
	public $dataArray = array(array('value' => '0', 'title' => 'Choose...', 'isSelect' => 'false'), array('value' => '1', 'title' => 'Some string', 'isSelect' => 'false'));

	/** Конструктор класса
	 *	@param $defaultText = '' - текст для элемента по-умолчанию.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_DropList($defaultText = '')
	{
		if($defaultText != '')
			$this->defaultTitle = $defaultText;
		
		$this->content = '';
	}
	
	/** Создает выводающий список.
	 *	@param $params - массив параметров для указания стиля списка и названия для формы;
	 *	@param $data - массив данных для добавления в список;
	 *	@param $isDraw - выводить или нет список.
	 *	@return Объект класса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function createListFromArray($params = array('style_class' => '', 'name' => 'frm_list', 'disable'=>'false'), $data = array(array('value' => '0', 'title' => 'Choose ...', 'isSelect' => 'false'), array('value' => '1', 'title' => 'Some string', 'isSelect' => 'false')))
	{
		if(empty($params))
			return MSG_ARRAYS_0001;

		$this->idList = $params['name'];
		$this->nameList = $params['name'];
		$this->syleList = $params['style_class'];
		$this->isDisable = $params['disable'];
		$this->dataArray = $data;

		$this->content = '';
	}

	/** Вывести список
	 *	@param $isDraw - выводить или нет html-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawList($isDraw = false)
	{
		$disabled = '';
		if($this->isDisable == 'true')
			$disabled = 'disabled';

		$multiple = '';
		if($this->isMultiple == true)
			$multiple = 'multiple';

		$sizeStr = '';
		if($this->size > 0)
			$sizeStr = 'size="'.$this->size.'"';
		
		$this->content  = '<select class="'.$this->syleList.'" name="'.$this->nameList.'" id="'.$this->nameList.'" '.$disabled.' '.$multiple.' '.$sizeStr.' '.$this->action.'>';
		$i = 0;
		foreach($this->dataArray as $rec)
		{
			if($rec['isSelect'] == 'true')
				$this->content .= '<option value="'.$rec['value'].'" selected>'.$rec['title'].'</option>';
			else
				$this->content .= '<option value="'.$rec['value'].'">'.$rec['title'].'</option>';
			
			$i++;
		}
		$this->content .= '</select>';
		
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
	
	/** Создает выподающий список по таблице из БД.
	 *	@param $params_table - массив описывающий таблицу из которой бурется данные: array('name' => '', 'id_field' => '', 'name_field' => '');
	 *	@param $name - идентификатор списка в форме;
	 *	@param $select - идентификатор записи в таблице (и в списке), который следует выбрать;
	 *	@param $disable - выключен или нет список;
	 *	@param $style - стиль списка;
	 *	@param $isDraw - Выводить или вернуть html-код.
	 *	@return Объект класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function createListFromDBTable($params_table, $name = '', $select = 0, $disable = 'false', $style = '')
	{
		global $shavDB;
		
		$sql = 'SELECT * FROM '.$params_table['name'];
		$results = $shavDB->get_results($sql);
		if($this->isMultiple == true)
			$data[] = array('value' => '0', 'title' => $this->defaultTitle, 'isSelect' => 'false');
		else
			$data[] = array('value' => '0', 'title' => $this->defaultTitle, 'isSelect' => 'true');
		foreach($results as $rec)
		{
			if(is_array($select) && !empty($select))
			{
				$k = 0;
				foreach($select as $id)
				{
					if((int)$id == $rec[$params_table['id_field']])
						$k++;
				}

				if($k > 0)
					$data[] = array('value' => $rec[$params_table['id_field']], 'title' => $rec[$params_table['name_field']], 'isSelect' => 'true');
				else
					$data[] = array('value' => $rec[$params_table['id_field']], 'title' => $rec[$params_table['name_field']], 'isSelect' => 'false');
			}
			else
			{
				if((int)$select == $rec[$params_table['id_field']])
					$data[] = array('value' => $rec[$params_table['id_field']], 'title' => $rec[$params_table['name_field']], 'isSelect' => 'true');
				else
					$data[] = array('value' => $rec[$params_table['id_field']], 'title' => $rec[$params_table['name_field']], 'isSelect' => 'false');
			}
		}

		$this->createListFromArray(array('style_class' => $style, 'name' => $name, 'disable' => $disable), $data);
	}
	
	/** Создает список из SQL-запроса.
	 *	@param $sql - SQL-запрос, на базе которого необходимо создать список;
	 *	@param $field_value - Поле из БД, которое будет выступать в качестве значений пунтка;
	 *	@param $field_text - Поле из БД, которое будет выступать в качестве текста пунска.
	 *	@return Объект класса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function createListFromSQL($sql, $field_value, $field_text, $name, $SelectElem = 0, $disable = 'false', $style = '')
	{
		global $shavDB;

		$results = $shavDB->get_results($sql);
		$data[] = array('value' => '0', 'title' => $this->defaultTitle, 'isSelect' => 'true');
		foreach($results as $rec)
		{
			if((int)$SelectElem == $rec[$field_value])
				$data[] = array('value' => $rec[$field_value], 'title' => $rec[$field_text], 'isSelect' => 'true');
			else
				$data[] = array('value' => $rec[$field_value], 'title' => $rec[$field_text], 'isSelect' => 'false');
		}

		$this->createListFromArray(array('style_class' => $style, 'name' => $name, 'disable' => $disable), $data);
	}
}
?>