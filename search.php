<?php
include_once("inc/shav_config.php");

$sercher = new SHAV_Search();

if(empty($_POST))
	echo '<div width="200px">'.$sercher->drawSearchForm().'</div>';

elseif($_POST['search_btn'])
{
	$sercher->doSearch('users', array('fio'), 'users_id');
	echo $sercher->drawResults();
}
?>