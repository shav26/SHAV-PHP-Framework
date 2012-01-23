<?php
/** Создает галерею картинок с эффектом CoverFlow как на iPhone.
	@param $id - идентификатор компонента;
	@param $params - массив строчек для галлереи, которые содержат ссылки на картинки, их название, описание;
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит или возвращает html-код галереи.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 10.03.2011*/
function shav_createImageGallery_flow($id, $params = array(), $isDraw = false)
{
	global $shavJS;
	
	if(empty($params))
		return MSG_ARRAYS_0001;

	$shavJS->content = '<script type="text/javascript">
	/* <![CDATA[ */
	var myMooFlowPage = {
		start: function(){
			var mf = new MooFlow($(\''.$id.'\'), {
				startIndex: 5,
				useSlider: true,
				useAutoPlay: true,
				useCaption: true,
				useResize: true,
				useMouseWheel: true,
				useKeyInput: true
	});
	}
	};
	window.addEvent(\'domready\', myMooFlowPage.start);
	/* ]]> */
	</script>';

	$content = '<div id="'.$id.'">';
	foreach($params as $rec)
	{
		$content .= '<a href="'.$rec['url'].'" rel="link"><img src="'.$rec['image'].'" title="'.$rec['title'].'" alt="'.$rec['desc'].'" /></a>';
	}
	$content .= '</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}


/** Создает текстовый слайдер, в котором прокручивается текст или картинки.
	@param $params - массив конфигурации;
	@param $isDraw - выводить или вернуть html-код.
	@return Выводит или возвращает html-код.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createTextSlider($params = array('id_text' => 'news', 'data_type' => 'text', 'data_texts' => array(array('url' => '', 'text' => ''))), $isDraw = false)
{
	if(empty($params))
		return MSG_ARRAYS_0001;

	$content  = '<ul id="'.$params['id_text'].'">';
	foreach($params['data_texts'] as $rec)
	{
		if($params['data_type'] == 'text')
			$content .= '<li><a href="'.$rec['url'].'">'.$rec['text'].'</a></li>';
		elseif($params['data_type'] == 'image')
			$content .= '<li><a href="'.$rec['url'].'"><img src="'.$rec['text'].'" /></a></li>';
	}
	$content .= '</ul>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает панель для данных.
	@param $id_component - идентификатор компонента;
	@param $title - Заголовок панели;
	@param $text - Содержимое панели;
	@param $isDraw - Выводить или вернуть html-код панели.
	@return Выводит или возвращает html-код панели.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createSideBar($id_component, $title = '', $text = '', $isDraw = false)
{
	$content  = '<div class="'.$id_component.'">';
	$content .= '	<h1>'.$title.'</h1>';
	$content .= '	<div class="toggle">'.$text.'</div>';
	$content .= '</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает форму авторизации.
	@param $params - текст для полей (параметров в массиве reg может быть массивом вида array('script'=>'register.php', 'title'=>'Create account'));
	@param $isDraw - выводить или вернуть html-код формы.
	@return Выводит или возвращает html-код формы авторизации.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createLoginForm($params = array('login' => 'You login:', 'pass' => 'You password:', 'reg' => '', 'button' => 'Sing in'), $isDraw = false)
{
	if(empty($params))
		return MSG_ARRAYS_0001;

	$content  = '<form class="login_frm" method="POST" action="'.$params['action_scrp'].'">';
	$content .= '	<table id="auth">';
	$content .= '		<tr>';
	$content .= '			<td><label for="login">'.$params['login'].'</label></td>';
	$content .= '			<td><input type="text" name="login" size="14" id="login" value="" /></td>';
	$content .= '		</tr>';
	$content .= '		<tr>';
	$content .= '			<td><label for="pass">'.$params['pass'].'</label></td>';
	$content .= '			<td><input type="password" name="pass" size="14" id="pass" value="" /></td>';
	$content .= '		</tr>';
	$content .= '		<tr>';
	if(is_array($params['reg']))
		$content .= '			<td><a href="'.$params['reg']['script'].'">'.$params['reg']['title'].'</a></td>';
	else
		$content .= '			<td>'.$params['reg'].'</td>';
	$content .= '			<td><div align="right"><input type="submit" name="signin" id="signin" value="'.$params['button'].'" /></div></td>';
	$content .= '		</tr>';
	$content .= '	</table>';
	$content .= '</form>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает модальное окно используя simpleModal на jQuery.
	@param $idWindow - Идентификатор окна, используется в /js/simplemodal/config.js;
	@param $linkTitle - Заголовок ссылки на создаваемое окно;
	@param $titleWnd - Заголовок окна при его отображение;
	@param $text - содержимое окна;
	@param $isDraw - Выводить или нет html-код.
	@return Выводит или возвращает html-код окна.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createModalWindow($idWindow, $linkTitle = 'Link title', $titleWnd = 'Window Title', $text = 'Window html text', $isDraw = false)
{
	$content  = '<div id="'.$idWindow.'"><a href="#" class="basic">'.$linkTitle.'</a></div>';
	$content .= '<div id="'.$idWindow.'-content"><div class="window_header"><h1>'.$titleWnd.'</h1></div><div class="window_content">'.$text.'</div></div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Заменяет вставленные в текст ($html_text) теги на их значения.
	@param $tags - Массив тегов и значений такого вида: array('<тег>'=>'<значение>');
	@param $html_text - текст которые нужно преобразовать;
	@param $isDraw - выводить или вернуть html-код текста.
	@return Выводит или возвращает html-код текста.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createContentsByTags($tags, $html_text, $isDraw = false)
{
	if(!is_array($tags))
	{
		if($isDraw == true)
			echo ERROR_ARRAYS_0100;
		else
			return ERROR_ARRAYS_0100;
	}

	foreach($tags as $key=>$value)
	{
		$html_text = str_replace($key, $value, $html_text);
	}

	if($isDraw == true)
		echo $html_text;
	else
		return $html_text;
}

/** Создает меню ввиде грамошки.
	@param $class - Идентификатор класс элемента, который нужно превратить в меню "Гармошка";
	@param $content_arr - массив данных для вывода;
	@param $isDraw - Выводить или вернуть HTML-код.
	@return Выводит или возвращает HTML-код меню.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 10.03.2011*/
function shav_createMenuGarmoshka($class, $content_arr = array(array('header'=>'', 'text'=>'')), $isDraw = false)
{
	global $shavJS;
	
	$shavJS->content = '<script type="text/javascript">
		$(document).ready(function(){
			$(".'.$class.' h3:first").addClass("active");
			$(".'.$class.' p:not(:first)").hide();

			$(".'.$class.' h3").click(function(){
				$(this).next("p").slideToggle("slow")
				.siblings("p:visible").slideUp("slow");

				$(this).toggleClass("active");
				$(this).siblings("h3").removeClass("active");
			});
		});
	</script>';

	$content = '<div class="'.$class.'">';
	foreach($content_arr as $rec)
	{
		$content .= '<h3>'.$rec['header'].'</h3><p>'.$rec['content'].'</p>';
	}
	$content .= '</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Функция для показа содержимого компонента в виде сайдбара.
	@param $idComponent - идентификатор компонента, который прячется или опказывается;
	@param $params - массив параметров для создания сайдбара;
	@param $isDraw - вывести на экран или вернуть html-код.
	@return Выводит или возвращает HTML-код текста.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createSideBarPanel($idComponent, $params=array(array('header' => 'Some header for text!', 'text' => 'Some hide text')), $isDraw = false)
{
	$content = '<div class="'.$idComponent.'">';
	foreach($params as $rec)
	{
		$content .= '<h1>'.$rec['header'].'</h1><div class="toggle">'.$rec['text'].'</div>';
	}
	$content .= '</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает слайдер для проверки на бота (Заменяет капчу).
	@param $idSlider - название компонента, в котором будет находится слайдер;
	@param $idStatusElement - идентификатор элемента формы, куда будет сохранено значение проверки;
	@param $idForm - идентификатор формы, данные из которой следует отправлять вслучае успешного результата проверки;
	@param $textREsult - текс результата успешной проверки;
	@param $isDraw - Выводить или вернуть HTML-код слайдера.
	@return Показывает слайдер для проверки "ботов".
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 10.03.2011*/
function shav_createSlider($idSlider, $idStatusElement, $idFrom, $message = 'turn off the lights!', $textREsult = '1', $isDraw = false)
{
	global $shavJS;
	
	$shavJS->content = '<script type="text/javascript">
		$(function(){
			var s2 = new Slider("'.$idSlider.'",{
				message: "'.$message.'",
				color: "red",
				handler: function(){
					$("input#'.$idStatusElement.'").val("'.$textREsult.'");
					$("form#'.$idFrom.'").submit();
				}
			});
			s2.init();
		});
	</script>';
	
	$content = '<div id="'.$idSlider.'" width="200px"><div class="track"><div class="track-left"></div><div class="track-right"></div><div class="track-center"><div class="track-message">turn off the lights!</div></div><div style="background-position: 0pt 39px;" class="handle"></div></div></div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}


/** Создает поле формы для ввода даты.
	@param $idField - идентификатор поля формы;
	@param $value - значение поля формы;
	@param $isDraw - Выводить или вернуть html-код.
	@return Поле формы с календарем.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 10.03.2011*/
function shav_createCalendarFieldRus($idField, $value = '', $size = 25,  $isDraw = false)
{
	$shavJS->content = '<script type="text/javascript">
		$(function() {
			$("#'.$idField.'").datepicker({ dateFormat: \'dd.mm.yy\', dayNamesMin: [\'Вс\', \'Пн\', \'Вт\', \'Ср\', \'Чт\', \'Пт\', \'Сб\'], firstDay: 1, monthNames: [\'Январь\',\'Февраль\',\'Март\',\'Апрель\',\'Май\',\'Июнь\',\'Июль\',\'Август\',\'Сентябрь\',\'Октябрь\',\'Ноябрь\',\'Декабрь\'] });

		});
	</script>';

	$content = '<div class="demo">
		<input id="'.$idField.'" name="'.$idField.'" type="text" size="'.$size.'" value="'.$value.'" /></p>
	</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает рекламный слайдер.
	@param $params - массив рекламного контента;
	@param $isDraw - Выводить или вернять html-код.
	@return Рекламный блок с авто прокруткой.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 20.08.2009
*	@date Обновленно: 06.02.2011*/
function shav_createTabbedRotator($params, $isDraw = false)
{
	if(!is_array($params) && $isDraw == true)
	{
		echo ERROR_ARRAYS_0100;
		return;
	}
	elseif(!is_array($params) && $isDraw == false)
		return ERROR_ARRAYS_0100;

	if(empty($params) && $isDraw == true)
	{
		echo MSG_ARRAYS_0002;
		return;
	}
	elseif(empty($params) && $isDraw == false)
		return MSG_ARRAYS_0002;

	$content  = '<div id="rotator">';
	$content .= '<ul class="ui-tabs-nav">';

	//Выводим все табы (кнопки)
	$i = 1;
	foreach($params['tabsArray'] as $tab)
	{
		if($i == 1)
			$content .= '<li class="ui-tabs-nav-item ui-tabs-selected" id="nav-fragment-'.$i.'"><a href="#fragment-'.$i.'"><span>'.$tab.'</span></a></li>';
		else
			$content .= '<li class="ui-tabs-nav-item" id="nav-fragment-'.$i.'"><a href="#fragment-'.$i.'"><span>'.$tab.'</span></a></li>';

		$i++;
	}
	$content .= '</ul>';

	//Выводим контент для всех табов
	$i = 1;
	foreach($params['contentsArray'] as $tabContent)
	{
		$content .= '<div id="fragment-'.$i.'" class="ui-tabs-panel" style="">';
		$content .= $tabContent;
		$content .= '</div>';

		$i++;
	}
	$content .= '</div>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}
?>