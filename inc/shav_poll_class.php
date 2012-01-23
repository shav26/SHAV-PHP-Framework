<?php
/** @class SHAV_jPoll_Vote
 *	@brief Класс проголосовавшего. Хранит информацию о том, кто проголосовал за тот или иной ответ на вопрос.
 *	Пример использования:
 *	@code
 * $id = 123; //Идентийикатор пользователя, который проголосовал
 * $vote = new SHAV_jPoll_Vote($id);
 * $vote->drawVote(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 09.03.2011
 *	@date Обновленно: */
class SHAV_jPoll_Vote extends SHAV_Object
{
	/** Идентификатор проголосовавшего.*/
	public $voteId = 0;
	
	/** Идентификатор ответа.*/
	public $answerId = 0;
	
	/** Дата голосования.*/
	public $pubDate = 0;
	
	/** IP адрес пользователя, с которого было произведенно голосования.*/
	public $userIP = '';
	
	/** Конструктор класса.
	 *	@param $id - идентификатор проголосовавшего.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 09.03.2011
	 *	@date Обновленно: */
	function SHAV_jPoll_Vote($id = 0)
	{
		if((int)$id > 0)
			$this->createVoteByID($id);
	}
	
	/** Выводит проголосовавшего.
	 *	@param $isDraw - выводить или нет HTML-код проголосовавшего.
	 *	@return HTML-код проголосовавшего.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 09.03.2011
	 *	@date Обновленно: */
	function drawVote($isDraw = false)
	{
		$content = '';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
	
	/** Сохраняет данные в базе данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 09.03.2011
	 *	@date Обновленно: */
	function saveToDB()
	{
		global $shavDB;

		$this->pubDate = time();
		if((int)$this->voteId <= 0)
		{
			$sql = 'INSERT INTO poll_votes SET answer_id = '.(int)$this->answerId.', pub_date = '.$this->pubDate.', user_ip = "'.$this->userIP.'"';
			$this->voteId = $shavDB->insert_data($sql);
		}
		else
		{
			$sql = 'UPDATE poll_votes SET answer_id = '.(int)$this->answerId.', user_ip = "'.$this->userIP.'" WHERE vote_id = '.(int)$this->voteId;
			$shavDB->get_results($sql);
		}
	}
	
	/** Удаляет данные из базы данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 09.03.2011
	 *	@date Обновленно: */
	function deleteFromDB()
	{
		global $shavDB;
		
		$sql = 'DELETE FROM poll_votes WHERE vote_id = '.(int)$this->voteId;
		$shavDB->get_results($sql);
	}
	
	/** Создает проголосовавшего по его идентификатору в базе данных.
	 *	@param $id - идентиифкатор проголосовавшего.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 09.03.2011
	 *	@date Обновленно: */
	private function createVoteByID($id)
	{
		global $shavDB;
		
		$sql = 'SELECT * FROM poll_votes WHERE vote_id = '.(int)$id;
		$results = $shavDB->get_results($sql);
		
		foreach($results as $rec)
		{
			$this->voteId = (int)$rec['vote_id'];
			$this->answerId = (int)$rec['answer_id'];
			$this->pubDate = (int)$rec['pub_date'];
			$this->userIP = $rec['user_ip'];
		}
	}
}

/** @class SHAV_jPoll_Answer.
 *	@brief Класс описывает ответ на вопрос в опросах.
 *	Пример использования:
 *	@code
 * $id = 1235478;	//идентификатор ответа;
 * $answer = new SHAV_jPoll_Answer();
 * $answer->drawAnswer(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 26.02.2011
 *	@date Обновленно: */
class SHAV_jPoll_Answer extends SHAV_Object
{
	/** Идентификатор вопроса в базе данных.*/
	public $answerId = 0;
	
	/** Идентификатор вопроса, к которому привязан ответ.*/
	public $questionId = 0;
	
	/** Текст ответа на вопрос.*/
	public $title = '';
	
	/** Массив проголосовавших пользователей.*/
	public $votes = array();

	/** Конструктор класса.
	 *	@param $id - Идентификатор ответа.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function SHAV_jPoll_Answer($id = 0)
	{
		if((int)$id > 0)
			$this->createAnswerById($id);
	}

	/** Выводит ответ.
	 *	@param $isDraw - выводить или нет HTML-код ответа.
	 *	@return HTML-код ответа на вопрос.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function drawAnswer($isDraw = false)
	{
		$content = '';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Сохраняет данные в базе данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function saveToDB()
	{
		global $shavDB;
		
		if((int)$this->answerId <= 0)
		{
			$sql = 'INSERT INTO poll_answers SET question_id = '.(int)$this->questionId.', answer_text = "'.htmlspecialchars($this->title).'"';
			$this->answerId = $shavDB->insert_data($sql);
		}
		else
		{
			$sql = 'UPDATE poll_answers SET question_id = '.(int)$this->questionId.', answer_text = "'.htmlspecialchars($this->title).'" WHERE answer_id = '.(int)$this->answerId;
			$shavDB->get_results($sql);
		}

		foreach($this->votes as $vote)
		{
			if(is_a($vote, 'SHAV_jPoll_Vote'))
				$vote->saveToDB();
		}
	}

	/** Удаляет данные из базы данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function deleteFromDB()
	{
		global $shavDB;
		
		$sql = 'DELETE FROM poll_answers WHERE answer_id = '.(int)$this->answerId;
		$shavDB->get_results($sql);

		foreach($this->votes as $vote)
		{
			if(is_a($vote, 'SHAV_jPoll_Vote'))
				$vote->deleteFromDB();
		}
	}
	
	/** Создает ответ по его идентификатору.
	 *	@param $id - идентификатор ответа.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	private function createAnswerById($id)
	{
		global $shavDB;
		
		$sql = 'SELECT * FROM poll_answers WHERE answer_id = '.(int)$id;
		$results = $shavDB->get_results($sql);
		
		foreach($results as $rec)
		{
			$this->answerId = (int)$rec['answer_id'];
			$this->questionId = (int)$rec['question_id'];
			$this->title = htmlspecialchars_decode($rec['answer_text']);
		}

		$this->votes = array();
		$sql = 'SELECT vote_id FROM poll_votes WHERE answer_id = '.(int)$this->answerId;
		$results = $shavDB->get_results($sql);
		foreach($results as $rec)
		{
			$vote = new SHAV_jPoll_Vote((int)$rec['vote_id']);
			$this->votes[] = $vote;
		}
	}
}

/** @class SHAV_jPoll_Question
 *	@brief Класс вопросов для опроса. Хранит текст вопроса и список ответов на них, а так же список тех кто отвечал на вопросы.
 *	Пример использования:
 *	@code
 * $id = 1235478;	//идентификатор вопроса;
 * $answer = new SHAV_jPoll_Question();
 * $answer->drawQuestion(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 26.02.2011
 *	@date Обновленно: */
class SHAV_jPoll_Question extends SHAV_Object
{
	/** Идентификатор вопроса.*/
	public $questionId = 0;

	/** Текст вопроса.*/
	public $text = '';

	/** Дата публикации вопроса.*/
	public $pubDate = 0;

	/** Массив ответов на вопрос.*/
	public $answers = array();

	/** Коструктор класса.
	 *	@param $id - идентификатор вопроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function SHAV_jPoll_Question($id = 0)
	{
		if((int)$id > 0)
			$this->createQuestionById($id);
	}

	/** Выводит вопрос.
	 *	@param $isDraw - выводить или нет HTML-код вопроса.
	 *	@return HTML-код вопроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function drawQuestion($isDraw = false)
	{
		$content  = '';
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
	
	/** Сохраняет данные в базе данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function saveToDB()
	{
		global $shavDB;

		if((int)$this->questionId <= 0)
		{
			$sql = 'INSERT INTO poll_question SET text = "'.htmlspecialchars($this->text).'", pub_date = '.time();
			$this->questionId = $shavDB->insert_data($sql);
		}
		else
		{
			$sql = 'UPDATE poll_question SET text = "'.htmlspecialchars($this->text).'" WHERE question_id = '.(int)$this->questionId;
			$shavDB->get_results($sql);
		}

		//Добавляем все ответы на вопрос.
		foreach($this->answers as $answer)
		{
			if(is_a($answer, 'SHAV_jPoll_Answer'))
				$answer->saveToDB();
		}
	}

	/** Удаляет данные из базы данных.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	function deleteFromDB()
	{
		global $shavDB;
		
		$sql = 'DELETE FROM poll_question WHERE question_id = '.(int)$this->questionId;
		$shavDB->get_results($sql);

		foreach($this->answers as $answer)
		{
			if(is_a($answer, 'SHAV_jPoll_Answer'))
				$answer->deleteFromDB();
		}
	}
	
	/** Создает вопрос по его идентификатору.
	 *	@param $id - идентификатор вопроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	private function createQuestionById($id)
	{
		global $shavDB;
		
		$sql = 'SELECT * FROM poll_question WHERE question_id = '.(int)$id;
		$results = $shavDB->get_results($sql);
		
		foreach($results as $rec)
		{
			$this->questionId = (int)$rec['question_id'];
			$this->text = htmlspecialchars_decode($rec['text']);
			$this->pubDate = (int)$rec['pub_date'];
		}
		
		$sql = 'SELECT answer_id FROM poll_answers WHERE question_id = '.(int)$id;
		$results = $shavDB->get_results($sql);
		$this->answers = array();
		foreach($results as $rec)
		{
			$answer = new SHAV_jPoll_Answer((int)$rec['answer_id']);
			$this->answers[] = $answer;
		}
	}

	/** Расчитывает общее количество проголосовавших за данный вопрос по всем пунктам.
	 *	@return int общее количество проголосовавших.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	private function getTotalVotesForQuestion()
	{
		$total = 0;
		foreach($this->answers as $answer)
		{
			if(is_a($answer, 'SHAV_jPoll_Answer'))
				$total += count($answer->votes);
		}

		return $total;
	}

	/** Производит поиск ответа по его идентификатору.
	 *	@param $id - идентиифкатор ответа.
	 *	@return SHAV_jPoll_Answer ответ с идентификатором $id.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 26.02.2011
	 *	@date Обновленно: */
	private function getAnswerById($id)
	{
		$res = NULL;

		foreach($this->answers as $answer)
		{
			if(is_a($answer, 'SHAV_jPoll_Answer'))
			{
				if($answer->answerId == $id)
				{
					$res = $answer;
					break;
				}
			}
		}

		return $res;
	}
}

/** @class SHAV_jPoll
 *	@brief Класс работы с опросами.
 *	Пример использования:
 *	@code
 * $poll = new SHAV_jPoll();
 * $poll->getContentPoll();	//Выводим текущий опрос
 * $poll->drawEditorInterface(true);	//Выводим интерфейс редактора вопросов (работает только для пользователей с администраторскими правами)
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 10.03.2011
 *	@date Обновленно: */
class SHAV_jPoll extends SHAV_Object
{
	/** Идентификатор вопроса.*/
	public $idQuestion = 0;

	/** Массив всех вопросов, которые доступны в базе данных.*/
	public $questions = array();

	/** Конструктор класса.*/
	function SHAV_jPoll()
	{
		if(empty($this->questions))
			$this->getAllQuestions();
	}

	/** Выводит редактор опросов.
	 *	@param $isDraw - выводить или нет HTML-код редактора.
	 *	@return HTML-код редактора.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	function drawEditorInterface($idDraw = false)
	{
		if($_POST['add'] && $_POST['qText'] != '')
		{
			$q = new SHAV_jPoll_Question();
			$q->text = $_POST['qText'];
			$q->saveToDB();

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php');
		}
		elseif($_POST['save'] && (int)$_POST['questionId'] > 0 && $_POST['qText'] != '')
		{
			$q = new SHAV_jPoll_Question((int)$_POST['questionId']);
			$q->text = $_POST['qText'];
			$q->saveToDB();

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php');
		}
		elseif($_POST['addAnswer'] && (int)$_POST['qId'] > 0 && $_POST['answer'] != '')
		{
			$answer = new SHAV_jPoll_Answer();
			$answer->questionId = (int)$_POST['qId'];
			$answer->title = $_POST['answer'];
			$answer->saveToDB();
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php?action=view_answers&qId='.$answer->questionId);
		}
		elseif($_POST['saveAnswer'] && (int)$_POST['answerId'] > 0 && $_POST['answer'] != '')
		{
			$answer = new SHAV_jPoll_Answer((int)$_POST['answerId']);
			$answer->title = $_POST['answer'];
			$answer->saveToDB();
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php?action=view_answers&qId='.$answer->questionId);
		}

		if($_GET['action'] == 'del' && (int)$_GET['qId'] > 0)
		{
			$q = new SHAV_jPoll_Question((int)$_GET['qId']);
			$q->deleteFromDB();

			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php');
		}
		elseif($_GET['action'] == 'delAnswer' && (int)$_GET['aId'] > 0)
		{
			$a = new SHAV_jPoll_Answer((int)$_GET['aId']);
			$a->deleteFromDB();
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/poll_editor.php?action=view_answers&qId='.$answer->questionId);
		}

		if($_GET['action'] == 'view_answers' && (int)$_GET['qId'] > 0)
		{
			$q = new SHAV_jPoll_Question((int)$_GET['qId']);
			$content  = '<div class="poll_editor">';
			$content .= '<table>';
			$content .= '<tr><td><b>Вопрос: </b><i>'.$q->text.'</i></td><td width="100px">'.$this->createAnswerEditor($q->questionId, 0).'</td></tr><tr><td colspan="2"><table style="margin:0;">';
			foreach($q->answers as $answer)
			{
				$content .= '<tr><td>'.$answer->title.'</td><td width="150px">'.$this->createAnswerEditor($q->questionId, $answer->answerId).'&nbsp;|&nbsp;<a href="?action=delAnswer&aId='.$answer->answerId.'">Удалить</a></td></tr>';
			}
			$content .= '</table></td></tr></table>';
			$content .= '</div>';
		}
		else
		{
			if(empty($this->questions))
			{
				$content = '<div class="poll_editor"><div style="width:50px;float:right;">'.$this->createQuestionEditor(0).'</div><p>В данный момент нет вопросов в базе.</p></div>';
			}
			else
			{
				$content  = '<div class="poll_editor"><div style="width:50px;float:right;">'.$this->createQuestionEditor(0).'</div><table><tr><th>Вопрос</th><th width="100px"></th></tr>';
				foreach($this->questions as $question)
				{
					if(is_a($question, 'SHAV_jPoll_Question'))
						$content .= '<tr><td>'.$question->text.'</td><td>'.$this->createQuestionEditor($question->questionId).'&nbsp;|&nbsp;<a href="?action=del&qId='.$question->questionId.'">Удалить</a>&nbsp;|&nbsp;<a href="?action=view_answers&qId='.$question->questionId.'">Ответы</a></td></tr>';
				}
				$content .= '</table>';
				$content .= '</div>';
			}
		}

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Выводит контейрен для вывода опроса.
	 *	@return HTML-код контейнера.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	function getContentPoll()
	{
		$this->getJavaScriptPoll();
		
		$content  = '<div id="poll_container">';
		$content .= '	<h1>User Poll</h1><div id="poll_pollcontainer"></div><p id="loader" >Loading...</p>';
		$content .= '</div>';

		return $content;
	}

	/** Выводит javascript для настроек опроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function getJavaScriptPoll()
	{
		global $shavJS;

		$shavJS->content .= '<script type="text/javascript" >
		$(function(){
			var loader=$(\'#loader\');
			var pollcontainer=$(\'#poll_pollcontainer\');
			loader.fadeIn();
			//Load the poll form
			$.get(\'poll.php\', \'\', function(data, status){
				pollcontainer.html(data);
				animateResults(pollcontainer);
				pollcontainer.find(\'#viewresult\').click(function(){
					//if user wants to see result
					loader.fadeIn();
					$.get(\'poll.php\', \'result=1\', function(data,status){
						pollcontainer.fadeOut(1000, function(){
							$(this).html(data);
							animateResults(this);
			});
			loader.fadeOut();
			});
			//prevent default behavior
			return false;
			}).end()
			.find(\'#pollform\').submit(function(){
			var selected_val=$(this).find(\'input[name=poll]:checked\').val();
			if(selected_val!=\'\'){
				//post data only if a value is selected
				loader.fadeIn();
				$.post(\'poll.php\', $(this).serialize(), function(data, status){
					$(\'#formcontainer\').fadeOut(100, function(){
					$(this).html(data);
				animateResults(this);
				loader.fadeOut();
			});
			});
			}
			//prevent form default behavior
			return false;
			});
				loader.fadeOut();
			});

				function animateResults(data){
					$(data).find(\'.bar\').hide().end().fadeIn(\'slow\', function(){
					$(this).find(\'.bar\').each(function(){
					var bar_width=$(this).css(\'width\');
				$(this).css(\'width\', \'0\').animate({ width: bar_width }, 1000);
			});
			});
			}
			});
		</script>';
	}

	/** Создается окно редактирование вопроса.
	 *	@param $id - идентификатор вопроса на вопрос.
	 *	@return HTML-код ссылки на окно для редактирования.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function createQuestionEditor($id = 0)
	{
		global $shavJS;
		
		if((int)$id > 0)
		{
			$q = new SHAV_jPoll_Question($id);
			$questionId = array('name'=>'questionId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>(int)$id);
			$btn = array('name'=>'save', 'label_align'=>'right', 'label'=>$this->createAnswerEditor($id), 'type'=>'submit', 'value'=>'Сохранить');
		}
		else
		{
			$btn = array('name'=>'add', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить');
			$questionId = array('name'=>'questionId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>0);
		}
		
		$recs = array(	array('name'=>'qText', 'label_align'=>'left', 'label'=>'Вопрос:', 'type'=>'textarea', 'size'=>array('cols'=>'80', 'rows'=>'15'), 'value'=>$q->text),
						$questionId, $btn);
		
		$params = array('method'=>'POST', 'action_scrp'=>'', 'enctype'=>'', 'style_class'=>'addQuestion', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		
		if((int)$id > 0)
		{
			$modal = new SHAV_jModalWindow('window_q_'.$id, 'Редактирование вопрос');
			$modal->content = $form->drawForm();
			$shavJS->content .= $modal->createJSConfig();
			
			return $modal->drawLink('Изменить');
		}
		else
		{
			$modal = new SHAV_jModalWindow('window_addQuestion', 'Создать новый вопрос');
			$modal->content = $form->drawForm();
			$shavJS->content .= $modal->createJSConfig();
			
			return $modal->drawLink('Добавить');
		}

		return '';
	}

	/** Создается окно редактирование ответа на вопрос.
	 *	@param $qId - идентификатор вопроса;
	 *	@param $id - идентификатор ответа на вопрос.
	 *	@return HTML-код ссылки на окно для редактирования.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function createAnswerEditor($qId, $id = 0)
	{
		global $shavJS;
		
		if((int)$id > 0)
		{
			$a = new SHAV_jPoll_Answer($id);
			$answerId = array('name'=>'answerId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>(int)$id);
			$btn = array('name'=>'saveAnswer', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Сохранить');
		}
		else
		{
			$btn = array('name'=>'addAnswer', 'label_align'=>'right', 'label'=>'', 'type'=>'submit', 'value'=>'Добавить');
			$answerId = array('name'=>'answerId', 'label_align'=>'right', 'label'=>'', 'type'=>'hidden', 'value'=>0);
		}
		
		$recs = array(	array('name'=>'answer', 'label_align'=>'left', 'label'=>'Ответ на вопрос:', 'type'=>'textarea', 'size'=>array('cols'=>'80', 'rows'=>'15'), 'value'=>$a->title),
				array('name'=>'qId', 'labe_align'=>'left', 'label'=>'', 'type'=>'hidden', 'value'=>$qId),
				$answerId, $btn);
		
		$params = array('method'=>'POST', 'action_scrp'=>'', 'enctype'=>'', 'style_class'=>'addAnswer', 'title'=>'', 'content_frm'=>$recs);
		$form = new SHAV_Form();
		$form->createFromArray($params);
		
		if((int)$id > 0)
		{
			$modal = new SHAV_jModalWindow('window_a'.(int)$id, 'Изменить ответ');
			$modal->content = $form->drawForm();
			$shavJS->content .= $modal->createJSConfig();
			return $modal->drawLinkWithContent('Изменить ответ');
		}
		else
		{
			$modal = new SHAV_jModalWindow('window_a'.(int)$id, 'Добавление ответ');
			$modal->content = $form->drawForm();
			$shavJS->content .= $modal->createJSConfig();
			return $modal->drawLinkWithContent('Добавить ответ');
		}
	}

	/** Выводит опрос.
	 *	@param $id - идентификатор вопроса;
	 *	@param $isDraw - выводить или нет HTML-код редактора.
	 *	@return HTML-код результатов опроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function drawPoll($id, $isDraw = false)
	{
		if((int)$id > 0)
		{
			$q = $this->getQuestionById($id);
			$content = $q->drawQuestion();
		}
		else
		{
			$q = $this->questions[0];
			$content = $q->drawQuestion();
		}
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Выводим результат опроса.
	 *	@param $id - идентификатор вопроса;
	 *	@param $isDraw - выводить или нет HTML-код редактора.
	 *	@return HTML-код результатов опроса.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function drawPollResults($id, $idDraw = false)
	{
		$q = $this->getQuestionById($id);
		$content = $q->drawResults();
		
		if($isDraw == true)
			echo $content;
		else
			return $content;
	}

	/** Получаем список всех вопросов которые существуют в базе.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function getAllQuestions()
	{
		global $shavDB;

		$sql = 'SELECT question_id FROM poll_question';
		$results = $shavDB->get_results($sql);

		$this->questions = array();
		foreach($results as $rec)
		{
			$question = new SHAV_jPoll_Question((int)$rec['question_id']);
			$this->questions[] = $question;
		}
	}

	/** Получаем вопрос по его идентификатору из массива всех доступных вопросов.
	 *	@param $id - идентификатор вопроса.
	 *	@return SHAV_jPoll_Question Вопрос, если он найден. Иначе - NULL.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 10.03.2011
	 *	@date Обновленно: */
	private function getQuestionById($id)
	{
		$res = NULL;
		
		foreach($this->questions as $question)
		{
			if(is_a($question, 'SHAV_jPoll_Question'))
			{
				if($question->questionId == $id)
				{
					$res = $question;
					break;
				}
			}
		}

		return $res;
	}
}
?>