<?php
include_once('inc/shav_config.php');
$shavJS->shav_jQuerySideBar(array('idcomponent'=>'#sidebar', 'show_text'=>'Show', 'hide_text'=>'Hide'));

//Создаем объект класс для нашего сайта
$desktop_site = new SHAV_Desktop();
$desktop_site->titleSite = 'Тестовый сайт';
$desktop_site->description = 'Тестовый сайт с использованием SHAV PHP Framework';
//$desktop_site->cssArray[] = '/css/shav_common.css';
//$desktop_site->jsArray[] = '/js/jquery.js';
$desktop_site->jsArray[] = $shavJS->drawJS();

//Создаем верхнюю панель
$menu = array();
$menu[] = array('title'=>'Сайт', 'sub_menus'=>array(array('url'=>'/', 'text'=>'Простой сайт'), array('url'=>'/desktop_index.php', 'text'=>'Домой')));
$desktop_site->topPanel = new SHAV_TopBar($menu);
$menuDB = new SHAV_Menu();
$menuDB->createMenuFromArray($sites_pages, 3);
$desktop_site->topPanel->createFromMenuClass('Из базы', $menuDB);

//Создаем иконки на рабочем столе
$desktop_site->icons[] = new SHAV_DesktopIcon('Google.com', '/images/home.png', 'left:20px;top:20px;', 'http://google.com');
$desktop_site->icons[] = new SHAV_DesktopIcon('Информация', '/images/admin.png', 'left:20px;top:80px;', '#icon_info');

//Создаем окно
$window1 = new SHAV_DesktopWindow();
$window1->title = 'Информация';
$window1->content = 'Это тестовый сайт, который использует последнюю версию SHAV PHP Framework.<br />Дата последнего обновления: 09.03.2011
<br /><br />
SHAV PHP Engine<br />&nbsp;&nbsp;&nbsp;+ -> это добавленные функции;	- -> это убранные функции;	m -> это внесенные изменения.<br />
<br />
<p>Version 1.3.0<br />
&nbsp;&nbsp;&nbsp;m файл shav_javascripts_loading.php был изменен на shav_javascripts_class.php, а все функции были внесенны в нутрь класса SHAV_JavaScript;<br />
&nbsp;&nbsp;&nbsp;+ класс редактирования опросов;<br />
&nbsp;&nbsp;&nbsp;+ класс редактирования Site API;<br />
&nbsp;&nbsp;&nbsp;+ класс для обтпавки Push Notification для Apple iPhone;<br />
&nbsp;&nbsp;&nbsp;+ класс редактирования шаблонов страниц;<br />
&nbsp;&nbsp;&nbsp;+ комментарии для скриптов в формате doxygen для автоматической генерации документации;<br />
&nbsp;&nbsp;&nbsp;m исправлена верстка центрального блока для контента.<br />
<br />
Version 1.2.1:<br />
&nbsp;&nbsp;&nbsp;m исправлен баг при создании под меню в главном меню;<br />
&nbsp;&nbsp;&nbsp;m изменен класс создания выпадающих списков, теперь можно изменить текст для первого значения, которое выбирается всегда первым;<br />
&nbsp;&nbsp;&nbsp;m исправленны проблемы с версткой в todo tracker;<br />
&nbsp;&nbsp;&nbsp;+ добавленна верстка с закруглениями (реализована на CSS 3, поэтому в IE может не работать);<br />
&nbsp;&nbsp;&nbsp;m изменена функция создания формы авторизации shav_createLoginForm();<br />
&nbsp;&nbsp;&nbsp;+ добавлен класс работы с файлами SHAV_File;<br />
&nbsp;&nbsp;&nbsp;- удален параметры $isDraw в функциях createJSON..... класса SHAV_API;<br />
&nbsp;&nbsp;&nbsp;+ класс SHAV_GalleryView_Image, описывающий элемент картинки для галереи SHAV_GalleryView;<br />
&nbsp;&nbsp;&nbsp;+ в класс SHAV_GalleryView добавлена возможность создавать галереии из папки, для этого в конструктор следует передать путь к папки;<br />
&nbsp;&nbsp;&nbsp;+ функция shav_GetAllFilesFromFolder() - получает все файлы из директории;<br />
&nbsp;&nbsp;&nbsp;+ класс SHAV_Object, который содержит все функции необходимы в каждом наследуемом классе от данного. Сейчас доступна только одна: drawHelp() - выводит помощь по методам и переменным классов;<br />
&nbsp;&nbsp;&nbsp;- удалена из-за недадобности shav_createSimpleGallery();<br />
&nbsp;&nbsp;&nbsp;- функции для работы с простыми галлереями.<br />
<br />
Version 1.2.0:<br />
&nbsp;&nbsp;&nbsp;+ добавленна поддержка JSON, теперь можно легко конвертировать массивы в строки JSON и обратно;<br />
&nbsp;&nbsp;&nbsp;+ добавленны функции API для работы с внешними приложениями, например, iPhone App или PC App;<br />
&nbsp;&nbsp;&nbsp;m заменена функция для создания форм на класс SHAV_From, теперь Вы можете создавать формы используя специальный класс;<br />
&nbsp;&nbsp;&nbsp;+ класс для создания TODO-трекера (портала для работы с проектами, создания задачь);<br />
&nbsp;&nbsp;&nbsp;+ класс для загрузки картинок на сервер используя jQuery и AjaxUpload;<br />
&nbsp;&nbsp;&nbsp;+ функции для создания графиков и диограмм;<br />
&nbsp;&nbsp;&nbsp;+ функции для работы с простыми галлереями;<br />
&nbsp;&nbsp;&nbsp;+ функции настройки и создания рекламных слайдеров с авто прокруткои и переходами по табам: shav_tabbedRotator() - настройка, shav_createTabbedRotator() - создает ротатор;<br />
&nbsp;&nbsp;&nbsp;+ функция для создания поля для формы с каледрарем shav_createCalendarFieldRus();<br />
&nbsp;&nbsp;&nbsp;+ функции по работе с выводающими списками вынесенны в отдельнвй класс SHAV_DropList;<br />
&nbsp;&nbsp;&nbsp;+ добавлен плагин jqModal для создания красивых модальных окон;<br />
&nbsp;&nbsp;&nbsp;+ добавлена библиотека по работе с jQuery UI;<br />
&nbsp;&nbsp;&nbsp;+ функция создания слайдера для проверки на бота shav_createSlider();<br />
&nbsp;&nbsp;&nbsp;+ функция подключения плагина для создания слайдеров shav_loadSlider();<br />
&nbsp;&nbsp;&nbsp;m заменены функции создания сайтов в виде рабочего стола, теперь Вы можете создавать такие сайты используя класс;<br />
&nbsp;&nbsp;&nbsp;m заменены функции работы с меню, теперь Вы можете создавать меню используя класс, который позволяет создавать три вида меню;<br />
&nbsp;&nbsp;&nbsp;+ возможность загрузки картинок с использование jQuery (т.е. без перезагрузки страницы);<br />
&nbsp;&nbsp;&nbsp;+ возможность изменить размер картинки и наложить на новую картинку водяной знак;<br />
&nbsp;&nbsp;&nbsp;m изменена функция для создания панели с SideBar\'ами.<br />
<br />
Version 1.1.1 (dev):<br />
&nbsp;&nbsp;&nbsp;m заменены функции создания сайтов в виде рабочего стола, теперь Вы можете создавать такие сайты используя класс;<br />
&nbsp;&nbsp;&nbsp;m удаленны функции рабьоты с меню, теперь Вы можете создавать меню используя класс, который позволяет создавать три вида меню;<br />
&nbsp;&nbsp;&nbsp;+ возможность загрузки картинок с использование jQuery (т.е. без перезагрузки страницы);<br />
&nbsp;&nbsp;&nbsp;+ возможность изменить размер картинки и наложить на новую картинку водяной знак.<br />
<br />
Version 1.1.0:<br />
&nbsp;&nbsp;&nbsp;+ сообщения об ошибках в самих функциях freamwork\'а вынесены в отдельный скрипт errors_msg.php;<br />
&nbsp;&nbsp;&nbsp;+ каркас админ-панели для сайтов;<br />
&nbsp;&nbsp;&nbsp;+ создание XML и работа с RSS лентами;<br />
&nbsp;&nbsp;&nbsp;+ стандартизированна функция для создания страничек сайтов;<br />
&nbsp;&nbsp;&nbsp;m изменены массивы параметров и функции для создания меню, теперь они учитывают то, что не все странички будут показыватся;<br />
&nbsp;&nbsp;&nbsp;m изменены массивы параметров и функции для создания выпадающих списков и HTML-форм, теперь можно выключать элементы;<br />
&nbsp;&nbsp;&nbsp;+ интеграция сайта с форумом phpbb3.x.x;<br />
&nbsp;&nbsp;&nbsp;+ функция для преобразований пользовательских тегов в их значения используя массив соответствий;<br />
&nbsp;&nbsp;&nbsp;m верстка описанна более подробно;<br />
&nbsp;&nbsp;&nbsp;+ функция для создания ссылок на скрипты обработки без перезагрузки страниц (для использования необходимо подключать jQuery);<br />
&nbsp;&nbsp;&nbsp;+ добавленна функция для подключения jQuery, чтобы не писать вручную;<br />
&nbsp;&nbsp;&nbsp;+ добавлен класс для работы со страничками сайта;<br />
&nbsp;&nbsp;&nbsp;+ добавленны функции для создания сайдбаров со спрятанным текстом.<br />
<br />
Version 1.0.3:<br />
&nbsp;&nbsp;&nbsp;+ собавлен скрипт для создания сайтов в виде рабочего стола;<br />
&nbsp;&nbsp;&nbsp;* сам проект переименован, новое название SHAV PHP Freamwork.<br />
<br />
Version 1.0.2:<br />
&nbsp;&nbsp;&nbsp;+ shav_createMenu_jQuery() - Создает меню с использувание jQuery и некоторых плагинов;<br />
&nbsp;&nbsp;&nbsp;+ shav_createImageGallery_flow() - Создает галерею картинок с эффектом CoverFlow как на iPhone;<br />
&nbsp;&nbsp;&nbsp;+ shav_createSimpleGallery() - Создает простую галерею с прокруткой;<br />
&nbsp;&nbsp;&nbsp;+ shav_createTextSlider() - Создает текстовый слайдер, в котором прокручивается текст или картинки;<br />
&nbsp;&nbsp;&nbsp;m Изменены значения поумолчанию в функциях находящикхся в файлах: shav_page_func.php и javascripts_loading.php. Теперь значение поумолчанию fslse.<br />
<br />
Version 1.0.1:<br />
&nbsp;&nbsp;&nbsp;+ Добавленна функция для создания sideBar\'ов;<br />
&nbsp;&nbsp;&nbsp;+ Добавленны дополнительные функции позволяющие подключать jQuery и спользовать красивые элементы этой библиотеки.<p>';
$window1->icon = '/desktop/images/contacts.png';
$window1->idCssClass = 'info';
$window1->idCssClassIcon = '#icon_info';
$window1->leftPanel = shav_createSideBar('sidebar', 'Cсылки', '<p><a href="http://lmwshav.org.ua/">Наш сайт</a></p><p><a href="http://forum.lmwshav.org.ua/">Наш Форум</a></p><p><a href="http://code.google.com/p/phpfreamwork/">Мы на google code</a></p>');
$window1->bottomPanel = 'Нижняя панель';
$desktop_site->windows[] = $window1;

//Создаем связь окна с иконками на системной панели
$desktop_site->windowsOnDock[] = new SHAV_DockIcon('icon_info', '#info', '/desktop/images/contacts.png', 'Информация');
$desktop_site->systemPanel = new SHAV_SystemPanel('Show Desktop', $desktop_site->windowsOnDock, $iconPanel = '/desktop/images/icons/icon_22_desktop.png');

//Выводим сайт
echo $desktop_site->drawSite();
?>