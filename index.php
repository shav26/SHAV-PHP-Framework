<?php
include_once('inc/shav_config.php');

$pms_plot = array('canvasTextRenderer', 'canvasAxisTickRenderer', 'dateAxisRenderer', 'barRenderer', 'categoryAxisRenderer', 'pieRenderer', 'pointLabels');
$shavJS->shav_FishEye();
$shavJS->shav_tiniMCE();
$shavJS->shav_jqModal();
$shavJS->shav_loading_jqPloat($pms_plot);
$shavJS->shav_loadSlider();
$shavJS->shav_jQuerySideBar(array('idcomponent'=>'.sidebar', 'show_text'=>'Show', 'hide_text'=>'Hide'));
$shavJS->shav_jQueryWindow();
/*$shavJS->shav_galleryView();
$shavJS->shav_gallery_jcarousel();*/

//Создаем меню
$menu = new SHAV_Menu();
$menu->createMenuFromArray($sites_pages, 3);
$jQueryMenu = $menu->content;

/*$poll = new SHAV_jPoll();
$content = $poll->getContentPoll();*/

$modal1 = new SHAV_jModalWindow('window_1', 'Тестовое окно 1');
$modal1->content = '<p>Все работает!</p>';
$modal2 = new SHAV_jModalWindow('window_2', 'Тестовое окно 2');
$modal2->content = '<p>Это второе окно сос воими данными :-)</p>'.$modal1->drawLink();
$modal1->createJSConfig();
$modal2->createJSConfig();
$content .= '<div style="width:200px;">'.shav_createSideBar('sidebar', 'Пользователи', '<p>'.$modal2->drawLink().'</p>').'</div>';

$table = new SHAV_Table();
$table->id = 'table_test';
$table->title = 'Пример таблиц с выподающем содержимым.';
$table->titleHeader = array('Название', 'Описание');
$table->titleContent = array(array('title'=>array('Программирование', 'C/C++, Objective-C/C++, Qt/C++, C#, Java, JavaScript, PHP, Perl'), 'content'=>'Разработка программ для разных платформ.'));
$content .= $table->drawTable();

/*$content = '<h1>Это пример работы SHAV PHP Framework версии 1.2.0</h1><h3>Галереи:</h3>';
$params = array(); $i = 0;
while($i < 9)
{
	$params[] = array('url'=>'#','image'=>'/images/Battles/Battles_0'.($i+1).'.jpg', 'title'=>'Image '.$i, 'desc'=>'Image '.$i);
	$i++;
}
$content .= shav_createImageGallery_flow('gallery_flow', $params);*/
/*$gallery = new SHAV_jCarouselGallery();
//$gallery->drawHelp();
$gallery->idGallery = 'gallery';
$gallery->idContainer = 'conteiner_gallery';
$gallery->title = 'Simple photo gallery with Galleria and jCarousel.';
$gallery->images = array();
$i = 1;
while($i < 10)
{
	$gallery->images[] = array('image'=>'images/Battles/Battles_0'.($i+1).'.jpg', 'title'=>'Image '.$i, 'thumbs'=>'images/Battles/Battles_0'.($i+1).'.jpg');
	$i++;
}
$gallery->size_thumbs = array('width'=>'auto', 'height'=>50);
$gallery->size = array('width'=>300, 'height'=>100);
$content .= $gallery->drawGallery();*/
/*
$gallery2 = new SHAV_GalleryView('images/Battles/');
//$gallery2->drawHelp();
$gallery2->idGallery = 'gallery2';
$gallery2->images = array();
$i = 1;
while($i < 10)
{
	$gallery2->images[] = new SHAV_GalleryView_Image('images/Battles/Battles_0'.($i+1).'.jpg', 'images/Battles/Battles_0'.($i+1).'.jpg', 'Image '.$i, 'Image '.$i);
	
	$i++;
}
$content .= $gallery2->drawGallery();*/
/*$dropBox = new SHAV_DropList();
$dropBox->createListFromDBTable(array('name'=>'bans_status', 'id_field'=>'bans_status_id', 'name_field'=>'bans_status_name'), 'status', 0);
$content = $dropBox->drawList();*/

//Создаем страницу админки.
$header = '<div class="logo"><a href="/"><img src="/images/logo2.png" /></a></div><div style="float:left;"><div align="center" style="font:28px Arial;width:728px;height:50px;">'.HTTP_HOST.'</div><div class="menu_pos">'.$jQueryMenu.'</div></div>';
$footer = '<div align="center" style="font:12px Arial;"><a href="http://lmwshav.org.ua">SHAV Software.</a><br />Copyright 2009-2011<br />Все права защещены!!!</div>';
$left = '<p>Это контент для левой панели.</p>';
$links = '<p><a href="http://lmwshav.org.ua/forum">Форум для вопросов</a><br /><a href="http://lmwshav.org.ua/projects.php">Проекты</a><br /><a href="http://lmwshav.org.ua/articls.php">Полезные статьи.</a></p>';
$right = '<div class="frame"><div class="header_frame"><h1>Полезные ссылки</h1></div><div class="content_frame">'.$links.'</div></div>';

$page = new SHAV_Page();
$tags = array('#TITLE#'=>$title, '#DESCRIPTION#'=>'Тестовый сайт с использованием SHAV PHP Framework', '#KEYWORDS#'=>'SHAV PHP Freamwork', '#JAVA_SCRIPTS#'=>$shavJS->drawJS(), '#HEADER#'=>$header, '#FOOTER#'=>$footer, '#CONTENT#'=>$content, '#LEFT_PANEL#'=>$left, '#RIGHT_PANEL#'=>$right);
$page->createPageFromFileWithTags('./tmpls/index.html', $tags);

//Выводим страницу
$page->drawPage();
?>