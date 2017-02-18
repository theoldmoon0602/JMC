<?php

$inputfile = $argv[1];
$operation = $argv[2];
$problem_id = $argv[3];
$user_id = $argv[4];


require_once('functions.php');
rquiree_once('settings.php');
$score = exec("../calcscore $inputfile $operation 2>&1", $output, $return);

if ($return != 0) {
	error_log(":submit: " . var_export($output, true));
	$score = null;
}

$rank = null;
foreach (getRankingAbout($problem_id) as $i => $r) {
	if ($r['user_id'] == $user_id) {
		$rank = $i+1;
	}
}
insertSubmit($problem_id, $user_id, file_get_contents($operation), $score);
$newrank = null;
foreach (getRankingAbout($problem_id) as $i => $r) {
	if ($r['user_id'] == $user_id) {
		$newrank = $i+1;
	}
}

if (!is_null($score)) {
	$username = getUserInfo($user_id)['username']; 
	slackSend("$username submitted to problem " PROBLEMS[$problem_id] . ". score is $score, rank: $rank => $newrank");
}
