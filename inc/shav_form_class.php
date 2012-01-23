<?php

/** @class SHAV_FormElement
 *	@brief Класс элемента формы.
 *	Пример использования:
 *	@code
 * $element = new SHAV_FormElement();
 * $element->id = $array['name'];
 * $element->value = $array['value'];
 * $element->labelAlign = $array['label_align'];
 * $element->labelTitle = $array['label'];
 * $element->disable = $array['disable'];
 * $element->size = $array['size'];
 * $element->type = $array['type'];
 * $element->drawComponent();
 *	@endcode
 *	Или ипользовать массив параметров вида:
 *	@code
 * $rec = array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>'текс');
 * $elem = new SHAV_FormElement();
 * $elem->createFromArray($rec);
 * $elem->drawComponent();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 17.04.2010
 *	@date Обновленно: 27.02.2011*/
class SHAV_FormElement extends SHAV_Object
{
	/** Идентификатор компонена для формы (id и name).*/
	public $id = '';
	
	/** Значение компонента.*/
	public $value = '';
	
	/** Выравнивание текста.*/
	public $labelAlign = 'left';//
	
	/** Заголовок компонента.*/
	public $labelTitle = '';
	
	/** Размер компонента, для textarea это массим вида array('cols'=>'12', 'rows'=>10).*/
	public $size = '';
	
	/** Тип компонента (html-формы и list-для выподающего списка, textarea-для текстового поля, date-для датты в виде jQuery, uploads-для загрузки файлов в виде jQuery)*/
	public $type = '';
	
	/** Выключенно или нет.*/
	public $disable = 'false';
	
	/** HTML-код компонента.*/
	public $content = '';
	
	/** Состояние компонента (disable или status='' - enable).*/
	public $status = '';

	/** Конструктор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 27.02.2011*/
	function SHAV_FormElement()
	{}

	/** Создания комопнента из массива.
	 *	@param array - массив параметров вида: @code$array = array('name'=>'author', 'label_align'=>'left', 'label'=>'', 'type'=>'hidden', 'size'=>'25', 'value'=>$comment->authorId);@endcode
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 27.02.2011*/
	function createFromArray($array)
	{
		if(!is_array($array))
		{
			echo '<b class="">ОШИБКА! Параметр не является массивом. Проверте правильность параметра.</b>';
			return;
		}

		$this->id = $array['name'];
		$this->value = $array['value'];
		$this->labelAlign = $array['label_align'];
		$this->labelTitle = $array['label'];
		$this->disable = $array['disable'];
		$this->size = $array['size'];
		$this->type = $array['type'];
	}

	/** Вывод html-кода компонента
	 *	@param $isDraw - Выводить или нет html-код компонента.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 27.02.2011*/
	function drawComponent($isDraw = false)
	{
		$this->content = '';

		$disabled = '';
		if($rec['disable'] == 'true')
			$disabled = 'disabled';
		
		if($this->type != 'submit' && $this->type != 'list' && $this->type != 'checkbox' && $this->type != 'date' && $this->type != 'textarea' && $this->type != 'uploads')
		{
			$this->content .= '<td align="'.$this->labelAlign.'" valign="top"><label for="'.$this->id.'">'.$this->labelTitle.'</label></td>';
			$this->content .= '<td><input type="'.$this->type.'" '.$this->status.' name="'.$this->id.'" id="'.$this->id.'" size="'.$this->size.'" value="'.$this->value.'" '.$disabled.' /></td>';
		}
		elseif($this->type == 'list')
		{
			$this->content .= '<td align="'.$this->labelAlign.'" valign="top"><label for="'.$this->id.'">'.$this->labelTitle.'</label></td>';
			$this->content .= '<td>'.$this->value.'</td>';
		}
		elseif($this->type == 'submit')
		{
			$reg_url = '';
			if($this->labelTitle != '')
				$reg_url = $this->labelTitle;
			
			$this->content  .= '<td colspan="2" align="'.$this->labelAlign.'" valign="top">'.$reg_url.'&nbsp;&nbsp;<input type="'.$this->type.'" '.$disabled.' name="'.$this->id.'" id="'.$this->id.'" size="'.$this->size.'" value="'.$this->value.'" /></td>';
		}
		elseif($this->type == 'checkbox')
		{
			$checked = '';
			if($rec['value'] == 1)
				$checked = 'checked';
			
			$this->content .= '<td align="'.$this->labelAlign.'" valign="top"><label for="'.$this->id.'">'.$this->labelTitle.'</label></td>';
			$this->content .= '<td><input type="'.$this->type.'" '.$checked.$disabled.' name="'.$this->id.'" id="'.$this->id.'" size="'.$this->size.'" value="'.$this->value.'" /></td>';
		}
		elseif($this->type == 'date')
		{
			$calendarField = shav_createCalendarFieldRus($this->id, $this->size, $this->value);
			$this->content .= '<td align="'.$this->labelAlign.'" valign="top"><label for="'.$this->id.'">'.$this->labelTitle.'</label></td>';
			$this->content .= '<td>'.$calendarField.'</td>';
		}
		elseif($this->type == 'textarea')
		{
			$this->content .= '<tr><td colspan="2" align="'.$this->labelAlign.'" valign="top"><label for="'.$this->id.'">'.$this->labelTitle.'</label></td></tr>';
			$this->content .= '<tr><td colspan="2"><textarea id="'.$this->id.'" name="'.$this->id.'" cols="'.$this->size['cols'].'" rows="'.$this->size['rows'].'">'.$this->value.'</textarea></td>';
		}
		elseif($rec['type'] == 'uploads')
		{
			$this->content .= '<td colspan="2">'.$this->value.'</td>';
		}

		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
}

/** @class SHAV_Form
 *	@brief Класс формы.
 *	Пример использования:
 *	@code
 *$form = new SHAV_Form();
 *$form->id = 'Идентификатор формы';
 *$form->method = 'Метод'; //POST или GET.
 *$form->enctype = '';	//Используется для установки параметра "enctype" в HTML-коде формы.
 *$form->actionScript = 'ссылка на скрипт куда необходимо передать параметры формы';
 *$form->title = 'Заголовок формы';
 *$form->elements = array();	//Массив елементов формы.
 *	@endcode
 *	Второй способ использования такой:
 *	@code
 * //Создаем массив всех элементов.
 *$recs = array(	array('name' => 'name', 'label_align' => 'left', 'label' => 'Название:', 'type' => 'text', 'size' => '25', 'value' => ''),
 *		array('name' => 'add', 'label_align' => 'right', 'label' => '', 'type' => 'submit', 'value' => 'Добавить'));
 * 
 * //Задаем массив параметров для настройки формы
 *$params = array('method' => 'POST', 'action_scrp' => '/admin/edit_type.php', 'enctype' => '', 'style_class' => 'statusFrm', 'title' => '', 'content_frm' => $recs);
 * 
 * //Создаем форму и выводим форму
 *$form = new SHAV_Form();
 *$form->createFromArray($params);
 *$form->drawForm();
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 17.04.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_Form extends SHAV_Object
{
	/** Идентификатор формы (id и name)*/
	public $id = 'forma';
	
	/** Метод передачи параметров.*/
	public $method = 'POST';
	
	/** Тип для передачи файлов.*/
	public $enctype = '';
	
	/** Скрипт, в который будут переданны параметры формы. Если пустой, то в тот откуда вызвана форма.*/
	public $actionScript = '';
	
	/** Заголовок формы.*/
	public $title = 'HTML-форма';
	
	/** Все елементы формы.*/
	public $elements = array();
	
	/** HTML-контент формы.*/
	public $content = '';

	/** Конструктор
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_Form()
	{}

	/** Создает форму из массива параметров.
	 *	@param $params = array('method'=>'POST', 'action_scrp'=>'/admin/traker_fnc.php', 'enctype'=>'', 'style_class'=>'save_project', 'title'=>'', 'content_frm'=>$recs);
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 05.02.2011*/
	function createFromArray($array)
	{
		if(!is_array($array))
		{
			echo '<b class="">ОШИБКА! Параметр не является массивом. Проверте правильность параметра.</b>';
			return;
		}

		$this->id = $array['style_class'];
		$this->actionScript = $array['action_scrp'];
		$this->enctype = $array['enctype'];
		$this->method = $array['method'];
		$this->title = $array['title'];
		$this->elements = array();

		foreach($array['content_frm'] as $rec)
		{
			$element = new SHAV_FormElement();
			$element->createFromArray($rec);
			$this->elements[] = $element;
		}
	}

	/** Вывод формы в стиле поумолчанию на экран.
	 *	@param $isDraw - выводить или нет html-код формы.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 17.04.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawForm($isDraw = false)
	{
		$this->content  = '<form id="'.$this->id.'" name="'.$this->id.'" method="'.$this->method.'" action="'.$this->actionScript.'" enctype="'.$this->enctype.'">';
		$this->content .= '<table width="100%"><tr><td colspan="2"><h1>'.$this->title.'</h1></td></tr>';

		foreach($this->elements as $element)
		{
			if(is_object($element))
			{
				$this->content .= '<tr>'.$element->drawComponent().'</tr>';
			}
		}
		$this->content .= '</table></form>';

		if($isDraw == true)
			echo $this->content;
		else
			return $this->content;
	}
}
?>