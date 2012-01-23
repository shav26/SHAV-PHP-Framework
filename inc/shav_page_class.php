<?php

include_once('shav_config.php');

/** @class SHAV_Page
 *	@brief Класс странички.
 *	Пример использования:
 *	@code
 * $page = new SHAV_Page();
 * $tags = array('#TITLE#'=>$title, '#DESCRIPTION#'=>'Тестовый сайт с использованием SHAV PHP Framework', '#KEYWORDS#'=>'SHAV PHP Freamwork', '#JAVA_SCRIPTS#'=>$shavJS->drawJS(), '#HEADER#'=>$header, '#FOOTER#'=>$footer, '#CONTENT#'=>$content, '#LEFT_PANEL#'=>$left, '#RIGHT_PANEL#'=>$right);
 * $page->createPageFromFileWithTags('./tmpls/index.html', $tags);
 * $page->drawPage();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 25.12.2009
 *	@date Обновленно: 09.03.2011*/
class SHAV_Page extends SHAV_Object
{
	/** Кодировка странички.*/
	public $charset = 'utf-8';
	
	/** Заголовок странички.*/
	public $title = '';
	
	/** Описание странички.*/
	public $description = '';
	
	/** Краткое описание странички.*/
	public $keywords = '';
	
	/** Автор странички.*/
	public $author = '';
	
	/** Стили странички.*/
	public $style = '';
	
	/** JavaScript'ы странички.*/
	public $javascript = '';
	
	/** Содержимое boby.*/
	public $content = '';
	
	/** Другие мета-теги странички.*/
	public $other_meta = '';
	
	/** Массив идентификаторов.*/
	public $dynSimpleModal = array();

	/** Конструктор страницы. Создает страницу по массивы параметров.
	 *	@param $params - массив параметров настройки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 16.03.2011*/
	function SHAV_Page($params = array())
	{
		if($params['title'] != '')
			$this->title = '<title>'.$params['title'].'</title>';

		if($params['description'] != '')
			$this->description = '<meta name="description" content="'.$params['description'].'" />';

		if($params['keywords'] != '')
			$this->keywords = '<meta name="keywords" content="'.$params['keywords'].'" />';

		if($params['author'] != '')
			$this->author = '<meta name="author" content="'.$params['author'].'" />';

		if($params['style'] != '')
			$this->style = $this->createStyle($params['style']);
		else
			$this->style = $this->createStyle(array('/css/shav_common.css'));

		if($params['javascript'] != '')
			$this->javascript = $this->createJS($params['javascript']);

		if($params['charset'] != '')
			$this->charset = $params['charset'];

		if($params['content'] != '')
			$this->content = str_replace('#CONTENT#', $params['content'], $this->createPageContent());
		else
			$this->content = $this->createPageContent();
	}

	/** Создает страничку из файла HTML.
	 *	@param $file_full_path - полный путь к файлу.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function createPageFromFile($file_full_path)
	{
		if(file_exists($file_full_path))
			$this->content = file_get_contents($file_full_path);
		else
			$this->content = ERROR_FILES_0003;
	}

	/** Создает страничку из html-фала в котором данные заменяются по тегам.
	 *	@param $file_full_path - полный путь к html-файлу;
	 *	@param $tags_array - массив тегов и их значений.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function createPageFromFileWithTags($file_full_path, $tags_array)
	{
		if(file_exists($file_full_path))
			$this->content = shav_createContentsByTags($tags_array, file_get_contents($file_full_path));
		else
			$this->content = ERROR_FILES_0003;
	}

	/** Создает страничку из файла, котором находится верстка содержимого body, а не вся страничка.
	 *	@param $content - html-текст для вывода в блоке body html-странички (может быть немосредственно самой версткой, или путем на файл с этой версткой);
	 *	@param $tags_array - Массив тегов, которые нужно заменить в шаблоне.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function createPageWithBodyContent($content, $tags_array)
	{
		$text = $content;
		if(is_file($content))
		{
			if(file_exists($content))
				$text = file_get_contents($content);
			else
				$this->content = ERROR_FILES_0003;
		}

		if($text != '' && is_array($tags_array))
		{
			if(!empty($tags_array))
			{
				$this->content = shav_createContentsByTags($tags_array, str_replace('#CONTENT#', $text, $this->createPageContent()));
			}
			else
				$this->content = MSG_ARRAYS_0002;
		}
		else
			$this->content = ERROR_ARRAYS_0100;
	}

	/** Задает мета теги.
	 *	@param $tags_array - массив мета тегов вида array('има_тега'=>'значение', ....)
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function setMetaTags($tags_array)
	{
		if(!is_array($tags_array))
			echo ERROR_ARRAYS_0100;

		$this->other_meta = $this->createOtherMetaTags($tags_array);
	}

	/** Задает массив java-скриптов.
	 *	@param $datas - массив скриптов для подключения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function setJS($datas)
	{
		if(!is_array($datas))
			echo ERROR_ARRAYS_0100;

		$this->javascript = $this->createJS($datas);
	}

	/** Задает массив стилей.
	 *	@param $datas - массив стилей для подключения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function setStyles($datas)
	{
		if(!is_array($datas))
			echo ERROR_ARRAYS_0100;

		$this->style = $this->createStyle($datas);
	}

	/** Выводит страничку на экран
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function drawPage()
	{
		global $shavDB;

		$shavDB->db_close();
		
		$this->content = str_replace('#OTHER_META_TAGS#', $this->other_meta, $this->content);
		$this->content = str_replace('#CONTENT#', '', $this->content);
		$this->content = str_replace('#STYLE_MODAL_WINDOWS#', $this->createCSSSimplModal(), $this->content);
		$this->content = str_replace('#DYN_MODAL_WINDOWS#', $this->createdJSSimpleModal(), $this->content);

		echo $this->content;
	}

	/** Сохраняет страничку в файл.
	 *	@param $file_full_path - полный путь для сохраниения данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	function savePageToFile($file_full_path)
	{
		if(is_writable(dir($file_full_path)))
			file_put_contents($file_full_path, $this->content);
		else
			echo ERROR_FILES_0002;
	}

	/** Создает дополнительные мета-теги на странички.
	 *	@param $data_arr - массив мета-данных ввиде array('<имя>'=>'<значение>')
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 16.03.2011*/
	private function createOtherMetaTags($data_arr)
	{
		if(!is_array($data_arr))
			return MSG_ARRAYS_0002;

		$data = '';
		foreach($data_arr as $key=>$value)
		{
			$data .= '<meta name="'.$key.'" content="'.$value.'" />';
		}

		return $data;
	}

	/** Создает стиль для SimpleModal
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	private function createCSSSimplModal()
	{
		if(empty($this->dynSimpleModal))
			return '';

		$style = '<style>';
		foreach($this->dynSimpleModal as $rec)
		{
			$style .= '#'.$rec.'-content, ';
		}

		$style = substr($style, 0, strlen($style)-2).'{display:none;}</style>';

		return $style;
	}
	
	/** Настраивает модальные окна для плагина SimpleModal
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	private function createdJSSimpleModal()
	{
		if(empty($this->dynSimpleModal))
			return '';

		$prefWindows = '<script type="text/javascript">
		$(document).ready(function () {';
		foreach($this->dynSimpleModal as $rec)
		{
			$prefWindows .= '
				$(\'#'.$rec.' input.basic, #'.$rec.' a.basic\').click(function (e) {
					e.preventDefault();
					$(\'#'.$rec.'-content\').modal();
				});';
		}
		$prefWindows .= '});
		</script>';

		return $prefWindows;
	}

	/** Создает пустую страничку
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 16.03.2011*/
	private function createPageContent()
	{
		$content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset='.$this->charset.'" />';

		if($this->keywords != '')
			$content .= $this->keywords;

		if($this->description != '')
			$content.= $this->description;

		if($this->title != '')
			$content .= $this->title;

		if($this->author != '')
			$content .= $this->author;

		if($this->style != '')
			$content .= $this->style;

		if($this->javascript != '')
			$content .= $this->javascript;

		$content .= '#OTHER_META_TAGS#
		</head>
		<body>
			#CONTENT#
		</body>
		</html>';

		return $content;
	}

	/** Подключает javaScript'ы в страничку.
	 *	@param $datas - массив с скртиптами для подключения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	private function createJS($datas)
	{
		if(!is_array($datas))
			return MSG_ARRAYS_0002;

		$text = '';
		$i = 0; $count = count($datas);
		while($i < $count)
		{
			$firstLetter = substr($datas[$i], 0, 1);
			if($firstLetter == '/')
				$datas[$i] = substr($datas[$i], 1);

			if(is_file($datas[$i]))
				$text .= '<script type="text/javascript" src="/'.$datas[$i].'"></script>';
			else
				$text .= $datas[$i];

			$i++;
		}

		return $text;
	}

	/** Подключает стили к страничке.
	 *	@param $datas - массив стилей.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 25.12.2009
	 *	@date Обновленно: 09.03.2011*/
	private function createStyle($datas)
	{
		if(!is_array($datas))
			return MSG_ARRAYS_0002;

		$text = '';
		$i = 0; $count = count($datas);
		while($i < $count)
		{
			$firstLetter = substr($datas[$i], 0, 1);
			if($firstLetter == '/')
				$datas[$i] = substr($datas[$i], 1);

			if(is_file($datas[$i]))
				$text .= '<link href="/'.$datas[$i].'" rel="stylesheet" rev="stylesheet" type="text/css" />';
			else
				$text .= $datas[$i];

			$i++;
		}

		return $text;
	}
}
?>