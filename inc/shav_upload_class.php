<?php

/** @class SHAV_Uploader
 *	@brief Класс для поля загрузки файлов в html-формах.
 *	Пример использования:
 *	@code
 * $uploader = new SHAV_Uploader();
 * $uploader->init(1);
 * $uploader->drawContent();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 02.05.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Uploader extends SHAV_Object
{
	/** Название поля в форме.
	 *	@note Используется только при типе 1 - список обычных полей
	 *	@see Функции SHAV_Uploader::init() или SHAV_Uploader::initWithArray()*/
	public $frmName = 'uploadfile';
	
	/** Идентификатор поля на форме.*/
	public $btnId = 'upload';
	
	/** Идентификатор статуса.*/
	public $statusId = 'status';
	
	/** Скрипт для выполнения загрузки файла с использованием Ajax.*/
	public $actionSrcipt = '/upload_file.php';
	
	/** Сообщения об ошибке.*/
	public $errorMsg = 'Ошибка в типе файла!';
	
	/** Сообщения в момент загрузки файла.*/
	public $actionMsg = 'Загрузка...';
	
	/** Типы файлов которые можно загружать.*/
	public $typesOfFiles = array('gif','png','jpg','jpeg');
	
	/** Массив параметров для передачи в скрипт загрузки файлов.*/
	public $datas = array();
	
	/** Ссылка для загрузки файла.*/
	public $title = 'Загрузить файл';
	
	/** HTML-код компонента.*/
	public $content = '';

	/** Конструктор.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Uploader(){}

	/** Инициализация объекта по массиву параметров.
	 *	@param $params - массив параметров для настройки скрипта: array('error_msg'=>'',
																'action_msg'=>'',
																'action_scr'=>'upload_file.php',
																'name'=>'upload',
																'types_of_files'=>array('gif','png',....));
	 *	@param $type - тип компонента: 1 - список обычных полей; 2 - Ajax кнопка для загрузки использую jQuery.
	 *	@return Содержимое для вывода на экран.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	function initWithArray($params, $type = 1)
	{
		if(!is_array($params))
		{
			echo ERROR_ARRAYS_0100;
			return;
		}
		
		if(empty($params) && $isDraw == true)
		{
			echo MSG_ARRAYS_0002;
			return;
		}

		$this->frmName =  $params['name'];
		$this->btnId = $params['id'];
		$this->statusId = $params['status_id'];
		$this->actionSrcipt = $params['action_scr'];
		$this->errorMsg = $params['error_msg'];
		$this->actionMsg = $params['action_msg'];
		$this->typesOfFiles = $params['types_of_files'];
		$this->datas = $params['datas'];

		switch($type)
		{
			case 1: $this->content = $this->createContent();
				break;
			case 2: $this->content = $this->createContentAjax();
				break;
		}
	}

	/** Создание контента по-умолчанию
	 *	@param $type - тип компонента: 1 - список обычных полей; 2 - Ajax кнопка для загрузки использую jQuery.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	function init($type = 1)
	{
		switch($type)
		{
			case 1: $this->content = $this->createContent();
				break;
			case 2: $this->content = $this->createContentAjax();
				break;
		}
	}

	/** Выводит html-код поля для загрузки файлов.
	 *	@param $isDraw - выводить или нет html-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawContent($isDraw = false)
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}

	/** Выводит html-код поля для загрузки файлов c использованием JQuery и Ajax.
	 *	@param $isDraw - выводить или нет html-код.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawAjaxContent($isDraw = false)
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
	
	/** Создает список для загрузки файлов N
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createContent()
	{
		global $shavJS;
		$shavJS->content .= '<script type="text/javascript">';
		$shavJS->content .= 'function changeCountField_'.$this->frmName.'(){
					public str = "";
					public cont = "";
					public i = 0;
					$("input#count").each(function () {
						str = $(this).val();
					});

					for(i=0; i<(str-1); i++)
					{
						cont = cont + "<tr><td><input type=\"file\" name=\"file_" + i + "\" id=\"file_"+i+"\" value=\"\" /></td></tr>";
					}

					$("#content_file_list").html(cont);
				}';
		$shavJS->content .= '</script>';
		
		$content  = '<table width="100%">';
		$content .= '<tr><td clospan="2" align="center"><label for="'.$this->frmName.'">'.$this->title.'</td></tr>';
		$content .= '<tr><td clospan="2">Количество файлов для загрузки: <input type="text" name="count" id="count" size="5" value="1" onChange="changeCountField_'.$this->frmName.'();"/></td></tr>';
		$content .= '<tr><td clospan="2"><div width="100%" id="content_file_list"><tr><td><input type="file" name="file_1" id="file_1" value="" /></td></tr></div></td></tr>';
		$content .= '</tr>';
		$content .= '</table>';

		return $content;
	}
	
	/** Создает содержимое для вывода
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 02.05.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createContentAjax()
	{
		global $shavJS;

		$dataString = '[';
		$i = 0; $count = count($this->typesOfFiles);
		foreach($this->typesOfFiles as $type)
		{
			if($i < ($count-1))
				$dataString .= '\''.$type.'\', ';
			else
				$dataString .= '\''.$type.'\'';

			$i++;
		}
		$dataString .= ']';

		$paramString = '';
		$i = 0; $count = count($this->datas);
		foreach($this->datas as $key=>$value)
		{
			if($i < ($count-1))
				$paramString .= $key.':"'.$Value.'", ';
			else
				$paramString .= $key.':"'.$Value.'"';
			
			$i++;
		}


 /*	params: {'.$paramString.'},
 
 // validation
 // ex. [\'jpg\', \'jpeg\', \'png\', \'gif\'] or []
 allowedExtensions: '.$dataString.',
 sizeLimit: 0, // max size
  m inSizeLimit: 0, // min size
  
  // set to true to output server response to console
  debug: true,
  
  // events
  // you can return false to abort submit
  onSubmit: function(id, fileName){
	  
  },
  onProgress: function(id, fileName, loaded, total){
	  
  },
  onComplete: function(id, fileName, responseJSON){
	  //$(\'#'.$this->statusId.'\').html(responseJSON);
  },
  onCancel: function(id, fileName){
	  $(\'#'.$this->statusId.'\').html(\'Невозможно загрузить файл\'+fileName);
  },
  
  messages: {
	  
  },
  
  showMessage: function(message){ alert(message); }*/

		$shavJS->content .= '<script type="text/javascript">
			function createUploader(){
				var uploader = new qq.FileUploader({
					element: document.getElementById(\''.$this->btnId.'\'),
					listElement: document.getElementById(\''.$this->statusId.'\'),
					action: \''.$this->actionSrcipt.'\',
					// additional data to send, name-value pairs
				});
			}
			window.onload = createUploader;
		</script>';

				$content  = '<div id="'.$this->btnId.'"></div>
				<ul id="'.$this->statusId.'"></ul>';
		return $content;
	}
}
?>