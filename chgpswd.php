<?php

if (count($argv) != 3) {
	fprintf(STDERR, "%s username password\n",  $argv[0]);
	exit;
}

$pdo = new PDO('sqlite:database.db');
$stmt = $pdo->prepare('update users set password=:password where username=:username');
if (!$stmt) {
	fprintf(STDERR, "%s\n", $pdo->errorInfo()[2]);
	exit;
}
$stmt->bindValue(':username', $argv[1]);
$stmt->bindValue(':password', password_hash($argv[2], PASSWORD_DEFAULT));

echo $stmt->queryString . ' is ok? (y/n)';
fscanf(STDIN, "%c", $y);
if ($y == 'y') {
	$stmt->execute();
	echo 'DONE\n';
}
