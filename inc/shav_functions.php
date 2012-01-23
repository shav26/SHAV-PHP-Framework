<?php
/** Дополнительные функции для работы с графикой, почтой и текстом.

/** Проверяет тип файла и возвращает переменныю для опций вида: $image или $file.
	@param $type - тип из переменной FILES.
	@returns $images - если файл - картинка;
			$file - если это другой файл.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_getOptionFileType($type)
{
	if($type == '') return '';

	if($type == '.png' || $type == '.jpg' || $type == '.jpeg' || $type == '.bmp' || $type == '.tif')
	{
		return '$image';
	}

	if($type == ".zip" || $type == ".rar" || $type == ".doc" || $type == ".pdf" || $type == ".rtf" || $type == ".docx")
	{
		return '$file';
	}

	return '';
}

/** Проверяет тип файла.
	@param $type - тип файла для проверки
	@returns true - если подходит;
			false - если не подходит.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_check_file_type($type)
{
	if($type == "image/gif" || $type == "image/jpeg" || $type == "image/pjpeg" || $type == "application/x-zip
" || $type == "application/zip" || $type == "application/x-rar" || $type == "image/png" || $type == "image/tif" || $type == "image/jpg")
		return true;
	else
		return false;
}

/** Изменяет размер картинки.
	@param $in_file — путь к jpg-изображению, которое надо изменить;
	@param $out_file — место и имя новой картинки;
	@param $percent — процент, который будет составлять новая каринка от исходной.
	@return Измененный файл.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_jpgresize($in_file, $out_file, $percent)
{
	$old_img = imagecreatefromjpeg($in_file);
	$old_img_size = getimagesize($in_file);

	$new_size_w = $old_img_size[0]*$percent/100;
	$new_size_h = $old_img_size[1]*$percent/100;
	$img_new = imagecreatetruecolor($new_size_w, $new_size_h);

	imagecopyresampled($img_new, $old_img, 0, 0, 0, 0, $new_size_w, $new_size_h, $old_img_size[0], $old_img_size[1]);
	imageinterlace($img_new, 1);
	imagejpeg($img_new, $out_file, 100);

//	copy($in_file, $out_file);
	//echo 'old_img='.$in_file.' img_new='.$out_file.'<br />';

	imagedestroy($img_new); # убить объект, но не файл
	imagedestroy($old_img);
}


/** Изменяет размер картинки и преобразует формат.
	@param $in_file - путь к jpg-изображению, которое надо изменить;
	@param $out_file - место и имя новой картинки;
	@param $new_size_w - ширина новой картинки в пикселях;
	@param $new_size_h - высота новой картинки в пикселях.
	@return Измененный файл.
	@author Шаповалов Антон.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_imageresize($in_file, $out_file, $new_size_w, $new_size_h)
{
     $ext = strtolower(substr($in_file,-3));
     if($ext == 'jpg')     $old_img = imagecreatefromjpeg($in_file);
     if($ext == 'png')     $old_img = imagecreatefrompng($in_file);
     if($ext == 'gif')     $old_img = imagecreatefromgif($in_file);

     $old_img_size = getimagesize($in_file);

     $img_new = imagecreatetruecolor($new_size_w, $new_size_h);

     imagecopyresampled($img_new, $old_img, 0, 0, 0, 0, $new_size_w, $new_size_h, $old_img_size[0], $old_img_size[1]);
     //imageinterlace($img_new, 1);

     $ext = strtolower(substr($out_file,-3));
     if($ext == 'jpg')     imagejpeg($img_new, $out_file, 100);
     if($ext == 'png')     imagepng($img_new, $out_file);
     if($ext == 'gif')     imagegif($img_new, $out_file);
}

/** Функция для изменения размера изображения и наложения водяного знака на него.
	@param $outfile -  путь к файлу, который получится после преобразования;
	@param $infile - путь к файлу, который преобразуем;
	@param $watermark - путь к файлу с фодянным знаком;
	@param $neww - ширина в px, к которой преобразуем;
	@param $quality - качество изображения в %.
	@return Создает картинку заданного размера и с водяным знаком.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_ImageResizeWithWatermark($outfile, $infile, $watermark, $neww = 0, $quality = 100)
{
	if(!file_exists($infile))
	{
		$tags = array('$FILE$'=>$infile);
		echo shav_createContentsByTags($tags, ERROR_FILES_0003);
		return;
	}

	if(!file_exists($watermark))
	{
		$tags = array('$FILE$'=>$watermark);
		echo shav_createContentsByTags($tags, ERROR_FILES_0003);
		return;
	}

	if($neww == 0)
	{
		//Получаем исходный файл
		$ext = strtolower(substr($infile, -3));
		if($ext == 'jpg')	$im = imagecreatefromjpeg($infile);
		if($ext == 'png')	$im = imagecreatefrompng($infile);
		if($ext == 'gif')	$im = imagecreatefromgif($infile);

		//Добовляем фодянной знак
		$ext = strtolower(substr($watermark, -3));
		if($ext == 'jpg')	$im_logo = imagecreatefromjpeg($watermark);
		if($ext == 'png')	$im_logo = imagecreatefrompng($watermark);
		if($ext == 'gif')	$im_logo = imagecreatefromgif($watermark);

		imagecopy($im, $im_logo, 0, 0, 0, 0, imagesx($im_logo), imagesy($im_logo));

		//Сохраняем новое изображение.
		imagejpeg($im, $outfile, $quality);

		//Очищаем все временное
		imagedestroy($im);
		imagedestroy($im_logo);
	}
	else
	{
		$im = imagecreatefromjpeg($infile);
		$newh = $neww * imagesy($im) / imagesx($im);

		$im1 = imagecreatetruecolor($neww, $newh);
		imagecopyresampled($im1, $im, 0, 0, 0, 0, $neww, $newh, imagesx($im), imagesy($im));

		if($neww >= 140)//добавляем водяной знак на изображения больше среднего размера
		{              //путь к изображению с водяным знаком
			$ext = strtolower(substr($watermark, -3));
			if($ext == 'jpg')	$im_logo = imagecreatefromjpeg($watermark);
			if($ext == 'png')	$im_logo = imagecreatefrompng($watermark);
			if($ext == 'gif')	$im_logo = imagecreatefromgif($watermark);

			imagecopy($im1, $watermark, 0, 0, 0, 0, 150, 25);
		}
		imagejpeg($im1,$outfile,$quality);
		imagedestroy($im);
		imagedestroy($im1);
	}
}

/** Получить массив со всеми файлами из указанной директории.
	@param $folder - Путь к папке, содержимое которой следует вывести;
	@param $recursive - Проверять вложеные папки или нет (поумолчанию - да).
	@return Массив со всеми файлами.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_GetAllFilesFromFolder($folder, $recursive = true)
{
	if(empty($folder))
		return;
	
	$files = array();
	if($dir = @opendir($folder))
	{
		while (($f = readdir($dir)) !== false)
		{
			if($f > '0' and filetype($folder.$f) == "file")
			{
				$files[] = $folder.$f;
			}
			elseif($f > '0' and filetype($folder.$f) == "dir" && $recursive == true)
			{
				$tmp = shav_GetAllFilesFromFolder($folder.$f.'/');
				foreach($tmp as $f1)
					$files[] = $f1;
			}
		}
		
		closedir($dir);
	}

	return $files;
}

/** Закачивает файл в папку на сервере.
	@param $to - папка на сервере, куда копируем файл;
	@param $file_name - имя файла для сохранения;
	@param $field - поле формы с файлом.
	@return ошибка если файл не возможно загрузить или путь к загруженному файлу.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_upload_files($to, $file_name, $field = 'file')
{
	if(empty($_FILES))
		return '<pre>'.ERROR_FILES_0001.'</pre>';

	if(!is_dir($to))
	{
		mkdir($to);
	}

	if(is_writable($to))
	{
		/*$file = $_FILES[$field]["name"];

		$file = str_replace(' ', '_', $file);
		$file = str_replace('(', '', $file);
		$file = str_replace(')', '', $file);
		$file = str_replace('[', '', $file);
		$file = str_replace(']', '', $file);
		$file = str_replace('{', '', $file);
		$file = str_replace('}', '', $file);
		$file = str_replace('@', '', $file);
		$file = str_replace('#', '', $file);
		$file = str_replace('$', '', $file);
		$file = str_replace('^', '', $file);
		$file = str_replace('&', '', $file);
		$file = str_replace('*', '', $file);
		$file = str_replace('=', '', $file);
		$file = str_replace('+', '', $file);*/

		if (!file_exists($to.$file_name))
		{
			move_uploaded_file($_FILES[$field]["tmp_name"], $to.$file_name);
			return $to.$file_name;
		}
	}
	else
	{
		return '<pre>'.ERROR_FILES_0002.'</pre>';
	}
}

/** Очищает папки, удаляя все файлы и сами папки.
	@param $directory - папка на сервере, для удаления.
	@return ошибка если файл не возможно загрузить.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_delete_folders($current_dir)
{
	if($dir = @opendir($current_dir))
	{
		while (($f = readdir($dir)) !== false)
		{
			if($f > '0' and filetype($current_dir.$f) == "file")
			{
				unlink($current_dir.$f);
			}
			elseif($f > '0' and filetype($current_dir.$f) == "dir")
			{
				shav_delete_folders($current_dir.$f."\\");
			}
		}
		closedir($dir);
		rmdir($current_dir);
	}
}

/** Получаем массим ввиде название параметра и его значения.
	@param $string - строка со сначением и названием параметра;
	@param $symbol - символ-разделитель.
	@return Массив типа array(NAME, VALUE).
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 25.02.2011*/
function shav_options_value_pars($string = '', $symbol = ' ')
{
	if($string == '')
		return '';

	$opt_array = explode($symbol, strip_tags($string));

	return array('NAME' => $opt_array[0], 'VALUE' => $opt_array[1]);
}

/** Получаем все опции по ключу.
	@param $text - содержимое опций;
	@param $symbol - символ разделителя параметров.
	@return Массив с полученными опциями.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 25.02.2011*/
function shav_options_pars($text, $symbol = ',')
{
	global $shavDB;

	if($text == '')
		return array();

	$opt = explode($symbol, strip_tags($text));
	$count = count($opt);
	$i = 0;
	while($i < $count)
	{
		$options[] = shav_options_value_pars($opt[$i]);
		$i++;
	}

	return $options;
}

/** Выводит содержимое массива.
	@param $array - Массив для вывода.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function drawObject($array = array(), $isDraw = true)
{
	if(empty($array))
	{
		echo '<br />'.MSG_ARRAYS_0002;
		return;
	}

	if(is_object($array))
		$content = '<pre>'.print_r($array, true).'Methods: '.print_r(get_class_methods($array), true).'</pre>';
	else
		$content = '<pre>'.print_r($array, true).'</pre>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Отправляет сообщение на электронную почту.
	@param $from - от кого пришло письмо;
	@param $to - эллектронный адрес получателя (e-mail);
	@param $message - собственно само сообщение в HTML;
	@param $title - тема сообщения.
	@return Сообщение об отправке почты или ошибке.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_send_mail($from, $to, $message = "", $title = "No title")
{
	if($to == '')
		return ERROR_MAILS_0001;

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: '.$from."\r\n". 'Reply-To: '.$to;

	mail($from, $title, $message, $headers);

	return MSG_MAILS_0001;
}

/** Получаем часть текста.
	@param $text - исходный текст;
	@param $size - длина выходного текста (в словах).
	@return Текст из $size-слов.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 25.02.2011*/
function shav_get_half_text($text, $size = 10)
{
	if($text == '')
		return '';

	$postx = explode(" ", strip_tags($text));

	$i = 1;
	$cont = '';
	$count = count($postx);
	foreach ($postx as $word)
	{
		$cont .= $word.' ';
		$i++;

		if($i > $size)
			break;
	}

	if($count > $size)
		return $cont."[...]";
	else
		return $cont;
}

/** Генерирует пароль определенной длины.
	@param $size - длина пароля.
	@return Произвольный пароль из $size символов.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_genSecretKey($size = 15)
{
	$arr = array(	'a','b','c','d','e','f',
			'g','h','i','j','k','l',
			'm','n','o','p','r','s',
			't','u','v','x','y','z',

			'A','B','C','D','E','F',
			'G','H','I','J','K','L',
			'M','N','O','P','R','S',
			'T','U','V','X','Y','Z',

			'1','2','3','4','5','6',
			'7','8','9','0','.',',',
			'(',')','[',']','!','?',
			'&','^','%','@','*','$',
			'<','>','/','|','+','-',
			'{','}','`','~');

	// Generating new password
	$pass = "";
	$i = 0;
	$index = 0;
	while($i < $size)
	{
		// Getting random index
		$index = rand(0, count($arr) - 1);
		$pass .= $arr[$index];

		$i++;
	}

	return $pass;
}

/** Создает разбивку на страницы.
	@param $count - Количество данных для разбивки на страницы;
	@param $max_rows = 10 - количество строк на одной странице.
	@return Выводит строку ввиде 1, 2, 3,..., n, где 1-n - это номера страниц.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_createPageForDataArray($count, $max_rows = 10, $cur_page = 1)
{
	$text = '';
	$pages = (int)($count / $max_rows);

	if((int)($pages * $max_rows) < $count)
		$pages = $pages + 1;

	$i = 1;
	while($i <= $pages)
	{
		if(($cur_page) == $i)
			$page_str .= $i.', ';
		else
			$page_str .= '<a href="?page='.$i.'">'.$i.'</a>, ';
		$i++;
	}

	$page_str = substr($page_str, 0, strlen($page_str)-2);
	$text = '<p class="page">'.PAGE.$page_str.'</p>';

	return $text;
}

/** Создает ссылку которая обрабатывается используя скрипт на PHP. В этом скрипте можно выполнять SQL-запросы и при этом текущая страничка не перегружается.
	@param $to_script - скрипт для обработки параметров при нажатии на ссылку;
	@param $text_for_link - текст для ссылки (можно и тег <img>);
	@param $params - массив параметров, имеет вид: array('<параметр>'=>'<значение>', ...);
	@param $isDraw - выводить или вернуть HTML-код.
	@return Выводит на экран или возвращает HTML-код ссылки с использованием JavaScript.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_createJSLink($to_script, $text_for_link, $params = array('id'=>'0'), $aler = 'JavaScript is Done!', $isDraw = false)
{
	if(empty($params))
	{
		if($isDraw == true)
			echo MSG_ARRAYS_0001;
		else
			return MSG_ARRAYS_0001;
	}

	$params_query = '';
	foreach($params as $key=>$value)
	{
		$params_query .= $key.'='.$value.'&';
	}

	$params_query = str_replace('&', '&amp;', substr($params_query, 0, strlen($params_query)-1));

	$content = '<a href="#" onclick="$.post(\''.$to_script.'?'.$params_query.'\', function(data){alert(\''.$aler.'\');});">'.$text_for_link.'</a>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Конвертирует массив в строку формата JSON.
	@param $array - массив с данными для конвертирования в JSON;
	@param $isDraw - выводить или вернуть HTML-код.
	@return Возвращает или выводи строку с данными в формате JSON.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_convertArrayToJSON($array, $isDraw = false)
{
	$content = json_encode($array);

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Конвертирует строку формата JSON в массив.
	@param $json - строка формата JSON.
	@return Возвращает или выводи строку с данными в формате JSON.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_convertJSONToArray($json)
{
	$array = json_decode($array);

	return $array;
}

/** Исправляет проблемы с кодирование русских символов в JSON строке.
	@param $json_str - строка в формате JSON.
	@return Строка с преобразованными русскими символами.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_jsonFixCyr($json_str)
{
	$cyr_chars = array (
		'\u0430' => 'а', '\u0410' => 'А',
		'\u0431' => 'б', '\u0411' => 'Б',
		'\u0432' => 'в', '\u0412' => 'В',
		'\u0433' => 'г', '\u0413' => 'Г',
		'\u0434' => 'д', '\u0414' => 'Д',
		'\u0435' => 'е', '\u0415' => 'Е',
		'\u0451' => 'ё', '\u0401' => 'Ё',
		'\u0436' => 'ж', '\u0416' => 'Ж',
		'\u0437' => 'з', '\u0417' => 'З',
		'\u0438' => 'и', '\u0418' => 'И',
		'\u0439' => 'й', '\u0419' => 'Й',
		'\u043a' => 'к', '\u041a' => 'К',
		'\u043b' => 'л', '\u041b' => 'Л',
		'\u043c' => 'м', '\u041c' => 'М',
		'\u043d' => 'н', '\u041d' => 'Н',
		'\u043e' => 'о', '\u041e' => 'О',
		'\u043f' => 'п', '\u041f' => 'П',
		'\u0440' => 'р', '\u0420' => 'Р',
		'\u0441' => 'с', '\u0421' => 'С',
		'\u0442' => 'т', '\u0422' => 'Т',
		'\u0443' => 'у', '\u0423' => 'У',
		'\u0444' => 'ф', '\u0424' => 'Ф',
		'\u0445' => 'х', '\u0425' => 'Х',
		'\u0446' => 'ц', '\u0426' => 'Ц',
		'\u0447' => 'ч', '\u0427' => 'Ч',
		'\u0448' => 'ш', '\u0428' => 'Ш',
		'\u0449' => 'щ', '\u0429' => 'Щ',
		'\u044a' => 'ъ', '\u042a' => 'Ъ',
		'\u044b' => 'ы', '\u042b' => 'Ы',
		'\u044c' => 'ь', '\u042c' => 'Ь',
		'\u044d' => 'э', '\u042d' => 'Э',
		'\u044e' => 'ю', '\u042e' => 'Ю',
		'\u044f' => 'я', '\u042f' => 'Я',
		'\u2116' => '№',

		'\r' => '',
		'\n' => '<br />',
		'\t' => ''
	);

	foreach ($cyr_chars as $cyr_char_key => $cyr_char)
		$json_str = strip_tags(str_replace($cyr_char_key, $cyr_char, $json_str));

	return $json_str;
}

/** Создает объект файла по полному путь.
	@param $path - полный путь к файлу.
	@return Объект класса SHAV_File с информацией о файле.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 10.01.2009
*	@date Обновленно: 15.02.2011*/
function shav_createFileByPath($path)
{
	if($path == '')
		return null;

	$obj = new SHAV_File($path);

	return $obj;
}

?>