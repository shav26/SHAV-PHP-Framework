<?php
/** @class SHAV_JavaScript
 *	@brief Класс для конфигурирования JavaScript для сайта.
 *	Пример использования:
 *	@code
 * $shavJS = new SHAV_JavaScript();
 * $shavJS->shav_FishEye();
 * $shavJS->shav_tiniMCE();
 * $shavJS->shav_jqModal();
 *	@endcode
 *	Внутри других скриптов следует использовать такой код:
 *	@code
 * include_once('/inc/shav_confiv.php');
 * global $shavJS;
 * $shavJS->content .= '<script>Какой-то скрипт на JavaScript</script>';
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 09.03.2011
 *	@date Обновленно: */
class SHAV_JavaScript extends SHAV_Object
{
	/** HTML-код подключенных скриптов.*/
	public $content = '';
	
	/** Конструктор класса. Поуполчанию подключает только jQuery.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 09.03.2011
	*	@date Обновленно: */
	function SHAV_JavaScript()
	{
		$this->content = '';
		$this->shav_jQuery();
	}
	
	/** Подключаем jQuery.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 27.02.2011*/
	function shav_jQuery()
	{
		$this->content  = '<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/jqDnR.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dimensions.js"></script>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/css/ui-lightness/jquery-ui-1.7.2.custom.css" />';
	}

	/** Подключает плагин работы с модальными окнами.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_jQueryWindow()
	{
		$this->content .= '<link type="text/css" href="/js/jquery-window/css/jquery.window.css" rel="stylesheet" />';
		$this->content .= '<script type="text/javascript" src="/js/jquery-window/jquery.window.js"></script>';
	}

	/** Подколючает плагин для создания слайдера.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_loadSlider()
	{
		$this->content .= '<script type="text/javascript" src="/js/slider/slider.js"></script>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/slider/slider.css"/>';
	}

	/** Позволяет создавать SideBar, которые можно сворачивать и разворачивать.
	*	@param $params - массив параметров для настройки скрипта. array('idcomponent'=>'', 'show_text'=>'Show', 'hide_text'=>'Hide');
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_jQuerySideBar($params)
	{
		if(!is_array($params))
		{
			echo ERROR_ARRAYS_0100;
			return;
		}

		if(empty($params) && $isDraw == true)
		{
			echo MSG_ARRAYS_0002;
			return;
		}

		$idComponent = $params['idcomponent'];

		$this->content .= '<script type="text/javascript">
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
	}
	
	/** Подключает плагин для создания галереи.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_slideViewer()
	{
		$this->content .= '<script type="text/javascript" src="/js/sideviewer/jquery.slideViewerPro.1.0.js"></script>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/sideviewer/svwp_style.css" />';
		$this->content .= '<script src="/js/sideviewer/jquery.timers.js" type="text/javascript"></script>';
	}

	/** Функция включает плагин для работы с диограмами.
	*	@param $enabledPlg - массив с перечнем плагинов, которые можно подключать (смотреть можно в папке /js/jqPlot/plugins, название плагина barRenderer, без jqplot. и .min.js);
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_loading_jqPloat($enabledPlg = array())
	{
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/jqPlot/jquery.jqplot.min.css"/>';
		$this->content .= '<script type="text/javascript" src="/js/jqPlot/jquery.jqplot.min.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/jqPlot/excanvas.min.js"></script>';
		if(!empty($enabledPlg))
		{
			foreach($enabledPlg as $plg)
			{
				$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.'.$plg.'.min.js"></script>';
			}
		}
		else
		{
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.barRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.canvasTextRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.categoryAxisRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.cursor.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.dragable.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.highlighter.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.logAxisRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.mekkoAxisRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.mekkoRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.ohlcRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.pieRenderer.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.pointLabels.min.js"></script>';
			$this->content .= '<script language="javascript" type="text/javascript" src="/js/jqPlot/plugins/jqplot.trendline.min.js"></script>';
		}
	}

	/** Создаем рекламный слайдер с несколькими табами.
	*	@param $idRotator - идентификатор компонента куда следует вывести ротатор;
	*	@param $duration - задержка при сменне табов автоматически (1000 = 1 секунда);
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_tabbedRotator($idRotator, $duration = 4000)
	{
		$this->content .= '<script src="/js/jquery-ui-personalized-1.5.3.packed.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			$("#'.$idRotator.' > ul").tabs({fx:{opacity: "toggle"}}).tabs("rotate", '.$duration.', true);
		});
		</script>';
	}

	/** Подключает плагин jqModal для создания окон.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_jqModal()
	{
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/jqModal/windows_style.css"/>';
		$this->content .= '<script type="text/javascript" src="/js/jqModal/jqModal.js"></script>';
	}

	/** Позволяет создавать меню ввиде сниток.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_LickMenu()
	{
		//Menu jslickmenu
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/jslickmenu/style.css"/>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/jslickmenu/slickmenu.css"/>';
		$this->content .= '<script type="text/javascript" src="/js/jslickmenu/jquery.jslickmenu.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/jslickmenu/config.js"></script>';
	}

	/** Позволяет сосздавать меню с эффектом как в Маке нижняя панель.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_FishEye()
	{
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/fisheye/style.css" />';
		$this->content .= '<script type="text/javascript" src="/js/fisheye/jquery.jqDock.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/fisheye/config.js"></script>';
	}

	/** Создает модальные окна.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_ModalWindow()
	{
		//Модальные окна
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/simplemodal/basic.css" />';
		$this->content .= '<script type="text/javascript" src="/js/simplemodal/jquery_simplemodal.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/simplemodal/config.js"></script>';
	}

	/** Выводит картинки в модальном окне.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_ImagesInModal()
	{
		//Просмотр картинок в модальном окне
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/thickbox/thickbox.css" />';
		$this->content .= '<script type="text/javascript" src="/js/thickbox/thickbox.js"></script>';
	}

	/** Функция для создания галерии ввиде CoverFlow.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_MooFlow()
	{
		//Галерея ввиде coverflow
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/mooflow/style.css" />';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/mooflow/MooFlow.css" />';
		$this->content .= '<script type="text/javascript" src="/js/mooflow/mootools-1.2-core.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/mooflow/mootools-1.2-more.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/mooflow/MooFlow.js"></script>';
	}
	
	/** Подключает плагин для создания простой галереии.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_gallery_jcarousel()
	{
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/main.css" />';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/jcarousel/lib/jquery.jcarousel.css" />';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/gallery_jcarousel/jcarousel/skins/tango/skin.css" />';

		$this->content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jquery.galleria.min.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jcarousel/lib/jquery.jcarousel.pack.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/gallery_jcarousel/jquery.hotkeys-0.7.8-packed.js"></script>';
	}

	/** Подключает плагин Gallery View.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_galleryView()
	{
		$this->content .= '<script type="text/javascript" src="/js/galleryview/jquery.easing.1.3.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/galleryview/jquery.galleryview-2.0-pack.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/galleryview/jquery.timers-1.1.2.js"></script>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/galleryview/galleryview.css" />';
	}

	/** Изменяет текст с тегом #news его можно поменять.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_ChangeText()
	{
		//Смена текста
		$this->content .= '<script type="text/javascript" src="/js/innerfade/jquery.innerfade.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/innerfade/config.js"></script>';

		/*Расшифрую вам код который не находится в отдельном файле:
		'#news', 'ul#portfolio', '.fade' - это элементы, которые будут скрываться.
		animationtype - тип анимации: 'fade' (растворение) или 'slide' (слайд).
		speed - скорость в миллисекундах или в словах 'slow' (медленно), 'normal' (нормально) или 'fast' (быстро).
		timeout - время исчезания, в миллисекундах.
		type - тип чередования: 'sequence' (по порядку), 'random' (случайно) или 'random_start' (случайное начало).
		containerheight - высота контейнера или другого элемента, по-умолчанию 'auto'.
		runningclass - класс, который присваивается изменяемому элементу скриптом. */
	}

	/** Позволяет выделять исходный текст програм в сайтах.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_DeveloperLangs()
	{
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shCore.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushBash.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCpp.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCSharp.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushCss.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushDelphi.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushDiff.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushGroovy.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushJava.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushJScript.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPhp.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPlain.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushPython.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushRuby.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushScala.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushSql.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushVb.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/dev_lang/shBrushXml.js"></script>';
		$this->content .= '<link type="text/css" rel="stylesheet" href="/js/dev_lang/styles/shCore.css"/>';
		$this->content .= '<link type="text/css" rel="stylesheet" href="/js/dev_lang/styles/shThemeDefault.css"/>';
		$this->content .= '<script type="text/javascript">
				SyntaxHighlighter.config.clipboardSwf = \'/js/dev_lang/clipboard.swf\';
				SyntaxHighlighter.all();
			</script>';
	}

	/** Добавляет редактор для постов на сайт.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 24.10.2009
	*	@date Обновленно: 05.02.2011*/
	function shav_tiniMCE()
	{
		$this->content .= '<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>';
		$this->content .= '<script type="text/javascript" src="/js/tiny_mce/config.js"></script>';
	}

	/** Добавляет возможность загружать файлы используя Ajax технологию.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 10.04.2011
	*	@date Обновленно: */
	function shav_AjaxFileUplode()
	{
		$this->content .= '<script type="text/javascript" src="/js/ajax_uploader/fileuploader.js"></script>';
		$this->content .= '<link rel="stylesheet" type="text/css" href="/js/ajax_uploader/fileuploader.css" />';
	}

	/** Выводит содержимое подключенных скриптов.
	*	@param $isDraw - Выводить или нет HTML-код настройки javaScript на странице.
	*	@return HTML-код настройки JavaScript.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 09.03.2011
	*	@date Обновленно: */
	function drawJS()
	{
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
}
?>