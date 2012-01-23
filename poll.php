<?php
include_once('inc/shav_config.php');

if(!$_POST['poll'] || !$_POST['pollid'])
{
	$sql = "SELECT question_id, text FROM poll_question ORDER BY question_id DESC LIMIT 1";
	$poll_id = $shavDB->get_vars($sql);
	
	if($poll_id > 0)
	{
		$question = new SHAV_jPoll_Question($poll_id);
		//display question
		echo "<p class=\"pollques\" >".$question->text."</p>";
	}

	if($_GET["result"]==1 || $_COOKIE["voted".$poll_id]=='yes'){
		//if already voted or asked for result
		showresults($poll_id);
		exit;
	}
	else{
		//display options with radio buttons
		if(!empty($question->answers))
		{
			echo '<div id="formcontainer" ><form method="post" id="pollform" action="'.$_SERVER['PHP_SELF'].'" >';
			echo '<input type="hidden" name="pollid" value="'.$poll_id.'" />';
			foreach($question->answers as $answer)
			{
				echo '<p><input type="radio" name="poll" value="'.$answer->answerId.'" id="option-'.$answer->answerId.'" />
				<label for="option-'.$answer->answerId.'" >'.$answer->title.'</label></p>';
			}
			echo '<p><input type="submit"  value="Submit" /></p></form>';
			echo '<p><a href="'.$_SERVER['PHP_SELF'].'?result=1" id="viewresult">View result</a></p></div>';
		}
		else
			echo '<b class="error">Нет доступных опросов.</b>';
	}
}
else
{
	if($_COOKIE["voted".$_POST['pollid']]!='yes')
	{
		//Check if selected option value is there in database?
		$sql = "SELECT answer_id FROM poll_answers WHERE answer_id='".(int)$_POST["poll"]."'";
		$id = $shavDB->get_vars($sql);
		
		if((int)$id > 0)
		{
			$vote = new SHAV_jPoll_Vote();
			$vote->answerId = $_POST['poll'];
			$vote->userIP = $_SERVER['REMOTE_ADDR'];
			$vote->saveToDB();
			//Vote added to database
			setcookie("voted".$_POST['pollid'], 'yes', time()+86400*300);
		}
	}
	
	showresults(intval($_POST['pollid']));
}

function showresults($poll_id)
{
	global $shavDB;

	$sql = "SELECT COUNT(*) as totalvotes FROM poll_votes WHERE vote_id IN(SELECT vote_id FROM poll_answers WHERE question_id='$poll_id')";
	$total = $shavDB->get_vars($sql);//$row['totalvotes'];

	$sql="SELECT poll_answers.answer_id, poll_answers.answer_text, COUNT(*) as votes FROM poll_votes, poll_answers WHERE poll_votes.answer_id=poll_answers.answer_id AND poll_votes.answer_id IN(SELECT answer_id FROM poll_answers WHERE question_id='$poll_id') GROUP BY poll_votes.answer_id";
	$results = $shavDB->get_results($sql);

	//while($row=mysql_fetch_assoc($query))
	foreach($results as $row)
	{
		$percent=round(($row['votes']*100)/$total);
		echo '<div class="option" ><p>'.$row['answer_text'].' (<em>'.$percent.'%, '.$row['votes'].' votes</em>)</p>';
		echo '<div class="bar ';
		if($_POST['poll']==$row['answer_id']) echo ' yourvote';
		echo '" style="width: '.$percent.'%; " ></div></div>';
	}
	echo '<p>Total Votes: '.$total.'</p>';
}
?>