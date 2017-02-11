<?php

require_once('functions.php');
$score = exec("../calcscore $argv[1] $argv[2] 2>&1", $output, $return);

if ($return != 0) {
	error_log(":submit: " . var_export($output, true));
	exit;
}

insertSubmit($argv[3], $argv[4], file_get_contents($argv[2]), $score);
