<?php
include_once('inc/shav_config.php');

$pms_plot = array('canvasTextRenderer', 'canvasAxisTickRenderer', 'dateAxisRenderer', 'barRenderer', 'categoryAxisRenderer', 'pieRenderer', 'pointLabels');
$js = shav_jQuery().shav_FishEye().shav_tiniMCE().shav_jqModal().shav_loadSlider().shav_tabbedRotator('rotator').shav_loading_jqPloat($pms_plot);
$js .= shav_galleryView().shav_loadSlider().shav_MooFlow();

$content = '<h1>Это пример работы SHAV PHP Framework версии 1.2.0</h1><h3>Галереи:</h3>';
$params = array(); $i = 0;
while($i < 9)
{
	$params[] = array('url'=>'#','image'=>'/images/Battles/Battles_0'.($i+1).'.jpg', 'title'=>'Image '.$i, 'desc'=>'Image '.$i);
	$i++;
}
$content .= '<div width="300px" height="300px">'.shav_createImageGallery_flow('gallery', $params).'</div>';


//Создаем страницу админки.
$header = '<div class="logo"><a href="/"><img src="/images/logo2_blue.png" /></a></div><div style="float:left;"><div align="center" style="font:28px Arial;width:728px;height:50px;">'.HTTP_HOST.'</div><div class="menu_pos">'.$jQueryMenu.'</div></div>';
$footer = '<div align="center">Автор: Andrew Shapovalov; 3 декабря 2009<br />Все права защещены!!!<br /><a href="http://lmwshav.org.ua">Сайт автора</a></div>';

$page = new SHAV_Page();
$tags = array('#TITLE#'=>$title, '#DESCRIPTION#'=>'Тестовый сайт с использованием SHAV PHP Framework', '#KEYWORDS#'=>'SHAV PHP Freamwork', '#JAVA_SCRIPTS#'=>$js, '#HEADER#'=>$header, '#FOOTER#'=>$footer, '#CONTENT#'=>$content);
$page->createPageFromFileWithTags('./tmpls/index.html', $tags);

//Выводим страницу
$page->drawPage();

?>