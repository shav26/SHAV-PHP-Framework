<?php
include_once("shav_config.php");

/** @class SHAV_XmlItem
 *	@brief Класс элемента.
 *	Пример использования:
 *	@code
 * $data = array('key'=>'', 'attributes'=>array('name'=>'value', ....), 'value'=>'');
 * $item = new SHAV_XmlItem($data);
 * $item->drawItem(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 28.02.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_XmlItem extends SHAV_Object
{
	/** Название ключа.*/
	public $keyName = '';
	
	/** Атрибуты ключа.*/
	public $attributes = array();
	
	/** Содержимое ключа, может быть массивом.*/
	public $value = '';
	
	/** Содержимое для вывода.*/
	public $content = '';

	/** Конструктор.
	 *	@param $dataArr - массив данных вида:
	 *	@code $data = array('key'=>'', 'attributes'=>array('name'=>'value', ....), 'value'=>'');@endcode
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_XmlItem($dataArr = array())
	{
		if(empty($dataArr)) return;
		
		$this->keyName = $dataArr['key'];
		$this->attributes = $dataArr['attributes'];
		$this->value = $dataArr['value'];
	}

	/** Вывод на экран.
	 *	@param $isDraw - выводить или нет html-код элемента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawItem($isDraw = false)
	{
		$artbStr = '';
		if(!empty($this->attributes))
		{
			foreach($this->attributes as $key=>$value)
				$artbStr .= ' '.$key.'="'.$value.'"';
		}
		
		$content  = '<'.$this->keyName.$artbStr.'>';
		$content .= $this->createItamContentValue();
		$content .= '</'.$this->keyName.'>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Создаем рекурсивно элементов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createItamContentValue()
	{
		if(!is_array($this->value))
			return $this->value;
		elseif(is_array($this->value))
		{
			$content = '';
			foreach($this->value as $vl)
			{
				$tmp = new SHAV_XmlItem($vl);
				$content .= $tmp->drawItem();
			}

			return $content;
		}
	}
}

/** @class SHAV_XML
 *	@brief Класс XML.
 *	Пример использования:
 *	@code
 * $data = array('key'=>'', 'attributes'=>array('name'=>'value', ....), 'value'=>'');
 * $xml = new SHAV_XML('cp1251');
 * $xml->createXMLFromSQL('SELECT * FROM users');
 * $xml->drawXML();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 28.02.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_XML extends SHAV_Object
{
	/** Текст полученный в результате работы функций класса для выводна в файл или окно браузера.*/
	public $xmlContent = '';

	/** Кодировка содержимого.*/
	public $encoding = 'utf-8';

	/** Все эелемнты списка.*/
	public $items = array();

	/** Конструктор класса.
	 *	@param $encoding - кодировка данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_XML($encoding = 'utf-8')
	{
		$this->encoding = $encding;
	}

	/** Создаем XML из массива.
	 *	@param $prmArray - массив данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function createXMLFromArray($prmArray = array())
	{
		$this->xmlContent  = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$this->xmlContent .= '<root>';
		$this->xmlContent .= $this->createXMLItems($prmArray);
		$this->xmlContent .= '</root>';
	}

	/** Выводим содержимое XML.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawXML()
	{
		$this->xmlContent  = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$this->xmlContent .= '<root>';
		foreach($this->items as $item)
		{
			$this->xmlContent .= $item->drawItem();
		}
		$this->xmlContent .= '</root>';

		echo $this->xmlContent;
	}

	/** Создание XML из SQL-запроса.
	 *	@param $query - SQL-запрос.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function createXMLFromSQL($query = '')
	{
		global $shavDB;

		$results = $shavDB->get_results($query);

		$this->xmlContent  = '<?xml version="1.0" encoding="utf-8"?>';
		$this->xmlContent .= '<root>';
		$res = array();
		foreach($results as $key=>$value)
		{
			$res['item_'.$key] = $value;
		}
		$this->xmlContent .= $this->createXMLItems($res);
		$this->xmlContent .= '</root>';
	}

	/** Вывод XML данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawXMLContent()
	{
		echo $this->xmlContent;
	}

	/** Сохраняем данные в файл.
	 *	@param $url - файл для сохранения контента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function saveToFile($url)
	{
		$dir = dirname($url);

		if(file_exists($dir))
		{
			if(is_writable($dir))
			{
				file_put_contents($url, $this->xmlContent);
			}
			else
			{
				echo ERROR_FILES_0002;
			}
		}
		else
		{
			mkdir($dir);
			file_put_contents($url, $this->xmlContent);
		}
	}

	/** Создаем список всех параметров и значений.
	 *	@param $data - массив параметров.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 28.02.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createXMLItems($data = array())
	{
		$content = '';
		foreach($data as $key => $value)
		{
			if(is_array($value))
				$content .= '<'.$key.'>'.$this->createXMLItems($value).'</'.$key.'>';
			else
				$content .= '<'.$key.'>'.strip_tags($value).'</'.$key.'>';
		}

		return $content;
	}
}
?>