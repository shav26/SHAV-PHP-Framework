<?php

/** @class SHAV_PushNotification.
 *	@brief Класс для работы с Apple Push Notification. Позволяет отправлять уведомление на мобильные устройства корпорации Apple.
 *	Пример использования:
 *	@code
 * $push = new SHAV_PushNotification();
 * $push->drawInterface(true);	//Выводим интерфейс редактора для отправки оповищения.
 * $this->sendPush('token', 'msg', (int)$badge, 'sound', 'cer');	//Отправка оповищения.
 *	@endcode
 *	@note Для использования данного модуля Вам необходимо создать сертификат и поместить его в папку root_folder/uploads/certificates/. Как сгенерировать сертификат почитать можно по <a href="http://blog.boxedice.com/2009/07/10/how-to-build-an-apple-push-notification-provider-server-tutorial/">ссылке</a>.
 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">SHAV Software</a>).
 *	@date Созданно: 17.02.2011
 *	@date Обновленно: */
class SHAV_PushNotification extends SHAV_Object
{
	/** Массив всех доступных сертификатор для отправки Push Notification.*/
	public $certificates = array();

	/** Конструктор класса.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">SHAV Software</a>).
	 *	@date Созданно: 17.02.2011
	 *	@date Обновленно: */
	function SHAV_PushNotification()
	{
		$this->getAllFilesFromFolder('../uploads/certificates/');
	}

	/** Выводит интерйфейс для работы с Apple Push Notification.
	 *	@param $isDraw - выводить или нет HTML-код интерфейса
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">SHAV Software</a>).
	 *	@date Созданно: 17.02.2011
	 *	@date Обновленно: */
	function drawInterface($isDraw = false)
	{
		$cerSelect = (int)$_POST['cer'];
		if($cerSelect == 0) $cerSelect = 1;

		if($_POST['send'] && $_POST['msg'] != '' && (int)$_POST['badge'] > 0 && $_POST['sound'] != '' && $_POST['token'] != '')
		{
			$this->sendPush($_POST['token'], $_POST['msg'], (int)$_POST['badge'], $_POST['sound'], $this->certificates[(int)$_POST['cer']]);
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/push.php');
		}
		
		$cerList = '<select name="cer" id="cer"><option value="0">Не выбран сертификат</option>';
		$i = 1;
		foreach($this->certificates as $cer)
		{
			if($i == $cerSelect)
				$cerList .= '<option value="'.$i.'" selected>'.$cer.'</option>';
			else
				$cerList .= '<option value="'.$i.'">'.$cer.'</option>';
		}
		$cerList .= '</select>';
		
		$content  = '<div align="center"><div class="push_component">';
		$content .= '<form method="POST" action="">';
		$content .= '<table width="100%">';
		$content .= '<tr><th colspan="2" align="center">Отправить Push Notification</th></tr>';
		$content .= '<tr><td colspan="2" align="left">Для работы используется сертификат:&nbsp;'.$cerList.'</td></tr>';
		$content .= '<tr><td align="right" valign="top">Идентификатор устройства: </td>';
		$content .= '<td><input type="text" id="token" name="token" size="70" value="02da851d XXXXXXXX b4f2b5bf XXXXXXXX ce198270 XXXXXXXX 0d3dac72 bc87cd60" /></td></tr>';
		$content .= '<tr><td align="right" valign="top">Количество значков: </td><td><input type="text" id="badge" name="badge" value="1" /></td></tr>';
		$content .= '<tr><td align="right" valign="top">Название звука: </td><td><input type="text" id="sound" name="sound" value="default" /></td></tr>';
		$content .= '<tr><td align="right" valign="top">Сообщение: </td><td><textarea id="msg" name="msg" cols="80" rows="15">Сообщение для проверки.</textarea></td></tr>';
		$content .= '<tr><td colspan="2" align="right"><input type="submit" id="send" name="send" value="Отправить" /></td></tr>';
		$content .= '</table>';
		$content .= '</form>';
		$content .= '</div></div>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Отправить Push Notification.
	 *	@param $token - уникальный идентификатор устройства (iPhone, iPad, iPod touch);
	 *	@param $message - сообщение для отображения;
	 *	@param $badge - количество записей;
	 *	@param $sound - звук для воспроизведения.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">SHAV Software</a>).
	 *	@date Созданно: 17.02.2011
	 *	@date Обновленно: */
	function sendPush($token, $message, $badge = 1, $sound = 'default', $cer = '../uploads/certificates/cer.pem')
	{
		// Construct the notification payload
		$body = array();
		$body['aps'] = array('alert' => $message);
		if ((int)$badge > 0)
			$body['aps']['badge'] = $badge;

		if ($sound != '')
			$body['aps']['sound'] = $sound;
		
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $cer);
		// assume the private key passphase was removed.
		// stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);

		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		if (!$fp) {
			print "Failed to connect $err $errstrn";
			return;
		}
		else {
			print "Connection OKn";
		}
		
		$payload = json_encode($body);
		$msg = chr(0).pack("n",32).pack('H*', str_replace(' ', '', $token)).pack("n",strlen($payload)).$payload;
		print "sending message :".$payload."n";

		fwrite($fp, $msg);
		fclose($fp);
	}
	
	/** Получить все файлы из папки.
	 *	@param $folder - полный путь к папке в которой следует получить все файлы.
	 *	@return Массив из всех файлов.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">SHAV Software</a>).
	 *	@date Созданно: 17.02.2011
	 *	@date Обновленно: */
	private function getAllFilesFromFolder($folder)
	{
		if(empty($folder))
			return;
		
		$allFiles = shav_GetAllFilesFromFolder($folder, true);
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		
		$this->certificates = array();
		foreach($allFiles as $file)
		{
			$typeOfFile = finfo_file($finfo, $file);
			if($typeOfFile == 'text/plain')
			{
				$this->certificates[] = $file;
			}
		}
		
		finfo_close($finfo);
	}
}
?>