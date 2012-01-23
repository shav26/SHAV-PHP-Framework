<?php
/** @class SHAV_TemplateItem
 *	@brief Текущий темплейт.
 *	Пример использования:
 *	@code
 * $item = new SHAV_TemplateItem('/tmpl/index.html');
 * $item->drawTemplate(true);	//Выводим содержимое темплейта.
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 15.02.2011
 *	@date Обновленно: */
class SHAV_TemplateItem extends SHAV_Object
{
	/** Файл для хранения темплейта.*/
	public $fileName = '';

	/** Основная часть.*/
	public $content = '';

	/** Конструктор класса.
	 *	@param $file - путь к файлу с темплейтом.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function SHAV_TemplateItem($file = "")
	{
		if($file != "")
		{
			$this->fileName = $file;
			$this->content = file_get_contents($file);
			$this->content = str_replace("#", "$", $this->content);
		}
	}

	/** Выводит текущий компонент.
	 *	@param $isDraw - Выводить или нет HTML-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function drawTemplate($isDraw = false)
	{
		$data = '<textarea name="content" id="content" cols="97">'.$this->content.'</textarea>';

		if($isDraw == true)
			echo $data;
		else
			return $data;
	}

	/** Сохраняет содержимое темплейта в файл.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function saveToFile()
	{
		$this->content = str_replace("$", "#", $this->content);
		if(!file_put_contents($this->fileName, $this->content))
			echo '<d class="error">Ошибка записи в файл. Возможно у Вас не достаточно прав для записи проверте права и повторите попутку.</b>';
	}

	/** Удалить текущий темплейт.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function delete()
	{
		unlink($this->fileName);
	}
}


/** @class SHAV_TemplateEditor
 *	@brief Класс редактора темплейтов.
 *	Пример использования:
 *	@code
 * $tmpl = new SHAV_TemplateEditor('/tmpl/');
 * $tmpl->draw(true);	//Выводим интерфейс редактора.
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 15.02.2011
 *	@date Обновленно: */
class SHAV_TemplateEditor extends SHAV_Object
{
	/** Массив всех доступных темплейтов.*/
	public $templetes = array();

	/** HTML-код редактора, который можно вывести в окно браузера.*/
	public $content = '';

	/** Конструктор класса
	 *	@param $tmplsPath - Путь к папке с темплейтами.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function SHAV_TemplateEditor($tmplsPath = "../tmpls/")
	{
		if($tmplsPath != "")
		{
			$this->getAllFilesFromFolder($tmplsPath);
		}
		
		$this->createEditor();
	}

	/** Выводит на экран редактор.
	 *	@param $isDraw - выводить или нет HTML-код редактора.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	function draw($isDraw = false)
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}

	/** Создает редактор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	private function createEditor()
	{
		if($_POST['view'])
		{
			$idTmpls = (int)$_POST['tmpl'];
			$index = ($idTmpls-1);
			if($index >= 0)
			{
				$tmpl = $this->templates[($idTmpls-1)];
				$contentTmpl = $tmpl->drawTemplate().'<input type="hidden" id="tmpl_id" name="tmpl_id" value="'.$idTmpls.'" /><input type="submit" id="save" name="save" value="Сохранить" /><input type="submit" id="del" name="del" value="Удалить" />';
			}
		}
		else if($_POST['new'] && $_POST['content'] != '' && $_POST['file'] != '')
		{
			$tmpl = new SHAV_TemplateItem();
			$tmpl->content = $_POST['content'];
			$tmpl->fileName = $_POST['file'];
			$tmpl->saveToFile();
			
			header("Location: http://".$_SERVER['HTTP_HOST']."/admin/edit_templates.php");
		}
		else if($_POST['create'])
		{
			$contentTmpl = 'Файл шаблона:&nbsp;<input type="text" name="file" id="file" value="../tmpls/some_file_name.html" size="73" /><textarea name="content" id="content" cols="97">'.$this->loadDefaultHTML().'</textarea><input type="submit" name="new" id="new" value="Создать" />';
		}
		else if($_POST['save'] && $_POST["content"] != "" && (int)$_POST['tmpl_id'] > 0)
		{
			$idTmpls = (int)$_POST['tmpl_id'];
			$tmpl = $this->templates[($idTmpls-1)];
			$tmpl->content = $_POST['content'];
			$tmpl->saveToFile();
		}
		else if($_POST['del'] && (int)$_POST['tmpl_id'] > 0)
		{
			$idTmpls = (int)$_POST['tmpl_id'];
			$tmpl = $this->templates[($idTmpls-1)];
			$tmpl->delete();
			header("Location: http://".$_SERVER['HTTP_HOST']."/admin/edit_templates.php");
		}
		
		$listOfTmpls  = 'Список доступный шаблонов:&nbsp;<select id="tmpl" name="tmpl">';
		$listOfTmpls .= '<option value="0">Не указан</option>';
		$i = 1;
		foreach($this->templates as $tmpl)
		{
			if($i == $idTmpls)
				$listOfTmpls .= '<option value="'.$i.'" selected>'.$tmpl->fileName.'</option>';
			else
				$listOfTmpls .= '<option value="'.$i.'">'.$tmpl->fileName.'</option>';

			$i++;
		}
		$listOfTmpls .= '</select>';
		
		$this->content  = '<div align="center"><div class="tmpl_editor"><p><i>Для работы с темплейтами нужно саблюдать одно правило. Каждый тег информации должен начинаться с символа <b>$</b> (доллар). При сохранении нового или изменненого темплейта он будет перекадирован в "#". Например, тег для описания заголовка страницы будет указан так: <b>$TITLE$</b></i></p>';
		$this->content .= '<form method="POST" action="">'.$listOfTmpls.'<input type="submit" id="view" name="view" value="Посмотреть" /><input type="submit" name="create" id="create" value="Создать" /></form>';
		if($contentTmpl != "")
			$this->content .= '<form method="POST" action="">'.$contentTmpl.'</form>';
		$this->content .= '</div></div>';
	}

	/** Получить все файлы из папки.
	 *	@param $folder - полный путь к папке в которой следует получить все файлы.
	 *	@return Массив из всех файлов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	private function getAllFilesFromFolder($folder)
	{
		if(empty($folder))
			return;

		$allFiles = shav_GetAllFilesFromFolder($folder, true);

		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension

		$this->templates = array();
		foreach($allFiles as $file)
		{
			$typeOfFile = finfo_file($finfo, $file);
			if($typeOfFile == 'text/html')
			{
				$element = new SHAV_TemplateItem($file);
				$this->templates[] = $element;
			}
		}

		finfo_close($finfo);
	}

	/** Загружает заготовку верстки.
	 *	@return HTML-код заготовки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.02.2011
	 *	@date Обновленно: */
	private function loadDefaultHTML()
	{
		$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>$TITLE$</title>
		<meta name="description" content="$DESCRIPTION$">
		<meta name="keywords" content="$KEYWORDS$">
		<link href="/css/shav_common.css" rel="stylesheet" rev="stylesheet" type="text/css" />
		<link href="/js/simplemodal/basic.css" rel="stylesheet" rev="stylesheet" type="text/css" />
		$STYLE_MODAL_WINDOWS$
		$JAVA_SCRIPTS$
		</head>
		<body>
		<!-- Ваша верстка-->
		<script type="text/javascript" src="/js/simplemodal/jquery_simplemodal.js"></script>
		<script type="text/javascript" src="/js/simplemodal/config_static_windows.js"></script>
		$DYN_MODAL_WINDOWS$
		</body>
		</html>';

		return $content;
	}
}
?>