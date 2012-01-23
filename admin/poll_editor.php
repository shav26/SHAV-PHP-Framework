<?php
include_once('../inc/shav_config.php');
global $shavDB;

//Подключаем нужные JavaScript'ы
$shavJS->shav_FishEye();
$shavJS->shav_tiniMCE();
$shavJS->shav_jqModal();
$shavJS->shav_loadSlider();
$shavJS->shav_jQueryWindow();
$shavJS->shav_jQuerySideBar(array('idcomponent'=>'.sidebar', 'show_text'=>'Show', 'hide_text'=>'Hide'));


//Создаем меню
$menu = new SHAV_Menu();
$menu->createMenuFromArray($admin_pages, 3);
$jQueryMenu = $menu->content;

$pollEditor = new SHAV_jPoll();
$content = $pollEditor->drawEditorInterface();

$page_title = 'Редактор опросов';

include_once('def_admin_fnc.php');

$tags = array('#TITLE#'=>$title.': '.$page_title, '#DESCRIPTION#'=>'', '#KEYWORDS#'=>'', '#JAVA_SCRIPTS#'=>$shavJS->drawJS(), '#HEADER#'=>shav_createContentsByTags(array('#PAGE_TITLE#'=>$page_title), $header), '#FOOTER#'=>$footer, '#CONTENT#'=>$content, '#LEFT_PANEL#'=>$left, '#RIGHT_PANEL#'=>$right, '#TITLE_PAGE#'=>$title);

//Выводим страницу
$page = new SHAV_Page();
$page->createPageFromFileWithTags('../tmpls/admin/admin_index_panel.html', $tags);
$page->drawPage();
?>