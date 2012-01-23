<?php
/** @class SHAV_jqModal
 *	@brief Класс для настройки плагина jqModal.
 *	Пример использования:
 *	@code
 * $wnd = new SHAV_jqModal();
 * $wnd->windowId = 'comment_edit_'.(int)$comment->id;
 * $wnd->title = 'Редактирование комментария';
 * $wnd->wndSize = array('width'=>'620px');
 * $wnd->windowContent = $content;
 * $wnd->linkId = $wnd->windowId.'Trigger';
 * $wnd->linkName = 'Изменить';
 * $wnd->drawModalWindow(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 14.02.2010
 *	@date Обновленно: 06.02.2011*/
class SHAV_jqModal extends SHAV_Object
{
	/** Идентификатор компонента.*/
	public $windowId = '';

	/** Размер окна.*/
	public $wndSize = array('width'=>'500px', 'height'=>'180px');
	
	/** Положение окна относительно верхнего левого угла экрана.*/
	public $wndPosition = array('top'=>'10%', 'left'=>'45%');
	
	/** Ссылка на картинку для кнопки закрытия окна.*/
	public $closeImg = '/js/jqModal/img/close.gif';
	
	/** Содержимое окна.*/
	public $windowContent = '';
	
	/** Заголовок окна.*/
	public $title = 'Test Window';

	/** Текст ссылки, которая будет запускать окно.*/
	public $linkName = 'Show the window';
	
	/** Идентификатор ссылки.*/
	public $linkId = '';
	
	/** Ссылка для запуска окна.*/
	public $linkUrl = '#';
	
	/** Прозрачность в %.*/
	public $transparent = 30;

	/** Массив под окон (не обязателен).*/
	public $subWindows = array();

	/** Конструктор класса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function SHAV_jqModal()
	{}

	/** Выводит содержимое данного обекта.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawInfo()
	{
		DrawObject($this);
	}
	
	/** Выводит ссылку и окно.
	 *	@param $type - Тип окна: style_dialog - одно окно, nested_modal - окно с ajax и под окном;
	 *	@param $isDraw - вывести или вернуть html-код блока.
	 *	@return Создает окно.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 06.02.2011*/
	function drawModalWindow($type='style_dialog', $isDraw = false)
	{
		global $shavJS;
		
		$divsWidth = '#'.$this->windowId.'.jqmDialog';
		$divsHeight = '#'.$this->windowId.' .jqmdBC';
		foreach($this->subWindows as $wnd)
		{
			$divsWidth .= ', #'.$wnd->windowId.'.jqmDialog';
			$divsHeight .= '#'.$this->windowId.' .jqmdBC';
		}
		
		$shavJS->content .= '<style>
		'.$divsWidth.' {
			display: none;
			
			position: fixed;
			top: '.$this->wndPosition['top'].';
			left: '.$this->wndPosition['left'].';
			
			margin-left: -200px;
			width: '.$this->wndSize['width'].';
			
			overflow: hidden;
			font-family:verdana,tahoma,helvetica;
		}
		
		'.$divsHeight.' {
			background: url("/js/jqModal/img/bc.gif") repeat-x center bottom;
			padding: 7px 7px 7px;
			height: '.$this->wndSize['height'].'px;
			overflow: auto;
		}
		</style>';
		$shavJS->content .= '<script type="text/javascript">';
		
		$trigger = 'trigger: \'#'.$this->linkId.'\',
		overlay: '.$this->transparent.', /* 0-100 (int) : 0 is off/transparent, 100 is opaque */
		overlayClass: \'whiteOverlay\'})
		.jqDrag(\'.jqDrag\'); /* make dialog draggable, assign handle to title */';
		
		$subModalWindow = '';
		$t = '';
		if(is_array($this->subWindows) && !empty($this->subWindows))
		{
			$i = 1;
			foreach($this->subWindows as $wnd)
			{
				$subModalWindow .= '// nested dialog_'.$i.'
				$(\'#'.$wnd->windowId.'\').jqm({modal: true, overlay: '.$wnd->transparent.', trigger: false});';
				$i++;
			}
			$t = 'public t = $(\'#'.$this->windowId.' div.jqmdMSG\');';
			$trigger = 'trigger: \'a.'.$this->linkId.'\',
							ajax: \'@href\',
							target: t,
							modal: true, /* FORCE FOCUS */
							onHide: function(h) {
				t.html(\'Please Wait...\');  // Clear Content HTML on Hide.
				h.o.remove(); // remove overlay
				h.w.fadeOut(888); // hide window
			},
				  overlay: 0});';
		}
		
		
		$shavJS->content .= '$().ready(function() {
				'.$t.'
				$(\'#'.$this->windowId.'\').jqm({
					'.$trigger.'
					'.$subModalWindow.'
					
					// Close Button Highlighting. IE doesn\'t support :hover. Surprise?
					$(\'input.jqmdX\')
					.hover(
					function(){ $(this).addClass(\'jqmdXFocus\'); },
						   function(){ $(this).removeClass(\'jqmdXFocus\'); })
						   .focus(
						   function(){ this.hideFocus=true; $(this).addClass(\'jqmdXFocus\'); })
		.blur(
		function(){ $(this).removeClass(\'jqmdXFocus\'); });
		
		});';
		$shavJS->content .= '</script>';


		switch($type)
		{
			case 'style_dialog':
				$content  = '<a href="'.$this->linkUrl.'" id="'.$this->linkId.'">'.$this->linkName.'</a>';
				$content .= '<div id="'.$this->windowId.'" class="jqmDialog">';
				$content .= '<div class="jqmdTL"><div class="jqmdTR"><div class="jqmdTC jqDrag">'.$this->title.'</div></div></div>';
				$content .= '<div class="jqmdBL"><div class="jqmdBR"><div class="jqmdBC">';
				$content .= '<div class="jqmdMSG">'.$this->windowContent.'</div>';
				$content .= '</div></div></div>';
				$content .= '<input type="image" src="'.$this->closeImg.'" class="jqmdX jqmClose" />';
				$content .= '</div>';
				break;
			case 'nested_modal':
				$content  = '<a href="'.$this->linkUrl.'" class="'.$this->linkId.'">'.$this->linkName.'</a>
				<div id="'.$this->windowId.'" class="jqmDialog jqmdWide">
					<div class="jqmdTL">
						<div class="jqmdTR">
							<div class="jqmdTC">'.$this->title.'</div>
						</div>
					</div>
					<div class="jqmdBL">
						<div class="jqmdBR">
							<div class="jqmdBC">
								<div class="jqmdMSG"><p>Please wait... <img src="js/jqModal/img/busy.gif" alt="loading" /></p></div>
							</div>
						</div>
					</div>
					<input type="image" src="'.$this->closeImg.'" class="jqmdX jqmClose" />
				</div>';

				foreach($this->subWindows as $wnd)
				{
					$content .= '<!-- nested dialog -->';
					$content .= '<div id="'.$wnd->windowId.'" class="jqmDialog jqmdAbove">';
					$content .= '	<div class="jqmdTL">
					<div class="jqmdTR">
					<div class="jqmdTC">'.$wnd->title.'</div>
					</div>
					</div>
					<div class="jqmdBL">
					<div class="jqmdBR">
					<div class="jqmdBC jqmdTall">
					<div class="jqmdMSG">'.$wnd->windowContent.'</div>
					</div>
					</div>
					</div>';
					$content .= '	<input type="image" src="'.$wnd->closeImg.'" class="jqmdX jqmClose" />';
					$content .= '</div>';
				}
				break;
		}

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}

/** @class SHAV_jqPlot
 *	@brief Класс для создания диограм и графиков.
 *	Пример использования:
 *	@code
 * $sql = 'SELECT * FROM poll_results';
 * $plot = new SHAV_jqPlot();
 * $plot->idDiagram = 'plot';
 * $plot->title = 'Результаты голосования';
 * $plot->createFromSQLQuery($sql, 'user_name', 'value');
 * $plot->drawDiogram(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 14.02.2010
 *	@date Обновленно: 09.03.2011*/
class SHAV_jqPlot extends SHAV_Object
{
	/** Заголовок графика.*/
	public $title = 'График';

	/** Значение на оси X*/
	public $dataValues = array(array(2, 10, 20));
	
	/** значение на оси Y.*/
	public $rowsTitle = array('row 1', 'row2', 'Row 3');
	
	/** Максимальное значение для оси X.*/
	public $maxValue = 20;
	
	/** Минимальное значение для оси X.*/
	public $minValue = 0;
	
	/** */
	public $korrection = 10;
	
	/** Идентификатор компонента для вывода.*/
	public $idDiagram = 'chart';
	
	/** Позиция легенды (принцип компаса).*/
	public $legendPos = 'se';
	
	/** Легенда для графиков.*/
	public $labelLegends = array('line 1');
	
	/** Список подписей над вершинами.*/
	public $pointLabels = array('fourteen', 'thirty two', 'fourty one', 'fourty four', 'fourty');
	
	/** Шаг.*/
	public $numberTicks = 5;
	
	/** Стиль элемента для вывода в div.*/
	public $styleDiv = 'margin-top:20px; margin-left:50px; width:900px; height:400px;';

	/** Содержимое для вывода.*/
	public $content = '';
	
	/** */
	public $marginRow = 40;
	
	/** */
	public $paddingRow = 6;
	
	/** Расчитывать пройентное соотношение.*/
	public $isPracent = false;

	/** Конструктор класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 09.03.2011*/
	function SHAV_jqPlot()
	{
	}

	/** Создать диограму из таблиц базы данны использюя SQL-запрос.
	 *	@param $sql - запрос для базы на получение данных;
	 *	@param $titleField - название столбца таблицы из БД, который нужно использовать в качестве заголовков строк;
	 *	@param $valueField - значение для построения диограм.
	 *	@return Создаст диограму из данных базы
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 09.03.2011*/
	function createFromSQLQuery($sql, $titleField, $valueField)
	{
		global $shavDB;

		if($sql == '')
		{
			$this->content = '<b class="error">Не запроса для выполнений</b>';
			return;
		}

		$results = $shavDB->get_results($sql);
		$this->dataValues = array();	//Очищаем массив значений, ось X
		$this->rowsTitle = array();		//Очищаем массив значений, ось У
		$array = array();				//Количество линий (графиков), из БД получается только один.
		$this->pointLabels = array();	//Очищаем подписи
		foreach($results as $rec)
		{
			$array[] = $rec[$valueField];
			$this->rowsTitle[] = str_replace(' ', '&nbsp;', $rec[$titleField]);
		}
		$this->maxValue = max($array);
		$this->dataValues[] = $array;

		if($this->isPracent == true)
		{
			foreach($array as $rec)
			{
				$prc = ($rec / ($this->maxValue+$this->korrection)) * 100;
				$this->pointLabels[] = number_format($prc, 2, ',', '').' %';
			}
		}

		$this->createContent();
	}

	/** Выводим содержимое диограммы.
	 *	@param $isDraw - вывести или вернуть html-код блока.
	 *	@return Выводится окно.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 09.03.2011*/
	function drawDiogram($isDraw = false)
	{
		if($this->content == '')
			$this->createContent();
		
		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}

	/** Создаем контент будущей диограммы
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 14.02.2010
	 *	@date Обновленно: 09.03.2011*/
	private function createContent()
	{
		global $shavJS;
		
		//Создаем массивы данных
		$lines = '';
		$linesArr = '';
		$allLines = '';
		$i = 1;
		foreach($this->dataValues as $data)
		{
			$lines .= 'line'.$i.' = [';
			$k = 1;
			foreach($data as $value)
			{
				$lines .= '['.$value.', '.$k.'], ';
				$k++;
			}
			$allLines .= substr($lines, 0, strlen($lines)-2).'];';

			$linesArr .= 'line'.$i.', ';
			$i++;
		}

		$linesArr = substr($linesArr, 0, strlen($linesArr)-2);

		//Создаем легенду
		$series = 'series: [';
		foreach($this->labelLegends as $str)
		{
			$series .= '{label: \''.$str.'\'}, ';
		}
		$series = substr($series, 0, strlen($series)-2).']';

		//Создаем названия строк диограммы
		$rowsName = '[';
		foreach($this->rowsTitle as $titleRow)
		{
			$rowsName .= '\''.$titleRow.'\', ';
		}
		$rowsName = substr($rowsName, 0, strlen($rowsName)-2).']';

		//Создаем подписи
		if(!empty($this->pointLabels))
		{
			$pointLabels = 'labels:[';
			foreach($this->pointLabels as $lbl)
			{
				$pointLabels .= '\''.$lbl.'\', ';
			}
			$pointLabels = substr($pointLabels, 0, strlen($pointLabels)-2).'],';
		}
		else $pointLabels = '';
		
		//Настройка и вывод диограммы
		$shavJS->content .= "<script type=\"text/javascript\">
		$(document).ready(function(){
			".$allLines."
			plot_".$this->idDiagram." = $.jqplot('".$this->idDiagram."', [".$linesArr."], {
				seriesColors: [ \"#4bb2c5\", \"#c5b47f\", \"#EAA228\", \"#579575\", \"#839557\", \"#958c12\", \"#953579\", \"#4b5de4\", \"#d8b83f\", \"#ff5800\", \"#0085cc\"],
				stackSeries: true,
				legend: {
					show: false,
					location: '".$this->legendPos."'
				},
				grid:{
					background: 'rgba(255,255,255,0.0)',
					drawGridLines: false,
					borderColor: 'rgba(255,255,255,0.0)',
					borderWidth: 0,
					shadow: false
				},
				title: '".$this->title."',
				seriesDefaults: {
					renderer: $.jqplot.BarRenderer,
					rendererOptions: {
						barDirection: 'horizontal',
						barPadding: ".$this->paddingRow.",
						barMargin: ".$this->marginRow."
					},

					pointLabels:{
						".$pointLabels."
						ypadding:6,
						stackedValue: true
					}
				},
				$series,
				axes: {
					yaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						ticks: ".$rowsName."
					},
					xaxis: {min: ".$this->minValue.", max: ".($this->maxValue+$this->korrection).", numberTicks:".$this->numberTicks.", showTickMarks: true}
				}
			});
		});
		</script>";

		$this->content = '<div id="'.$this->idDiagram.'" style="'.$this->styleDiv.'"></div>';
	}
}

/** @class SHAV_jModalWindow_Button
 *	@brief Класс для создания кнопки в окне.
 *	Пример использования:
 *	@code
 * $btn = new SHAV_jModalWindow_Buttons();
 * $btn->id = 'asd';
 * //...
 * $btn->getJSON();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 27.02.2011
 *	@date Обновленно: */
class SHAV_jModalWindow_Button extends SHAV_Object
{
	/** Идентификатор кнопки.*/
	public $id = '';
	
	/** Заголовок кнопки, появляется при наведении.*/
	public $title = '';
	
	/** CSS стиль при нажатие кнопки.*/
	public $clazz = '';
	
	/** CSS стиль кнопки.*/
	public $style = '';
	
	/** Ссылка на иконку кнопки.*/
	public $image = '';
	
	/** jQuery код для работы кнопки. Например, можно понемять содержимое окна.*/
	public $fnc = '';

	/** Конструктор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 27.02.2011
	 *	@date Обновленно: */
	function SHAV_jModalWindow_Buttons()
	{}

	/** Настрока скриптов для работы.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 27.02.2011
	 *	@date Обновленно: */
	function getJSON()
	{
		$content .= '{ id: "'.$this->id.'",';
		$content .= 'title: "'.$this->title.'",';
		$content .= 'clazz: "'.$this->clazz.'",';
		$content .= 'style: "'.$this->style.'",';
		$content .= 'image: "'.$this->image.'",';
		$content .= 'callback: function(btn, wnd) {'.$this->fnc.'}';
		$content .= '}';
	}
}

/** @class SHAV_jModalWindow
 *	@brief Класс для создания модальных окон.
 *	Пример использования:
 *	@code
 * $modal = new SHAV_jModalWindow('window_1', 'Первое окно');
 * //Настройка окна.
 * $modal->drawLink('Название ссылки', true);
 * $modal->drawLinkWithContent('html_data', 'Название ссылки', true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 27.02.2011
 *	@date Обновленно: */
class SHAV_jModalWindow extends SHAV_Object
{
	/** Идентиифкатор окна. Используется для создания нескольких окон с разным содержимым.*/
	public $idWindow = 'window';
	
	/** Содержимое окна. Может быть как простой HTML-код так и jQuery код вида: $("#wndId").html(), где wndId - это id в div.*/
	public $content = '';

	/** Ссылка на страницу или сайт. Если указан, то параметр $content не используется*/
	public $url = '';
	
	/** Заголов окна.*/
	public $title = '';
	
	/** Содержимое нижней части окна (статус панель).*/
	public $footerContent = 'html-код для нижней панели.';
	
	/** Проверить диалоговое окно на переполнения HTML тела или вызывающего элемента*/
	public $boundary = 'true';
	
	/** Размер окна. Может иметь вид: array('width'=>200, 'height'=>160)*/
	public $size = array('width'=>600, 'height'=>400);
	
	/** Минимальный размер окна. Параметр имеет вид: array('width'=>400, 'height'=>300).*/
	public $minSize = array();
	
	/** Максимальный размер окна. Параметр имеет вид: array('width'=>400, 'height'=>300).*/
	public $maxSize = array();
	
	/** Позиция окна. Может иметь вид: array('x'=>80, 'y'=>'80', z='10'), где Z - это z-index;*/
	public $position = array();
	
	/** Положение свернутого окна. Может принимать такие значения: 'left', 'right', 'top', 'bottom'.*/
	public $dock = 'bottom';
	
	/** Анимация сворачивания и разворачивания.*/
	public $animSpeed = 400;
	
	/** Минимальное значения для сворнотого окна по высоте заголовка.*/
	public $minWinNarrow = 24;
	
	/** Минимальное значения для сворнотого окна по шерене заголовка.*/
	public $minWinLong = 120;
	
	/** Для обработки браузером прокрутки окна, когда статус изменился (максимально, сведения к минимуму каскада).*/
	public $handleScrollbar = true;
	
	/** Вывод в консоль отладки (для IE8 и Chrome).*/
	public $showLog = false;
	
	/** Показывать кнопку, которая позволяет добавлять страницу, которая показывается в содержимом окна, в закладки.*/
	public $bookmarkable = false;
	
	/** показывать кнопку закрития окна.*/
	public $closable = true;
	
	/** Разрешить перемещать окно.*/
	public $draggable = true;
	
	/** Разрешить изменять размер окна.*/
	public $resizable = true;
	
	/** Показывать кнопку, которая позволяет развернуть на всю рабочую часть браузера.*/
	public $maximizable = true;
	
	/** Показывать кнопку, которая позволяет свернуть окно.*/
	public $minimizable = true;
	
	/** Показывать полосу прокрутки в окне.*/
	public $scrollable = false;

	//Callback функции
	/** Действия на открытия окна.*/
	public $onOpen = '';
	
	/** Действия на отображения окна.*/
	public $onShow = '';
	
	/** Действия на закрытия окна.*/
	public $onClose = '';
	
	/** Действия на выбор окна.*/
	public $onSelect = '';
	
	/** Действия на отмену выбора окна.*/
	public $onUnselect = '';
	
	/** Действия на перемещение окна.*/
	public $onDrag = '';
	
	/** Действия после окончания перемещения.*/
	public $afterDrag = '';
	
	/** Действия на изменения размера окна.*/
	public $onResize = '';
	
	/** Действия после окончания изменения размера окна.*/
	public $afterResiz = '';
	
	/** Действия на сворачивание окна.*/
	public $onMinimize = '';
	
	/** Действия после окончания сворачивания окна.*/
	public $afterMinimize = '';
	
	/** Действия на разворачивания окна.*/
	public $onMaximize = '';
	
	/** Действия после окончания разворачивания окна.*/
	public $afterMaximize = '';
	
	/** Действия на переход в каскадное представление окон.*/
	public $onCascade = '';
	
	/** Действия после перехода в каскадное представление окон.*/
	public $afterCascade = '';

	//Кнопки
	/** Массив кнопок в зголовке. Позволяет добавить дополнительные кнопки. Состоит из объектов класса SHAV_jModalWindow_Button.*/
	public $buttons = array();

	//CSS стили
	/** CSS стить для контейнера.*/
	public $containerClass = '';
	
	/** CSS стиль для заголовка окна.*/
	public $headerClass = '';
	
	/** CSS стиль рамки окна.*/
	public $frameClass = '';
	
	/** CSS стиль нижней панели окна.*/
	public $footerClass = '';
	
	/** CSS стиль выбранного окна.*/
	public $selectedHeaderClass = '';

	/** Показывать нижную панель окна.*/
	public $showFooter = false;
	
	/** Отображать окно с закруглунными углами.*/
	public $showRoundCorner = true;
	
	/** Создания закругления. Массив настроек имеет вид: array('x'=>200, 'y'=>150).*/
	public $createRandomOffset = array();
	
	
	/** Конструктор класса.
	 *	@param $id - идентификатор окна для поддержки нескольких окон.
	 *	@param $title - Заголовок окна.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 27.02.2011
	 *	@date Обновленно: */
	function SHAV_jModalWindow($id, $title = 'Новое окно.')
	{
		$this->idWindow = $id;
		$this->title = $title;
	}

	/** Выводит ссылку на окно.
	*	@param $link_name - текс для ссылки на окно;
	*	@param $isDraw - выводить или нет HTML-код ссылки.
	*	@return HTML-код ссылки.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 27.02.2011
	*	@date Обновленно: */
	function drawLink($link_name = '', $isDraw = false)
	{
		$this->createJSConfig();

		if($link_name == '')
			$link_name = $this->title;

		$content  = '<a href="#" onClick="show_'.$this->idWindow.'();">'.$link_name.'</a>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Выводит ссылку на окно.
	*	@param $link_name - текс для ссылки на окно;
	*	@param $html - HTML-код содержимого окна для вывода;
	*	@param $isDraw - выводить или нет HTML-код ссылки.
	*	@return HTML-код ссылки.
	*	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	*	@date Созданно: 27.02.2011
	*	@date Обновленно: */
	function drawLinkWithContent($link_name = '', $html = '', $isDraw = false)
	{
		$this->createJSConfig();

		if($link_name == '')
			$link_name = $this->title;

		$content  = '<a href="#" onClick="show_'.$this->idWindow.'();">'.$link_name.'</a>';
		$content .= '<div id="'.$this->idWindow.'" style="display: none;">'.$html.'</div>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Создает настройки JS.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 27.02.2011
	 *	@date Обновленно: */
	private function createJSConfig()
	{
		global $shavJS;
		
		$shavJS->content .= '<script type="text/javascript">function show_'.$this->idWindow.'(){';
		$shavJS->content .= 'var wnd = $.window({';
		$shavJS->content .= 'title: "'.$this->title.'", ';
		
		if($this->url != '')
			$shavJS->content .= 'url: "'.$this->url.'", ';
		else
		{
			if(substr($this->content, 0, 1) == '$')
				$shavJS->content .= 'content: '.$this->content.', ';
			else
				$shavJS->content .= 'content: "'.str_replace('"', '\"', $this->content).'", ';
		}
		
		$shavJS->content .= 'dock: \''.$this->dock.'\', ';
		
		if($this->animSpeed > 0)
			$shavJS->content .= 'animationSpeed: '.$this->animSpeed.',';
		
		if($this->minWinNarrow > 0)
			$shavJS->content .= 'minWinNarrow: '.$this->minWinNarrow.', ';
		
		if($this->minWinLong > 0)
			$shavJS->content .= 'minWinLong: '.$this->minWinLong.', ';

		if($this->handleScrollbar == true) $content .= 'handleScrollbar: true, ';
		else $shavJS->content .= 'handleScrollbar: false, ';

		if($this->showLog == true) $content .= 'showLog: true, ';
		else $content .= 'showLog: false, ';
			
		$shavJS->content .= 'footerContent: "'.str_replace('"', '\"', $this->footerContent).'", ';
		$shavJS->content .= 'checkBoundary: '.$this->boundary.', ';
		if(!empty($this->size))
			$shavJS->content .= 'width: '.$this->size['width'].', height: '.$this->size['height'].', ';
		if(!empty($this->maxSize))
			$shavJS->content .= 'maxWidth: '.$this->maxSize['width'].', maxHeight: '.$this->maxSize['height'].', ';
		if(!empty($this->minSize))
			$shavJS->content .= 'minWidth: '.$this->minSize['width'].', minHeight: '.$this->minSize['height'].', ';
		if(!empty($this->position))
			$shavJS->content .= 'x: '.$this->position['x'].', y: '.$this->position['y'].', z: '.$this->position['z'].', ';

		if($this->draggable == true) $content .= 'draggable: true, ';
		else $shavJS->content .= 'draggable: false, ';

		if($this->resizable == true) $content .= 'resizable: true, ';
		else $shavJS->content .= 'resizable: false, ';

		if($this->maximizable == true) $content .= 'maximizable: true, ';
		else $shavJS->content .= 'maximizable: false, ';

		if($this->minimizable == true) $content .= 'minimizable: true, ';
		else $shavJS->content .= 'minimizable: false, ';

		if($this->scrollable == true) $content .= 'scrollable: true, ';
		else $shavJS->content .= 'scrollable: false, ';

		if($this->closable == true) $content .= 'closable: true, ';
		else  $shavJS->content .= 'closable: false, ';
		
		if($this->bookmarkable == true) $content .= 'bookmarkable: true, ';
		else  $shavJS->content .= 'bookmarkable: false, ';
		
		if($this->onOpen != '')
			$shavJS->content .= 'onOpen: function(wnd) { '.$this->onOpen.' }, ';
		
		if($this->onShow != '')
			$shavJS->content .= 'onShow: function(wnd) { '.$this->onShow.' }, ';
		
		if($this->onClose != '')
			$shavJS->content .= 'onClose: function(wnd) { '.$this->onClose.' }, ';
		
		if($this->onSelect != '')
			$shavJS->content .= 'onSelect: function(wnd) { '.$this->onSelect.' }, ';
		
		if($this->onUnselect != '')
			$shavJS->content .= 'onUnselect: function(wnd) { '.$this->onUnselect.' }, ';
		
		if($this->onDrag != '')
			$shavJS->content .= 'onDrag: function(wnd) { '.$this->onDrag.' }, ';
		
		if($this->afterDrag != '')
			$shavJS->content .= 'afterDrag: function(wnd) { '.$this->afterDrag.' }, ';
		
		if($this->onResize != '')
			$shavJS->content .= 'onResize: function(wnd) { '.$this->onResize.' }, ';
		
		if($this->afterResize != '')
			$shavJS->content .= 'afterResize: function(wnd) { '.$this->afterResize.' }, ';
		
		if($this->onMinimize != '')
			$shavJS->content .= 'onMinimize: function(wnd) { '.$this->onMinimize.' }, ';
		
		if($this->afterMinimize != '')
			$shavJS->content .= 'afterMinimize: function(wnd) { '.$this->afterMinimize.' }, ';
		
		if($this->onMaximize != '')
			$shavJS->content .= 'onMaximize: function(wnd) { '.$this->onMaximize.' }, ';
		
		if($this->afterMaximize != '')
			$shavJS->content .= 'afterMaximize: function(wnd) { '.$this->afterMaximize.' }, ';
		
		if($this->onCascade != '')
			$content .= 'onCascade: function(wnd) { '.$this->onCascade.' }, ';
		
		if($this->afterCascade != '')
			$shavJS->content .= 'afterCascade: function(wnd) { '.$this->afterCascade.' }, ';
		
		if(!empty($this->buttons))
		{
			$btn = '';
			foreach($this->buttons as $button)
				$btn .= $button->getJSON().',';
			$btn = substr($btn, 0, strlen($btn)-2);
			$shavJS->content .= 'custBtns: ['.$btn.'],';
		}

		if($this->containerClass != '')
			$shavJS->content .= 'containerClass: "'.$this->containerClass.'", ';
		
		if($this->headerClass != '')
			$shavJS->content .= 'headerClass: "'.$this->headerClass.'", ';
		
		if($this->frameClass != '')
			$shavJS->content .= 'frameClass: "'.$this->frameClass.'", ';
		
		if($this->footerClass != '')
			$shavJS->content .= 'footerClass: "'.$this->footerClass.'", ';
		
		if($this->selectedHeaderClass != '')
			$shavJS->content .= 'selectedHeaderClass: "'.$this->selectedHeaderClass.'", ';

		if(!empty($this->createRandomOffset))
			$shavJS->content .= 'createRandomOffset: {x:'.$this->createRandomOffset['x'].', y:'.$this->createRandomOffset['y'].'}, ';
		
		if($this->showFooter == true) $content .= 'showFooter: true, ';
		else $shavJS->content .= 'showFooter: false, ';
		
		if($this->showRoundCorner) $content .= 'showRoundCorner: true';
		else $shavJS->content .= 'showRoundCorner: false';
		
		$shavJS->content .= '}); ';
		$shavJS->content .= ' wnd.show();}';
		$shavJS->content .= '</script>';
	}
}

/** @class SHAV_Table
 *	@brief Класс для создания таблицы с выподающим контентом. Пример кода:
 *	Пример использования:
 * 	@code $table = new SHAV_Table();
 * $table->id = 'table_test';
 * $table->title = 'Пример таблиц с выподающем содержимым.';
 * $table->titleHeader = array('Название', 'Описание');
 * $table->titleContent = array(array('title'=>array('Программирование', 'C/C++, Objective-C/C++, Qt/C++, C#, Java, JavaScript, PHP, Perl'), 'content'=>'Разработка программ для разных платформ.'));
 * $content .= $table->drawTable();
 *	@endcode
 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">Сайт</a>)
 *	@date Созданно: 12.03.2011
 *	@date Обновленно: */
class SHAV_Table extends SHAV_Object
{
	/** Идентификатор таблицы для работы с CSS и JavaScript*/
	public $id = '';
	
	/** Название таблица*/
	public $title = '';
	
	/** Массив с перечнем заголовков сталбцов таблицы*/
	public $titleHeader = array();
	
	/** Массив с данными по строкам таблицы, имеет вид: array(array('title'=>array(), 'content'=>''), ...)*/
	public $titleContent = array(array('title'=>array(), 'content'=>''));
	
	/** Толщина линий таблицы.*/
	public $border = 0;

	/** Конструктор
	 *	@param $id - идентификатор таблицы;
	 *	@param $title - заголовок таблицы.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">Сайт</a>)
	 *	@date Созданно: 12.03.2011
	 *	@date Обновленно: */
	function SHAV_Table($id = 'table_test', $title = 'Таблица')
	{
		$this->id = $id;
		$this->title = $title;
	}

	/** Выводит HTML-код таблицы.
	 *	@param $isDraw - выводить или нет HTML-код таблицы.
	 *	@return HTML-код таблицы.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">Сайт</a>)
	 *	@date Созданно: 12.03.2011
	 *	@date Обновленно: */
	function drawTable($idDraw = false)
	{
		$this->createJSConfig();
		
		$content  = '<table id="'.$this->id.'" border="'.$this->border.'">';
		$content .= '<caption><h4>'.$this->title.'</h4></caption>';
		$content .= '<tr>';
		$row = 0; $count = count($this->titleHeader);
		foreach($this->titleHeader as $t)
		{
			if($row < ($count-1))
				$content .= '<th>'.$t.'</th>';
			else
				$content .= '<th>'.$t.'</th><th></th>';

			$row++;
		}
		$content .= '</tr><tr>';

		foreach($this->titleContent as $rec)
		{
			$row = 0; $count = count($rec['title'])+1;
			foreach($rec['title'] as $t)
			{
				if($row < ($count-1))
					$content .= '<td>'.$t.'</td>';
			}
			$content .= '<td align="right"><div class="arrow"></div></td></tr>';
			
			$content .= '<tr><td colspan="'.$count.'">'.$rec['content'].'</td></tr>';
		}
		$content .= '</table>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Настройка скрипта для работы таблица с выподающем контентом.
	 *	@author Andrew Shapovalov (<a href="http://lmwshav.org.ua">Сайт</a>)
	 *	@date Созданно: 12.03.2011
	 *	@date Обновленно: */
	private function createJSConfig()
	{
		global $shavJS;
		
		$shavJS->content .= '<style type="text/css">#'.$this->id.' { border-collapse:collapse;}
		#'.$this->id.' h4 { margin:0px; padding:0px;}
		#'.$this->id.' img { float:right;}
		#'.$this->id.' ul { margin:10px 0 10px 40px; padding:0px;}
		#'.$this->id.' th { background:#7CB8E2 url("/images/header_bkg.png") repeat-x scroll center left; color:#fff; padding:7px 15px; text-align:left;}
		#'.$this->id.' td { background:#C7DDEE none repeat-x scroll center left; color:#000; padding:7px 15px; }
		#'.$this->id.' tr.odd td { background:#fff url("/images/row_bkg.png") repeat-x scroll center left; cursor:pointer; }
		#'.$this->id.' div.arrow { background:transparent url("/images/arrows.png") no-repeat scroll 0px -16px; width:16px; height:16px; display:block;}
		#'.$this->id.' div.up { background-position:0px 0px;}</style>';
		
		$shavJS->content .= '<script type="text/javascript">
			$(document).ready(function(){
				$("#'.$this->id.' tr:odd").addClass("odd");
				$("#'.$this->id.' tr:not(.odd)").hide();
				$("#'.$this->id.' tr:first-child").show();

				$("#'.$this->id.' tr.odd").click(function(){
					$(this).next("tr").toggle();
					$(this).find(".arrow").toggleClass("up");
			});
			//$("#'.$this->id.'").jExpand();
			});
		</script>';
	}
}
?>