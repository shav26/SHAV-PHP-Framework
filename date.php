<?php
/*Утилита для работы с датами. Позволяет преобразовать даты и timestamp.*/

$type = $_GET['type'];
if($_GET['date'] != '' && $type != '')
{
	switch($type)
	{
		case 'to_date':
			if($_GET['format'] != '')
				echo 'Timestamp: '.$_GET['date'].' Date: '.date($_GET['format'], $_GET['date']);
			else
				echo 'ERROR: Format not set! Please, see <a href="date.php">help</a>';
			break;
		case 'to_timestamp':
			echo 'Timestamp: '.strtotime($_GET['date']);
			break;
		case 'date_to_date':
			if($_GET['format'] != '')
				echo 'Source date: '.$_GET['date'].' Target date: '.date($_GET['format'], strtotime($_GET['date']));
			else
				echo 'ERROR: Format not set! Please, see <a href="date.php">help</a>';
			break;
	}
}
else
{
	echo '<b>Помошь по использованию:</b><p>Текущий скрипт содержит два параметра:<ul><li><b>date</b> - дата. Может быть как timestamp так и дата ввиде строки.</li><li><b>format</b> - формат вывода преобразования даты.</li><li><b>type</b> - тип преобразования: <ul><li><i>to_date</i> - timestamp преобразует в дату используя format.</li><li><i>to_timestamp</i> - преобразовать в timestamp</li><li><i>date_to_date</i> - преобразовать дату в дату используя другой формат.</li></ul></li></ul></p>';
}
?>