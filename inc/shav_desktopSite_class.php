<?php
/** @class SHAV_DesktopWindow
 *	@brief Класс создания окна для сайта.
 *	Пример использования:
 *	@code
 * $window1 = new SHAV_DesktopWindow();
 * $window1->title = 'Заголовок';
 * $window1->content = 'Ваш конент';
 * $window1->icon = 'Ссылка на иконку для окна';
 * $window1->idCssClass = 'Идентификатор окна для верстки';
 * $window1->idCssClassIcon = 'Идентификатор окна для системы';
 * $window1->leftPanel = 'Содержимое левой панели окна';
 * $window1->bottomPanel = 'Нижняя панель';
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_DesktopWindow extends SHAV_Object
{
	/** Заголовок окна.*/
	public $title = '';
	
	/** Содержимое окна.*/
	public $content = '';
	
	/** Иконка для окна.*/
	public $icon = '';
	
	/** id окна, для связи с панелью и создания ссылок на это окно.*/
	public $idCssClass = '';
	
	/** id иконки на рабочем столе.*/
	public $idCssClassIcon = '';
	
	/** Содержимое левой панели окна.*/
	public $leftPanel = '';
	
	/** Нижняя панель окна.*/
	public $bottomPanel = '';

	/** Конструктор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_DesktopWindow()
	{}

	/** Создает окно из массива параметров.
	 *	@param $array = array('title'=>'', 'content'=>'', 'icon'=>'', 'idCssClass'=>'','idCssClassIcon'=>'', 'leftPanel'=>'', 'bottom'=>'');
	 *	@return Объект с параметрами из массива.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function createFromArray($array)
	{
		$this->title = $array['title'];
		$this->content = $array['content'];
		$this->icon = $array['icon'];
		$this->idCssClass = $array['idCssClass'];
		$this->idCssClassIcon = $array['idCssClassIcon'];
		$this->leftPanel = $array['leftPanel'];
		$this->bottomPanel = $array['bottom'];
	}

	/** Выводит HTML код окна.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выведет или вернет HTML-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawWindow($isDraw = false)
	{
		$content  = '<div id="'.$this->idCssClass.'" class="abs window">';
		$content .= '<div class="abs window_inner">';
		$content .= '<div class="window_top">';
		$content .= '<span class="float_left">';
		$content .= '<img src="'.$this->icon.'" />'.$this->title;
		$content .= '</span>';
		$content .= '<span class="float_right">';
		$content .= '<a href="#" class="window_min"></a>';
		$content .= '<a href="#" class="window_resize"></a>';
		$content .= '<a href="'.$this->idCssClassIcon.'" class="window_close"></a>';
		$content .= '</span>';
		$content .= '</div>';
		$content .= '<div class="abs window_content">';
		$content .= '<div class="window_aside">'.$this->leftPanel.'</div>';
//		$content .= '<div class="window_main"><table class="data"><tr><td>'.$this->content.'</td></tr></table></div>';
		$content .= '<div class="window_main">'.$this->content.'</div>';
		$content .= '</div>';
		$content .= '<div class="abs window_bottom">'.$this->bottomPanel.'</div>';
		$content .= '</div>';
		$content .= '<span class="abs ui-resizable-handle ui-resizable-se"></span>';
		$content .= '</div>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_DockIcon
 *	@brief Класс для Dock панели сайта.
 *	Пример использования:
 *	@code
 * $icon = new SHAV_DockIcon('icon_info', '#info', '/desktop/images/contacts.png', 'Информация');
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_DockIcon extends SHAV_Object
{
	/** id окна, для которого делается иконка на панели (<имя_иконки>).*/
	public $idCssClass = '';

	/** Ссылка на окно (#<имя_окна>).*/
	public $url = '';

	/** Иконка (ссылка на картинку).*/
	public $icon = '';

	/** Подпись окна.*/
	public $title = '';

	/** Создание Dock панели для сайта.
	 *	@param $cssClass - id окна, для которого делается иконка на панели (<имя_иконки>);
	 *	@param $url - Ссылка на окно (#<имя_окна>);
	 *	@param $icon - Иконка (ссылка на картинку);
	 *	@param $title - Подпись окна.
	 *	@return Объект класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_DockIcon($cssClass, $url, $icon, $title)
	{
		$this->idCssClass = $cssClass;
		$this->url = $url;
		$this->icon = $icon;
		$this->title = $title;
	}
	
	/** Вывод панели для сайта.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выведет или вернет HTML-код
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawPanel($isDraw = false)
	{
		$content = '<li id="'.$this->idCssClass.'"><a href="'.$this->url.'"><img src="'.$this->icon.'">'.$this->title.'</a></li>';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_SystemPanel
 *	@brief Класс системной панели.
 *	Пример использования:
 *	@code
 * $systemPanel = new SHAV_SystemPanel('Show Desktop', $desktop_site->windowsOnDock, $iconPanel = '/desktop/images/icons/icon_22_desktop.png');
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_SystemPanel extends SHAV_Object
{
	/** Иконка кнопки "Свернуть".*/
	public $icon = '';

	/** Подпись иконки.*/
	public $title = '';

	/** Массив иконок для окон, которые могут быть запущенны.*/
	public $dockIcons = array();

	/** Конструктор для создания системной панели.
	 *	@param $titlePanel - заголовок панели;
	 *	@param $dockIconsArr - массив всех окон, которые будут на ней выводится при открытии;
	 *	@param $iconPanel - иконка панели.
	 *	@return Создает объект данного коласса на основе переданных параметров.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_SystemPanel($titlePanel, $dockIconsArr, $iconPanel = '')
	{
		$this->title = $titlePanel;
		$this->dockIcons = $dockIconsArr;
		$this->icon = $iconPanel;
	}

	/** Вывод панели.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выведет или вернет HTML-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawPanel($isDraw = false)
	{
		$content  = '<div class="abs" id="bar_bottom">';
		$content .= '<a class="float_left" href="#" id="show_desktop" title="'.$this->title.'"><img src="'.$this->icon.'" /></a>';
		$content .= '<ul id="dock">';
		foreach($this->dockIcons as $icon)
			$content .= $icon->drawPanel();
		$content .= '<a class="float_right" href="http://lmwshav.org.ua/" title="SHAV Software">SHAV Software</a>';
		$content .= '</ul></div>';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_TopBar
 *	@brief Класс верхней панели.
 *	Пример использования:
 *	@code
 * $menu = array();
 * $menu[] = array('title'=>'Сайт', 'sub_menus'=>array(array('url'=>'/', 'text'=>'Простой сайт'), array('url'=>'/desktop_index.php', 'text'=>'Домой')));
 * $desktop_site->topPanel = new SHAV_TopBar($menu);
 * $menuDB = new SHAV_Menu();
 * $menuDB->createMenuFromArray($sites_pages, 3);
 * $desktop_site->topPanel->createFromMenuClass('Из базы', $menuDB);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_TopBar extends SHAV_Object
{
	/** Массив выпадающего меню.
	 *	@code
	 * 		array('title'=>'', 'sub_menus'=>array(array('url'=>'', 'text'=>'')))
	 *	@endcode*/
	public $menus = array();

	/** Создает объект верхней панели с меню из массива пунктов меню.
	 *	@param $menusArr - массив настроек меню.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_TopBar($menusArr = array())
	{
		$this->menus = $menusArr;
	}

	/** Создать меню из объектов SHAV_Menu.
	 *	@param $name_menu - Название пункта меню;
	 *	@param $menu - Массив объектов меню.
	 *	@return Добавляет пункт с данными из таблицы базы. При этом используются только странички верхнего уровня.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function createFromMenuClass($name_menu, $menu)
	{
		$subMenuArray = array();
		foreach($menu->items as $rec)
		{
			$subMenuArray[] = array('url'=>$rec->lickItem, 'text'=>$rec->titleItem);
		}
		
		$this->menus[] = array('title'=>$name_menu, 'sub_menus'=>$subMenuArray);
	}

	/** Вывод панели.
	 *	@param $isDraw - выводить или вернуть HTML-код;
	 *	@return Выведет или вернет HTML-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawPanel($isDraw = false)
	{
		$content  = '<div class="abs" id="bar_top">';
		$content .= '<span class="float_right" id="clock"></span>';
		$content .= '<ul>';
		foreach($this->menus as $rec)
		{
			$content .= '<li><a class="menu_trigger" href="#">'.$rec['title'].'</a><ul class="menu">';
			foreach($rec['sub_menus'] as $menu)
			{
				$content .= '<li><a href="'.$menu['url'].'">'.$menu['text'].'</a></li>';
			}
			$content .= '</ul></li>';
		}
		$content .= '</ul>';
		$content .= '</div>';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_DesktopIcon
 *	@brief Класс для создания иконок на рабочем столе сайта.
 *	Пример использования:
 *	@code
 * $icon = new SHAV_DesktopIcon('Google.com', '/images/home.png', 'left:20px;top:20px;', 'http://google.com');
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_DesktopIcon extends SHAV_Object
{
	/** Иконка ярлыка.*/
	public $icon = '';

	/** Стиль иконки (пока это просто позиция left:20px;top:20px;).*/
	public $style = '';

	/** Заголовок ярлыка.*/
	public $title = '';

	/** Ссылка на страничку (внешняя или внутренняя).*/
	public $url = '';

	/** Конструктор класса. Создает объект класса с заданными параметрами.
	 *	@param $cTitle - Заголовок ярлыка;
	 *	@param $cIcon - Иконка ярлыка;
	 *	@param $cStyle - Стиль иконки (пока это просто позиция left:20px;top:20px;);
	 *	@param $cUrl - Ссылка на страничку (внешняя или внутренняя).
	 *	@return Объект с заданными параметрами.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_DesktopIcon($cTitle, $cIcon, $cStyle, $cUrl)
	{
		$this->title = $cTitle;
		$this->icon = $cIcon;
		$this->style = $cStyle;
		$this->url = $cUrl;
	}

	/** Вывод панели.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выведет или вернет HTML-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawIcon($isDraw = false)
	{
		$content = '<a class="abs icon" style="'.$this->style.'" href="'.$this->url.'"><img src="'.$this->icon.'" />'.$this->title.'</a>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_Desktop
 *	@brief Класс для создания сайта.
 *	Пример использования:
 *	@code
 * $desktop_site = new SHAV_Desktop();
 * $desktop_site->titleSite = 'Тестовый сайт';
 * $desktop_site->description = 'Тестовый сайт с использованием SHAV PHP Framework';
 * $desktop_site->jsArray[] = $shavJS->drawJS();
 * $desktop_site->topPanel = new SHAV_TopBar($menu);
 * $desktop_site->icons[] = new SHAV_DesktopIcon('Google.com', '/images/home.png', 'left:20px;top:20px;', 'http://google.com');
 * $desktop_site->icons[] = new SHAV_DesktopIcon('Информация', '/images/admin.png', 'left:20px;top:80px;', '#icon_info');
 * $desktop_site->windows[] = $window1;
 * $desktop_site->windowsOnDock[] = new SHAV_DockIcon('icon_info', '#info', '/desktop/images/contacts.png', 'Информация');
 * $desktop_site->systemPanel = new SHAV_SystemPanel('Show Desktop', $desktop_site->windowsOnDock, $iconPanel = '/desktop/images/icons/icon_22_desktop.png');
 * echo $desktop_site->drawSite();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_Desktop extends SHAV_Object
{
	/** Массив окон в сайте.*/
	public $windows = array();
	
	/** Массив окон, которые выводятся в док панели.*/
	public $windowsOnDock = array();
	
	/** Массив иконок (функций) на рабочем столе сайта.*/
	public $icons = array();
	
	/** Заголовок сайта в окне браузера.*/
	public $titleSite = '';
	
	/** Описание сайта для поиска.*/
	public $description = '';
	
	/** Массив дополнительных тегов.*/
	public $metas = array();
	
	/** Массив дополнительных стилей.*/
	public $cssArray = array();
	
	/** Массив дополнительных JavaScript.*/
	public $jsArray = array();
	
	/** Верхняя панель с меню.*/
	public $topPanel = '';
	
	/** Системная панель.*/
	public $systemPanel = '';
	
	/** Конструктор класса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_Desktop()
	{}

	/** Выводит сайт.
	 *	@param $isDraw - выводить или вернуть HTML-код.
	 *	@return Выведет или вернет HTML-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawSite($isDraw = false)
	{
		$content  = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />';
		$content .= '<meta name="description" content="'.$this->description.'" />';

		//Выводим дополнительные мета-теги
		foreach($this->metas as $rec)
			$content .= '<meta name="'.$rec['name'].'" content="'.$rec['content'].'" />';

		$content .= '<title>'.$this->titleSite.'</title>';
		$content .= '<link rel="stylesheet" href="/desktop/stylesheets/html.css" />';
		$content .= '<link rel="stylesheet" href="/desktop/stylesheets/desktop.css" />';

		//Выводим дополнительные css стили подключаемые из файлов
		foreach($this->cssArray as $css)
			$content .= '<link rel="stylesheet" href="'.$css.'" />';

		foreach($this->jsArray as $js)
		{
			$firstLetter = substr($js, 0, 1);
			if($firstLetter == '/')
				$js = substr($js, 1);
			
			if(!is_file($js))
				$content .= $js;
			else
				$content .= '<script src="'.$js.'"></script>';
		}
		
		$content .= '<!--[if gte IE 7]><link rel="stylesheet" href="/desktop/stylesheets/ie.css" /><![endif]-->';
		$content .= '</head><body><div class="abs" id="desktop">';
		//Создаем иконки на рабочем столе.
		foreach($this->icons as $rec)
			$content .= $rec->drawIcon();
		
		//Создаем окна для иконок.
		foreach($this->windows as $rec)
			$content .= $rec->drawWindow();
		
		$content .= '</div>';
		//Создаем верхнюю панель
		$content .= $this->topPanel->drawPanel();
		
		//Создаем нижнюю панель
		$content .= $this->systemPanel->drawPanel();
		$content .= '<script src="/desktop/js/jquery.package.js"></script><script src="/desktop/js/jquery.desktop.js"></script><script>JQD.init_desktop();</script>';
		$content .= '</body></html>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}
?>