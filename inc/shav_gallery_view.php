<?php
/** @class SHAV_GalleryView_Image
 *	@brief Класс элемента галереи.
 *	Привер использования:
 *	@code $element = new SHAV_GalleryView_Image('/images/thumb_image.jpg', '/images/full_image.jpg', 'Название', 'Описание');@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 06.03.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_GalleryView_Image extends SHAV_Object
{
	/** Ссылка на маленькую картинку.*/
	public $thumb = '';

	/** Ссылка на большую картинку.*/
	public $image = '';

	/** Название картинки.*/
	public $title = '';

	/** Описание картинки.*/
	public $desc = '';

	/** HTML-код в самой галереии.*/
	public $content = '';

	/** HTML-код в списке картинок.*/
	public $imgBottom = '';


	/** Конструктор класса.
	 *	@param $thumb - ссылка на маленькую картинку;
	 *	@param $title - название картинки;
	 *	@param $desc - описание картинки.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_GalleryView_Image($thumb = '', $image = '', $title = '', $desc = '')
	{
		$this->thumb = $thumb;
		$this->image = $image;
		$this->title = $title;
		$this->desc = $desc;

		//Создаем HTML-код элемента
		$this->content  = '<div class="panel">';
		$this->content .= '<img src="'.$this->image.'" />';
		$this->content .= '<div class="panel-overlay">';
		$this->content .= '<h2>'.$this->title.'</h2>';
		$this->content .= '<p>'.$this->desc.'</p>';
		$this->content .= '</div>';
		$this->content .= '</div>';

		//Создаем HTML-код элемента списка
		$this->imgBottom .= '<li><img src="'.$this->thumb.'" alt="'.$this->title.'" title="'.$this->title.'" /></li>';
	}

	/** Выводит HTML-код элемента галлереи.
	 *	@param $isDraw - Выводить или вернуть HTML-код элемента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawElement($isDraw = false)
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}

	/** Выводит HTML-код элемента галлереи.
	 *	@param $isDraw - Выводить или вернуть HTML-код элементов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawBottom($isDraw = false)
	{
		if($isDraw == true)
			echo $this->imgBottom;
		else
			return $this->imgBottom;
	}
}


/** @class SHAV_GalleryView
 *	@brief Класс галереи.
 *	Пример использования:
 *	@code
 * $gallery = new SHAV_GalleryView();
 * $gallery->sizePanel = array('width'=>500, 'height'=>200);
 * $gallery->sizeFrame = array('width'=>100, 'height'=>50);
 * $gallery->idGallery = 'Идентификатор галлереи';
 * $gallery->images = array();	//Массив объектов класса SHAV_GalleryView_Image
 * $gallery->drawGallery(true);
 *	@endcode
 *	Второй способ использования:
 *	@code
 * gallery2 = new SHAV_GalleryView('images/Battles/');
 * $gallery2->idGallery = 'gallery2';
 * $gallery2->images = array();
 * $i = 1;
 * while($i < 10)
 * {
 *	 $gallery2->images[] = new SHAV_GalleryView_Image('images/Battles/Battles_0'.($i+1).'.jpg', 'images/Battles/Battles_0'.($i+1).'.jpg', 'Image '.$i, 'Image '.$i);
 *	 $i++;
 * }
 * $gallery2->drawGallery(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 06.03.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_GalleryView extends SHAV_Object
{
	/** Размер панели с миниатюрами.*/
	public $sizePanel = array('width'=>500, 'height'=>200);
	
	/** Размер поля где будет выводится галерея.*/
	public $sizeFrame = array('width'=>100, 'height'=>50);
	
	/** Идентификатор галереи.*/
	public $idGallery = 'photos';
	
	/** Содержимое галереии.*/
	public $contnet = '';
	
	/** Массив картинок.*/
	public $images = array();

	/** Конструктор.
	 *	@param $path - путь к папке с картинками.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_GalleryView($path = '')
	{
		if($path == '')
			return;

		$this->getAllFilesFromFolder($path);
	}

	/** Выводит галерею.
	 *	@param $isDraw - выводить или нет html-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawGallery($isDraw = false)
	{
		global $shavJS;
		
		$shavJS->content .= '<!-- InstanceBeginEditable name="head" -->
		<script type="text/javascript">
		$(document).ready(function(){
			$(\'#'.$this->idGallery.'\').galleryView({
				panel_width: '.$this->sizePanel['width'].',
				panel_height: '.$this->sizePanel['height'].',
				frame_width: '.$this->sizeFrame['width'].',
				frame_height: '.$this->sizeFrame['height'].',
				show_captions: true,
				overlay_opacity: 0.0,
				transition_interval: 0,
				background_color: \'white\'
			});
		});
		</script>
		<!-- InstanceEndEditable -->';
		$this->content = '<div id="'.$this->idGallery.'" class="galleryview">';

		$panels = '';
		$bottom = '';

		foreach($this->images as $img)
		{
			$panels .= $img->drawElement();

			$bottom .= $img->drawBottom();
		}
		//$this->content .= $panels;
		
		$this->content .= '<ul class="filmstrip">';
		$this->content .= $bottom;
		$this->content .= '</ul>';
		$this->content .= '</div>';
		
		if($isDraw = false)
			echo $this->content;
		else
			return $this->content;
	}

	/** Получить все файлы из папки.
	 *	@param $folder - полный путь к папке в которой следует получить все файлы.
	 *	@return Массив из всех файлов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.03.2010
	 *	@date Обновленно: 05.02.2011*/
	private function getAllFilesFromFolder($folder)
	{
		if(empty($folder))
			return;

		$allFiles = shav_GetAllFilesFromFolder($folder, false);

		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension

		$this->images = array();
		foreach($allFiles as $file)
		{
			$typeOfFile = finfo_file($finfo, $file);
			
			if($typeOfFile == 'image/png' || $typeOfFile == 'image/jpg' || $typeOfFile == 'image/jpeg' || $typeOfFile == 'image/gif' ||
				$typeOfFile == 'image/svg' || $typeOfFile == 'image/bmp')
			{
				$element = new SHAV_GalleryView_Image($file, $file, $file, 'Description for file not set');
				$this->images[] = $element;
			}
		}

		finfo_close($finfo);
	}
}
?>