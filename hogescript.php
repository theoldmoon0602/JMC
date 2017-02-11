<?php

function db() {
	$pdo = new PDO('sqlite:database.db');
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $pdo;
}

$pdo = db();

	$stmt = $pdo->prepare('insert into submits(problem_id, user_id, score, created_at, input) values (?, ?, ?, ?, ?)');
	$stmt->execute([rand()%16, rand()%3+1, rand()/1000.0, microtime(true), '']);

