<?php
/** @class SHAV_RSS
 *	@brief Класс для работы с RSS историей.
 *	Пример использования:
 *	@code
 * $rss = new SHAV_RSS();
 * $rss->title = '';
 * $rss->link_rss = '';
 * $rss->description = '';
 * $rss->language = '';
 * $rss->image_title = '';
 * $rss->image_url = '';
 * $rss->image_link = '';
 * $rss->image_width = '';
 * $rss->image_height = '';
 * $rss->encoding = 'utf-8';
 * $rss->allItems[] = array('title'=>'', 'link'=>'', 'description'=>'');
 * echo $rss->getRSSContent();
 *	@endcode
 *	Можно использовать ссылку на другой сервер с RSS фидом:
 *	@code
 * $rss = new SHAV_RSS();
 * $rss->pasingRSSFromURL('http://<other_rss_feed_site>');
 * echo $rss->getRSSContent();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 23.11.2009
 *	@date Обновленно: 06.02.2011*/
class SHAV_RSS extends SHAV_Object
{
	/** Заголовок RSS ленты.*/
	public $title = '';

	/** Ссылка на RSS.*/
	public $link_rss = '';

	/** Описание.*/
	public $description = '';

	/** Язык.*/
	public $language = '';

	/** Заголовок картинки для RSS ленты.*/
	public $image_title = '';

	/** Ссылка на картинку.*/
	public $image_url = '';

	/** Ссылка на файл.*/
	public $image_link = '';

	/** Ширина картинки.*/
	public $image_width = '';

	/** Высота картинки.*/
	public $image_height = '';
	
	/** Кодировка RSS ленты.*/
	public $encoding = 'utf-8';

	/** Все записи в ленте.*/
	public $allItems = array('title'=>'', 'link'=>'', 'description'=>'');

	/** Содержимое RSS.*/
	public $document;

	/** Создаем пустой RSS
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_RSS(){}

	/** Создаем новый RSS фид.
	 *	@param $params - массив настроек.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_RSS_WithParams($params = array())
	{
		if(!is_array($params) || empty($params)) return;
		
		$this->title = $params['title'];
		$this->link_rss = $params['link_rss'];
		$this->description = $params['description'];
		$this->language = $params['language'];
		$this->image_title = $params['image_title'];
		$this->image_url = $params['image_url'];
		$this->image_link = $params['image_link'];
		$this->image_width = $params['image_width'];
		$this->image_height = $params['image_height'];
		$this->encoding = $params['encoding'];
	}

	/** Добавляем новый элемент RSS.
	 *	@param $title - заголовок элемента;
	 *	@param $description - описание элемента;
	 *	@param $link - ссылка на полную версию.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function AddItem($title, $description, $link)
	{
		$this->allItems[] = array('title' => $title, 'description' => $description, 'link' => $link);
	}

	/** Возвращает количество записей в RSS.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function getItemCount()
	{
		return count($this->allItems);
	}

	/** Выводим весь RSS.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function getRSSContent()
	{
		$content  = '<?xml version="1.0" encoding="'.$this->encoding.'" ?>';
		$content .= '<rss version="2.0">';
		$content .= '<channel>';
		$content .= '<title>'.$this->title.'</title>';
		$content .= '<link>'.$this->link_rss.'</link>';
		$content .= '<description>'.$this->description.'</description>';
		$content .= '<language>'.$this->language.'</language>';
		$content .= '<image>';
		$content .= '<title>'.$this->image_title.'</title>';
		$content .= '<url>'.$this->image_url.'</url>';
		$content .= '<link>'.$this->image_link.'</link>';
		$content .= '<width>'.$this->image_width.'</width>';
		$content .= '<height>'.$this->image_height.'</height>';
		$content .= '</image>';
	        foreach($this->allItems as $rec)
		{
			$content .= '<item>';
			$content .= '<title>'.$rec["title"].'</title>';
			$content .= '<link>'.$rec["link"].'</link>';
			$content .= '<description><![CDATA['.$rec["description"].']]></description>';
			$content .= '</item>';
		}
		$content .= '</channel></rss>';

		return $content;
	}

	/** Парсить из URL
	 *	@param $url - ссытка для парсинга.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function pasingRSSFromURL($url)
	{
		if($url != '')
			$this->loadParser(file_get_contents($url));
	}

	/** Возвращаем все отпарсенные значения.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function getItems()
	{
		return $this->toArray($this->allItems);
	}

	/** Конвертирует в массив.
	 *	@param $data - данные для преобразования в массив.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	function toArray($data)
	{
		foreach($data as $valueName => $values)
		{
			if(isset($values['value']))
				$values = $values['value'];

			if(is_array($values))
				$valueBlock[$valueName] = $this->toArray($values);
			else
				$valueBlock[$valueName] = $values;
		}

		return $valueBlock;
	}

	/** Загружаем парсинг.
	 *	@param $rss - парсить rss или xml.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	private function loadParser($rss=false)
	{
		if($rss) {
			$this->allItems = array();
			$DOMDocument = new DOMDocument;
			$DOMDocument->strictErrorChecking = false;
			$DOMDocument->loadXML($rss);
			$this->document = $this->extractDOM($DOMDocument->childNodes);
			$this->allItems = $this->getItems();
		}
	}

	/** Парсим RSS.
	 *	@param $nodeList - список элементов дерева данных;
	 *	@param $parentNodeName - главный или нет.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 23.11.2009
	 *	@date Обновленно: 06.02.2011*/
	private function extractDOM($nodeList,$parentNodeName=false)
	{
		$itemCounter = 0;
		foreach($nodeList as $values)
		{
			if(substr($values->nodeName,0,1) != '#')
			{
				if($values->nodeName == 'item')
				{
					$nodeName = $values->nodeName.':'.$itemCounter;
					$itemCounter++;
				}else
					$nodeName = $values->nodeName;

				$tempNode[$nodeName] = array();
				if($values->attributes)
				{
					for($i=0;$values->attributes->item($i);$i++)
						$tempNode[$nodeName]['properties'][$values->attributes->item($i)->nodeName] = $values->attributes->item($i)->nodeValue;
				}

				if(!$values->firstChild)
					$tempNode[$nodeName]['value'] = $values->textContent;
				else
					$tempNode[$nodeName]['value']  = $this->extractDOM($values->childNodes, $values->nodeName);

				if(in_array($parentNodeName, array('channel','rdf:RDF')))
				{
					if($values->nodeName == 'item')
						$this->allItems[] = $tempNode[$nodeName]['value'];
					elseif(!in_array($values->nodeName, array('rss','channel')))
						$this->channel[$values->nodeName] = $tempNode[$nodeName];
				}
			}
			elseif(substr($values->nodeName,1) == 'text')
			{
				$tempValue = trim(preg_replace('/\s\s+/',' ',str_replace("\n",' ', $values->textContent)));
				if($tempValue)
					$tempNode = $tempValue;
			}
			elseif(substr($values->nodeName,1) == 'cdata-section')
				$tempNode = $values->textContent;
		}

		return $tempNode;
	}
}
?>