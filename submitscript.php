<?php

require_once('functions.php');
$score = exec("../calcscore $argv[1] $argv[2] 2>&1", $output, $return);

if ($return != 0) {
	error_log(":submit: " . var_export($output, true));
	exit;
}

$pdo = db();
$stmt = $pdo->prepare('update submits set score=:score where id=:id');
if (!$stmt) {
	error_log($pdo->errorInfo()[2]);
	exit;
}
$stmt->bindValue(':score', $output[0], PDO::PARAM_STR);
$stmt->bindValue(':id', $argv[3], PDO::PARAM_INT);
if (!$stmt->execute()) {
	error_log($pdo->errorInfo()[2]);
	exit;
}

