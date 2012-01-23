<?php
/** @class SHAV_Object
 *	@brief Класс объекта.
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 15.11.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_Object
{
	/** Имя класса.*/
	public $name = 'class';

	/** Конструктор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_Object()
	{}

	/** Выводит информацию о переменных класса.
	 *	@return информацию о переменных класса и методов.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawHelp()
	{
		$content  = '<h1>Помощь по использованию.</h1><div>';
		$content .= drawObject($this, false).'</div>';

		echo $content;
	}
}

/** @class SHAV_File
 *	@brief Класс описывающий файл.
 *	Пример использования:
 *	@code
 * $file = new SHAV_File('/uploads/image.jpg');
 * $file->saveFileToPath('/uploads/', 'new_image');
 * $file->deleteFile();	//Удалить текущий файл
 *	@endcode
 *	Так же можно использовать глобальню переменную $_FILES, например, так:
 *	@code
 * $file = new SHAV_File($_FILES['image']);	//image - это идентификатор параметра из формы для загрузки файла.
 * $file->saveFileToPath('/uploads/', 'new_image');
 * echo 'Saved to: '.$file->full_path_to_file; //Выводим полный путь к новому файлу после загрузки.
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 15.11.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_File extends SHAV_Object
{
	/** Путь к папке где находится файл.*/
	public $dir_name = '';
	
	/** Имя файла без разширения.*/
	public $base_name = '';
	
	/** Старое имя файла (используется при переименновании или перемещении).*/
	public $old_filename = '';
	
	/** Тип файла.*/
	public $type = '';
	
	/** Имя файла.*/
	public $filename = '';
	
	/** Разширение файла.*/
	public $extension = '';
	
	/** Дата последнего доступа к файлу.*/
	public $last_access = 0;
	
	/** Дата последней модификации.*/
	public $last_modify = 0;
	
	/** Права доступа.*/
	public $permission = '';
	
	/** Размер файла в байтах.*/
	public $sizeInBytes = 0;
	
	/** Размер файла для вывода.*/
	public $sizeString = '';
	
	/** Полный путь к файлу*/
	public $full_path_to_file = '';

	/** Создан ли объект из массива параметров.*/
	public $fromArray = false;

	/** Конструктор класса.
	 *	@param full_path - полный путь к файлу, информацию о котором следует получить.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_File($full_path = '')
	{
		if($full_path == '')
			return;
		
		if(is_array($full_path) && !empty($_FILES))
		{
			$array = pathinfo($full_path['name']);
			$array_tmp = pathinfo($full_path['tmp_name']);
			$this->dir_name = $array['dirname'];
			$this->base_name = $array['basename'];
			$this->type = $full_path['type'];
			$this->extension = $array['extension'];
			$this->old_filename = $array_tmp['basename'];
			$this->filename = $array['filename'];
			$this->last_access = fileatime($full_path['tmp_name']);
			$this->last_modify = filemtime($full_path['tmp_name']);
			$this->permission = $this->createPermissionData($full_path['tmp_name']);
			$this->sizeInBytes = $full_path['size'];
			$this->sizeString = $this->getFormatSizeFromBytes($this->sizeInBytes);
			$this->full_path_to_file = '';
			$this->fromArray = true;
		}
		else
		{
			$array = pathinfo($full_path);
			$this->dir_name = $array['dirname'];
			$this->base_name = $array['basename'];
			$this->extension = $array['extension'];
			$this->old_filename = $array['filename'];
			$this->filename = $array['filename'];
			$this->last_access = fileatime($full_path);
			$this->last_modify = filemtime($full_path);
			$this->permission = $this->createPermissionData($full_path);
			$this->sizeInBytes = filesize($full_path);
			$this->sizeString = $this->getFormatSizeFromBytes($this->sizeInBytes);
			$this->full_path_to_file = $this->dir_name.'/'.$this->filename.'.'.$this->extension;
		}
	}

	/** Сохранит файл в новую папку.
	 *	@param $new_path - путь к новому местонахождению файла;
	 *	@param $new_name - новое имя файла (если не указывать будет использованно старое имя файла).
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	function saveFileToPath($new_path, $new_name = '')
	{
		if($new_name != '')
			$this->filename = $new_name;

		if(!is_dir($new_path))
			mkdir($new_path);
		
		if(!is_writable($new_path))
		{
			echo '<b class="error">Ошибка: У вас недостатьчно прав для записи в указанную папку. Проверте права на папку. [SHAV_File::saveFileToPath($new_path, $new_name = \'\')]</b>';
			return false;
		}
		
		if(!file_exists($new_path.$this->filename.$this->extension))
		{
			if($this->fromArray == true)
			{
				$from = '/tmp/'.$this->old_filename;
				$to = $new_path.$this->filename.$this->extension;
				if($this->extension != '')
					$to = $new_path.$this->filename.'.'.$this->extension;
			}
			else
			{
				$from = $this->dir_name.'/'.$this->old_filename.$this->extension;
				if($this->extension != '')
					$from = $this->dir_name.'/'.$this->old_filename.'.'.$this->extension;
				
				$to = $new_path.$this->filename.$this->extension;
				if($this->extension != '')
					$to = $new_path.$this->filename.'.'.$this->extension;
			}

			if(move_uploaded_file($from, $to))
			{
				$this->dir_name = $new_path;
				$this->full_path_to_file = $this->dir_name.$this->filename.'.'.$this->extension;
				return true;
			}
		}

		return false;
	}

	/** Удалить файл
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	function deleteFile()
	{
		if(file_exists($this->dir_name.'/'.$this->filename.$this->extension))
			unlink($this->dir_name.'/'.$this->filename.$this->extension);
	}

	/** Создает строку описывающую права доступа к файлу.
	 *	@param $full_path - полный путь к файлу.
	 *	@return Строка вида -rw-r--r--
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	private function createPermissionData($full_path)
	{
		$perms = fileperms($full_path);
		
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}
		
		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		(($perms & 0x0800) ? 's' : 'x' ) :
		(($perms & 0x0800) ? 'S' : '-'));
		
		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		(($perms & 0x0400) ? 's' : 'x' ) :
		(($perms & 0x0400) ? 'S' : '-'));
		
		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		(($perms & 0x0200) ? 't' : 'x' ) :
		(($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}

	/** Получаем строку размера файла в удобном для чтение виде.
	 *	@param $bytes - размер файла в байтах.
	 *	@return Строка вида 200 MB
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 15.11.2010
	 *	@date Обновленно: 06.02.2011*/
	private function getFormatSizeFromBytes($bytes)
	{
		if ($bytes < 1024) return $bytes.' B';
		elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
		elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
		elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
		else return round($bytes / 1099511627776, 2).' TB';
	}
}
?>