<?php
/** Файл конфигурации движка. Подключает некоторые файлы для работы с сайтом и БД. Также можно указывать необходимые константы.
 @author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 @date Созданно: 08.08.2009
 @date Обновленно: 20.03.2011*/

//Подключаем файл класса для работы с БД
include_once("shav_db_fns.php");
//Объявляем объект класс
$shavDB = new SHAV_DB();
//Конфигурируем подключение к БД
$shavDB->db_connect("framework", "localhost", "root", "78824982");

//Запускаем сессию.
session_start();


//Параметры, которые нужно использовать в подключаемых скриптах.
//Тут можно использовать массив для функций создания форм, списков, меню и т. д.
//Меню для админки
$admin_pages = array(	'pages_table' => 'admin_pages',	//Название таблицы страниц
			'page_id_name' => 'pages_id', 				//Идентификатор страницы (ключ)
			'parent_id_name' => 'prn_id',				//Идентификатор родителя
			'show_page' => 'is_show',					//Показывать или нет стараничку
			'icon_fld' => 'icons',						//Поле с иконкой меню
			'url' => 'p_url',							//Ссылка на файл с выводом контента
			'title' => 'p_title');						//Заголовок страницы
//Меню для основного сайта
$sites_pages = array(	'pages_table' => 'pages',	//Название таблицы страниц
			'page_id_name' => 'pages_id', 			//Идентификатор страницы (ключ)
			'parent_id_name' => 'prn_id',			//Идентификатор родителя
			'show_page' => 'is_show',				//Показывать или нет стараничку
			'icon_fld' => 'icons',					//Поле с иконкой меню
			'url' => 'p_url',						//Ссылка на файл с выводом контента
			'title' => 'p_title');					//Заголовок страницы
//Параметры формы авторизации
$frm_login_params = array(	'login' => 'You login:',
							'pass' => 'You password:',
							'reg' => 'Registred',
							'action_scrp' => '',
							'reg_scrp' => '/register.php',
							'button' => 'Sing in');


//Подключаем дополнительные функции
include_once("shav_errors_msg.php");			//Системные ошибки и сообщения
include_once("shav_functions.php");				//Дополнительные функции для работы с данными на сайте
include_once("shav_standart_class.php");		//Класс простого объекта
include_once("shav_page_func.php");				//Дополнительные функции по работе с страницами сайта
include_once("shav_form_class.php");			//Класс для работы с HTML-формами
include_once("shav_javascripts_class.php");		//Класс дл настройки JavaScript.
include_once("shav_desktopSite_class.php");		//Класс для создания сайта ввиде рабочего стала компьютера
include_once("shav_rss_class.php");				//Класс для работы с RSS лент
include_once("shav_xml_class.php");				//Класс для работы с XML
include_once("shav_page_class.php");			//Класс для создания страниц сайтов
include_once("shav_menu_class.php");			//Класс для создания меню на сайтах
include_once("shav_droplist_class.php");		//Класс для создания выподающих списков
include_once("shav_other_classes.php");			//Дополнительные классы для работы с framework'ом
include_once("shav_upload_class.php");			//Класс для создания полей загрузки файлов в формах
include_once("shav_gallery_carousel_class.php");//Класс для создания галлереи
include_once("shav_gallery_view.php");			//Класс для создания галлереи на базе плагина Gallery View
include_once("shav_todo_classes.php");			//Класс для работы с Менеджером проектов (создание и редактирование задачь для проектов)
#include_once("shav_phpbb3_class.php");			//Модуль для работы с форумом phpbb3
include_once("shav_api_class.php");				//Модуль для обменна данными с сторонними программами
include_once("shav_search_class.php");			//Класс поиска
include_once("shav_templates_class.php");		//Класс для редактирования темплейтов
include_once("shav_push_class.php");			//Класс для работы с Apple Push Notification
include_once("shav_poll_class.php");			//Классы для работы с опросами
//Работа с админкой
include_once("shav_admin_fnc.php");		//Функции для работы с админкой
//Пользовательские файлы
include_once("country_arr.php");		//Массив всех стран на английском языке


//Разные констаны, если необходимы
$shavJS = new SHAV_JavaScript();

define('MAX_ROW_ON_PAGE', 60);					//Количество записей на одной странице статьей
define('MAX_ROW_ON_PROJECTS_PAGE', 10);		//Количество записей на одной странице проектов
define('MAX_ROW_ON_NEWS_PAGE', 60);			//Количество записей на одной странице новостей
define('FILE_UPLOAD', 'multipart/form-data');	//Для загрузки файло в формах.
define('SHAV_VERSION', '1.3.0');		//Версия framework'а
define('HTTP_HOST', 'http://'.$_SERVER['HTTP_HOST'].'/');	//Главный URL
?>