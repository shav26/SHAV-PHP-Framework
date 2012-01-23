<?php
/** Подключаем jQuery.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 27.02.2011*/
function shav_jQuery($isDraw = false)
{
	$content  = '<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>';
	$content .= '<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>';
//	$content .= '<script type="text/javascript" src="/js/jqDnR.js"></script>';
//	$content .= '<script type="text/javascript" src="/js/dimensions.js"></script>';
	$content .= '<link rel="stylesheet" type="text/css" href="/css/ui-lightness/jquery-ui-1.7.2.custom.css" />';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;

}

/** Подключает плагин работы с модальными окнами.
 *	@param $isDraw - выводить или вернуть html-код.
 *	@return Выводит или возврящает код для подключения скриптов.
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 24.10.2009
 *	@date Обновленно: 05.02.2011*/
function shav_jQueryWindow($isDraw = false)
{
	$content  = '<link type="text/css" href="/js/jquery-window/css/jquery.window.css" rel="stylesheet" />';
	$content .= '<script type="text/javascript" src="/js/jquery-window/jquery.window.js"></script>';
	
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Подколючает плагин для создания слайдера.
	@param $isDraw - выводить или вернуть html-код.
	@return Выводит или возврящает код для подключения скриптов.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_loadSlider($isDraw = false)
{
	$content  = '<script type="text/javascript" src="/js/slider/slider.js"></script>';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/slider/slider.css"/>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Позволяет создавать SideBar, которые можно сворачивать и разворачивать.
	@param $params - массив параметров для настройки скрипта. array('idcomponent'=>'', 'show_text'=>'Show', 'hide_text'=>'Hide');
	@param $isDraw - выводить или вернуть html-код.
	@return Выводит или возврящает код для подключения скриптов.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_jQuerySideBar($params, $isDraw = false)
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

	$idComponent = $params['idcomponent'];

	$content = '<script type="text/javascript">
	$(document).ready(function(){
		//Проверяем на отображение
		var is_visible = true;

		//Добавляем ссылку для того чтобы показать или спрятать текст
		$(\''.$idComponent.' .toggle\').prev().append(\' <a href="#" class="toggleLink">'.$params['hide_text'].'</a>\');

		//Прячим весь текст
		$(\''.$idComponent.' .toggle\').show();

		//обрабатываем клик на ссылку для показа или для того чтобы спрятать текст
		$(\''.$idComponent.' a.toggleLink\').click(function() {
			//Меняем статус отображенеие на противоположный
			is_visible = !is_visible;
			//Меняем ссылку на противоположную
			$(this).html( (!is_visible) ? \''.$params['show_text'].'\' : \''.$params['hide_text'].'\');

			//Отображаем текст
			$(this).parent().next(\''.$idComponent.' .toggle\').toggle(\'slow\');

			// return false so any link destination is not followed
			return false;
		});
	});
	</script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Подключает плагин для создания галереи.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_slideViewer($isDraw = false)
{
	$content  = '<script type="text/javascript" src="/js/sideviewer/jquery.slideViewerPro.1.0.js"></script>';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/sideviewer/svwp_style.css" />';
	$content .= '<script src="/js/sideviewer/jquery.timers.js" type="text/javascript"></script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Функция включает плагин для работы с диограмами.
	@param $enabledPlg - массив с перечнем плагинов, которые можно подключать (смотреть можно в папке /js/jqPlot/plugins, название плагина barRenderer, без jqplot. и .min.js);
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования плагина.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_loading_jqPloat($enabledPlg = array(), $isDraw = false)
{
	$content  = '<link rel="stylesheet" type="text/css" href="/js/jqPlot/jquery.jqplot.min.css"/>';
	$content .= '<script type="text/javascript" src="/js/jqPlot/jquery.jqplot.min.js"></script>';
	$content .= '<script type="text/javascript" src="/js/jqPlot/excanvas.min.js"></script>';
	if(!empty($enabledPlg))
	{
		foreach($enabledPlg as $plg)
		{
			$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.'.$plg.'.min.js"></script>';
		}
	}
	else
	{
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.barRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasTextRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.categoryAxisRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.cursor.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.dragable.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.highlighter.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.logAxisRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.mekkoAxisRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.mekkoRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.ohlcRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.pieRenderer.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.pointLabels.min.js"></script>';
		$content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.trendline.min.js"></script>';
	}

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создаем рекламный слайдер с несколькими табами.
	@param $idRotator - идентификатор компонента куда следует вывести ротатор;
	@param $duration - задержка при сменне табов автоматически (1000 = 1 секунда);
	@param $isDraw - выводить или вернуть html-код.
	@return Подключает плагин.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_tabbedRotator($idRotator, $duration = 4000, $isDraw = false)
{
	$content = '<script src="/js/jquery-ui-personalized-1.5.3.packed.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#'.$idRotator.' > ul").tabs({fx:{opacity: "toggle"}}).tabs("rotate", '.$duration.', true);
	});
	</script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Подключает плагин jqModal для создания окон.
	@param $isDraw - Выводить или вернуть html-код.
	@param Выводит скрипты для использования плагина.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_jqModal($isDraw = false)
{
	$content = '<link rel="stylesheet" type="text/css" href="/js/jqModal/windows_style.css"/>';
	$content .= '<script type="text/javascript" src="/js/jqModal/jqModal.js"></script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Позволяет создавать меню ввиде сниток.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_LickMenu($isDraw = false)
{
	//Menu jslickmenu
	$content  = '<link rel="stylesheet" type="text/css" href="/js/jslickmenu/style.css"/>';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/jslickmenu/slickmenu.css"/>';
	$content .= shav_jQuery();
	$content .= '<script type="text/javascript" src="/js/jslickmenu/jquery.jslickmenu.js"></script>';
	$content .= '<script type="text/javascript" src="/js/jslickmenu/config.js"></script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Позволяет сосздавать меню с эффектом как в Маке нижняя панель.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_FishEye($isDraw = false)
{
	//Меню "Рыбий глаз"
	$content = '<link rel="stylesheet" type="text/css" href="/js/fisheye/style.css" />';
	$content .= '<script type="text/javascript" src="/js/fisheye/jquery.jqDock.js"></script>';
	$content .= '<script type="text/javascript" src="/js/fisheye/config.js"></script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Создает модальные окна.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_ModalWindow($isDraw = false)
{
	//Модальные окна
	$content = '<link rel="stylesheet" type="text/css" href="/js/simplemodal/basic.css" />';
	$content .= '<script type="text/javascript" src="/js/simplemodal/jquery_simplemodal.js"></script>';
	$content .= '<script type="text/javascript" src="/js/simplemodal/config.js"></script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Выводит картинки в модальном окне.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_ImagesInModal($isDraw = false)
{
	//Просмотр картинок в модальном окне
	$content = '<link rel="stylesheet" type="text/css" href="/js/thickbox/thickbox.css" />';
	$content .= '<script type="text/javascript" src="/js/thickbox/thickbox.js"></script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Функция для создания галерии ввиде CoverFlow.
	@param $isDraw - Выводить или вернуть html-код.
	@return Возвращает или выводт скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_MooFlow($isDraw = false)
{
	//Галерея ввиде coverflow
	$content  = '<link rel="stylesheet" type="text/css" href="/js/mooflow/style.css" />';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/mooflow/MooFlow.css" />';
	$content .= '<script type="text/javascript" src="/js/mooflow/mootools-1.2-core.js"></script>';
	$content .= '<script type="text/javascript" src="/js/mooflow/mootools-1.2-more.js"></script>';
	$content .= '<script type="text/javascript" src="/js/mooflow/MooFlow.js"></script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Подключает плагин для создания простой галереии.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_gallery_jcarousel($isDraw = false)
{
	$content  = '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/main.css" />';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/jcarousel/lib/jquery.jcarousel.css" />';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/jcarousel/skins/tango/skin.css" />';
	
	$content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jquery.galleria.min.js"></script>';
	$content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jcarousel/lib/jquery.jcarousel.pack.js"></script>';
	$content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jquery.hotkeys-0.7.8-packed.js"></script>';
	
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Подключает плагин Gallery View.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_galleryView($isDraw = false)
{
	$content  = '<script type="text/javascript" src="/js/galleryview/jquery.easing.1.3.js"></script>';
	$content .= '<script type="text/javascript" src="/js/galleryview/jquery.galleryview-2.0-pack.js"></script>';
	$content .= '<script type="text/javascript" src="/js/galleryview/jquery.timers-1.1.2.js"></script>';
	$content .= '<link rel="stylesheet" type="text/css" href="/js/galleryview/galleryview.css" />';
	
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Изменяет текст с тегом #news его можно поменять.
	@param $isDraw - Выводить или вернуть html-код.
	@return Выводит скрипты для использования библиотеки.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_ChangeText($isDraw = false)
{
	//Смена текста
	$content .= shav_jQuery();
	$content .= '<script type="text/javascript" src="/js/innerfade/jquery.innerfade.js"></script>';
	$content .= '<script type="text/javascript" src="/js/innerfade/config.js"></script>';

	/*Расшифрую вам код который не находится в отдельном файле:
	'#news', 'ul#portfolio', '.fade' - это элементы, которые будут скрываться.
	animationtype - тип анимации: 'fade' (растворение) или 'slide' (слайд).
	speed - скорость в миллисекундах или в словах 'slow' (медленно), 'normal' (нормально) или 'fast' (быстро).
	timeout - время исчезания, в миллисекундах.
	type - тип чередования: 'sequence' (по порядку), 'random' (случайно) или 'random_start' (случайное начало).
	containerheight - высота контейнера или другого элемента, по-умолчанию 'auto'.
	runningclass - класс, который присваивается изменяемому элементу скриптом. */

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Позволяет выделять исходный текст програм в сайтах.
	@param $isDraw - выводить или вернуть html-код.
	@return Подключает скрипты и проводит поверхносную настройку редактора.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_DeveloperLangs($isDraw = false)
{
	$content  = '<script type="text/javascript" src="/js/dev_lang/shCore.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushBash.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCpp.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCSharp.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCss.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushDelphi.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushDiff.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushGroovy.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushJava.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushJScript.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPhp.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPlain.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPython.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushRuby.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushScala.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushSql.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushVb.js"></script>';
	$content .= '<script type="text/javascript" src="/js/dev_lang/shBrushXml.js"></script>';
	$content .= '<link type="text/css" rel="stylesheet" href="/js/dev_lang/styles/shCore.css"/>';
	$content .= '<link type="text/css" rel="stylesheet" href="/js/dev_lang/styles/shThemeDefault.css"/>';
	$content .= '<script type="text/javascript">
			SyntaxHighlighter.config.clipboardSwf = \'/js/dev_lang/clipboard.swf\';
			SyntaxHighlighter.all();
		</script>';

	//Выводим результат
	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Добавляет редактор для постов на сайт.
	@param $isDraw - выводить или вернуть html-код.
	@return Подключает скрипты и проводит поверхносную настройку редактора.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_tiniMCE($isDraw = false)
{
	$content .= '<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>';
	$content .= '<script type="text/javascript" src="/js/tiny_mce/config.js"></script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}

/** Позволяет делать таблицы с раскрывающимися строками.
	@param $isDraw - выводить или вернуть html-код.
	@return Подключает скрипты и проводит поверхносную настройку таблицы.
*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
*	@date Созданно: 24.10.2009
*	@date Обновленно: 05.02.2011*/
function shav_SlideTable($isDraw = false)
{
	$content  = '<link href="/css/slide_table.css" rel="stylesheet" rev="stylesheet" type="text/css" />';
	$content .= '<script type="text/javascript">
        $(document).ready(function(){
            $("#report tr:odd").addClass("odd");
            $("#report tr:not(.odd)").hide();
            $("#report tr:first-child").show();

            $("#report tr.odd").click(function(){
                $(this).next("tr").toggle();
                $(this).find(".arrow").toggleClass("up");
            });
            //$("#report").jExpand();
        });
    </script>';

	if($isDraw == true)
		echo $content;
	else
		return $content;
}
?>