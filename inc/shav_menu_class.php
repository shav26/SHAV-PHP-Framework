<?php
/** @class MENU_Item
 *	@brief Класс элемента меню (пункт меню).
 *	Пример использования:
 *	@code
 * $item = new MENU_Item($title, $link, $icon, $isShow, $submenu);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 01.02.2010
 *	@date Обновленно: 06.02.2011*/
class MENU_Item extends SHAV_Object
{
	/** Размер иконки для пункта меню*/
	public $icon_size = array('width'=>16, 'height'=>16);

	/** Загаловок пункта меню*/
	public $titleItem = '';

	/** Ссылка на иконку меню.*/
	public $iconItem = '';

	/** Ссылка на страничку.*/
	public $lickItem = '';

	/** Показывать или нет данный элемент меню.*/
	public $isShow = 1;

	/** Массив подпунктов.*/
	public $itemSubmenu = array();

	/** Конструктор класса, создает объект.
	 *	@param $title - заголовок пункта меню;
	 *	@param $link - ссылка для данного пункта меню;
	 *	@param $icon - иконка для пункта;
	 *	@param $isShow - показывать или нет (1-показывать, 0-не показывать);
	 *	@param $submenu - массив подпунктов (MENU_Item).
	 *	@return Объект класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function MENU_Item($title, $link, $icon, $show = 1, $submenu = array())
	{
		$this->titleItem = $title;
		$this->lickItem = $link;
		$this->iconItem = $icon;
		$this->isShow = $show;
		$this->itemSubmenu = $submenu;
	}

	/** Вывод пунктов для простого HTML меню.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выводит или возвращает HTML-код
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawHTMLItem($isDraw = false)
	{
		$submenu = '';
		if(!empty($this->itemSubmenu) && count($this->itemSubmenu) > 0)
		{
			$submenu = '<ul>';
			foreach($this->itemSubmenu as $rec)
				$submenu .= $rec->drawHTMLItem();
			$submenu .= '</ul>';
		}

		if($this->isShow == 1)
		{
			if($isDraw == true)
				echo '<li><a href="'.$this->lickItem.'">'.$this->titleItem.$submenu.'</a></li>';
			else
				return '<li><a href="'.$this->lickItem.'">'.$this->titleItem.$submenu.'</a></li>';
		}
	}

	/** Вывод для меню с использованием плагина на jQuery Стики.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выводит или возвращает HTML-код
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawLickMenuItem($isDraw = false)
	{
		if($isDraw == true)
			echo '<li><a href="'.$this->lickItem.'"><img src="'.$this->iconItem.'" alt="'.$this->titleItem.'" /></a></li>';
		else
			return '<li><a href="'.$this->lickItem.'"><img src="'.$this->iconItem.'" alt="'.$this->titleItem.'" /></a></li>';
	}

	/** Вывод для меню с использованием плагина на jQuery "Рыбий глаз".
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выводит или возвращает HTML-код
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawFishEyeItem($isDraw = false)
	{
		$path = substr($this->iconItem, 0, strlen($this->iconItem));//-4);
		
		if($isDraw == true)
			echo '<a href="'.$this->lickItem.'" title="'.$this->titleItem.'"><img src="'.$path.'" alt="" /></a>';
		else
			return '<a href="'.$this->lickItem.'" title="'.$this->titleItem.'"><img src="'.$path.'" alt="" /></a>';
	}
}

/** @class SHAV_Menu
 *	@brief Класс для меню.
 *	Пример использования:
 *	@code
 * $menu = new SHAV_Menu();
 * $menu->createMenuFromArray($sites_pages, 3);
 * $jQueryMenu = $menu->content;
 *	@endcode
 *	@param $content = ''; - HTML-код меню
 *	@param $items = array(); - Пункты меню.
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 01.02.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Menu extends SHAV_Object
{
	/** HTML-код меню.*/
	public $content = '';

	/** Пункты меню.*/
	public $items = array();

	/** Конструктор класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Menu()
	{}

	/** Функция создание меню.
	 *	@param $array - массив настройки
	 *	@param $type - тип меню (1-простое HTML-меню; 2-Меню стиками; 3-меню "Рыбий глаз").
	 *	@return Меню выбранного типа и созданного из массива настроек
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function createMenuFromArray($array, $type = 1)
	{
		$this->items = $this->createMenuArray($array);

		switch($type)
		{
			case 1:
				$this->content  = '<div class="menu"><ul>';
				foreach($this->items as $rec)
					$this->content .= $rec->drawHTMLItem();
				$this->content .= '</ul></div>';
				break;
			case 2:
				$this->createMenu_jQuery();
				break;
			case 3:
				$this->createMenu_jQuery(2);
				break;
		}
	}

	/** Добавление нового пункта меню.
	 *	@param $title - заголовок пункта меню;
	 *	@param $link - ссылка для данного пункта меню;
	 *	@param $icon - иконка для пункта;
	 *	@param $isShow - показывать или нет (1-показывать, 0-не показывать);
	 *	@param $submenu - массив подпунктов (MENU_Item).
	 *	@return Новый пункт меню.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function addItem($title, $link, $icon, $isShow = 1, $submenu = array())
	{
		$this->items[] = new MENU_Item($title, $link, $icon, $isShow, $submenu);
	}

	/** Вывод меню.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawHTMLMenu()
	{
		echo $this->content;
	}

	/** Создает меню с использувание jQuery и некоторых плагинов.
	 *	@param $param - массив конфигурации, имеет такой вид: array('pages_arr' => array(....), 'count_pages' => 0, 'type_menu' => 0);
	 *	@param $isDraw - Выводить или вернуть html-код.
	 *	@return Выводит или возвращает html-код меню.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createMenu_jQuery($type_menu = 1)
	{
		if($type_menu == 1)		//Используется библиотека shav_LickMenu()
		{
			$this->content  = '<div id="lickMenu"><ul>';

			foreach($this->items as $rec)
			{
				if($rec->isShow == 1)
					$this->content .= $rec->drawFishEyeItem();
			}

			$this->content .= '</ul></div>';
		}
		elseif($type_menu == 2)	//Используется библиотека shav_FishEye()
		{
			$this->content  = '<div id="fishMenu" class="demo">';

			foreach($this->items as $rec)
			{
				if($rec->isShow == 1)
					$this->content .= $rec->drawFishEyeItem();
			}

			$this->content .= '</div>';
		}
	}


	/** Создает массив подменю из раздела с $id.
	 *	@param $params - массив с параметрами настроки меню (см. документацию)
	 *	@param $id - идентификатор меню.
	 *	@return Возвращает массив со всеми вложенными меню начиная с раздела с идентификатором $id
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	private function getSubPageArrayByID($params, $id = 0)
	{
		if(empty($params))
			return MSG_ARRAYS_0001;

		global $shavDB;

		$sql = 'SELECT * FROM '.$params['pages_table'].' WHERE '.$params['parent_id_name'].' = '.$id.' AND '.$params['parent_id_name'].' > 0';
		$results = $shavDB->get_results($sql);

		$sub_pages = array();
		foreach($results as $rec)
		{
			if($rec[$params['page_id_name']] == $rec[$params['parent_id_name']])
				continue;

			$sub_pages[] = new MENU_Item($rec[$params['title']], $rec[$params['url']], $rec[$params['icon_fld']], (int)$rec[$params['show_page']], $this->getSubPageArrayByID($params, (int)$rec[$params['page_id_name']]));
		}

		return $sub_pages;
	}

	/** Создает меню из данных в таблице, которая находится в БД.
	 *	@param $params - массив с параметрами получения данных из БД (см. документацию).
	 *	@return Возвращает массив со всеми подменю
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 01.02.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createMenuArray($params = array('pages_table' => 'pages', 'page_id_name' => 'pages_id', 'show_page' => 'is_show', 'icon_fld' => 'icons', 'parent_id_name' => 'parent_id', 'url' => 'pages_url','title' => 'pages_title'))
	{
		if(empty($params))
			return MSG_ARRAYS_0001;

		global $shavDB;

		$sql = 'SELECT * FROM '.$params['pages_table'].' WHERE '.$params['parent_id_name'].' = 0';
		$results = $shavDB->get_results($sql);

		$menu = array();
		foreach($results as $rec)
		{
			$sub_page = $this->getSubPageArrayByID($params, $rec[$params['page_id_name']]);
			$menu[] = new MENU_Item($rec[$params['title']], $rec[$params['url']], $rec[$params['icon_fld']], (int)$rec[$params['show_page']], $sub_page);
		}

		return $menu;
	}
}
?>