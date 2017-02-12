<?php

require_once('settings.php');

function getStartTime() {
	return strtotime(START_DATE);
}
function getEndTime() {
//	return strtotime('2017-02-11 12:00');
	return strtotime(END_DATE);
}

function getProblems() {
	static $problems = [];
	if (count($problems) == 0) {
	       	foreach(PROBLEMS as $p) {
			$ary = [
				'name' => $p,
				'file' => 'problems/'.$p.'.txt',
			];
			$problems []= $ary;
		}
	}
	return $problems;
}

function csrfcheck($csrf) {
	return password_verify(session_id(), $csrf);
}

function csrf() {
	return password_hash(session_id(), PASSWORD_DEFAULT);
}

function checktime($t = null) {
	if (is_null($t)) {
		$t = time();
	}
	return getStartTime() <= $t && $t < getEndTime();
}

function o($s) {
	echo htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
function db() {
	$pdo = new PDO('sqlite:../database.db');
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $pdo;
}

// login
function login($params) {
	$pdo = db();
	$stmt = $pdo->prepare('select id, password from users where username = :username');
	$stmt->bindParam(':username', $params['username'], PDO::PARAM_STR);
	if (!$stmt->execute()) {
		throw new Exception("Failed to login.");
	}
	$r = $stmt->fetchAll()[0];
	if (password_verify($params['password'], $r['password'])) {
		$_SESSION['username'] = $params['username'];
		$_SESSION['id'] = $r['id'];
		return 'Logged in. Welcome ' . $params['username'];
	}
	throw new Exception('Failed to login.');
}

// register
function register($params) {
	$password = password_hash($params['password'], PASSWORD_DEFAULT);

	$pdo = db();
	$stmt = $pdo->prepare('insert into users(username, password, handicap) values (:username, :password, 1.0)');
	$stmt->bindValue(':username', $params['username'], PDO::PARAM_STR);
	$stmt->bindValue(':password', $password, PDO::PARAM_STR);
	if (!$stmt->execute()) {
		throw new Exception('Failed to register new user');
	}
	return 'Added new user. please log in';
}

function getUserSubmits($id) {
	$pdo = db();
	$stmt = $pdo->prepare('select * from submits where user_id = :user_id order by created_at desc');
	if (!$stmt) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(9). please report to admin");
	}
	$stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
	if (!$stmt->execute()) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(9). please report to admin");
	}
	return $stmt->fetchAll();
}
// getUserInfo
function getUserInfo($id) {
	$pdo = db();
	$stmt = $pdo->prepare('select * from users where id = :id');
	if (!$stmt) {
		throw new Exception("unknown error(4). please report to admin");
	}
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	if (!$stmt->execute()) {
		throw new Exception("unknown error(4). please report to admin");
	}
	return $stmt->fetchAll()[0];
}

// getRankingAbout
// get ranking about problem[$problem_id]
function getRankingAbout($problem_id) {
	$pdo = db();
	$query = 'select user_id, score*handicap as score, handicap, submits.score as rawscore, max(created_at) from submits join users on users.id = user_id where problem_id = :problem_id and submits.score is not null group by user_id order by score';
	$stmt = $pdo->prepare($query);
	if (!$stmt) {
		throw new Exception("unknown error(3). please report to admin");
	}
	$stmt->bindValue(':problem_id', $problem_id, PDO::PARAM_INT);
	if (!$stmt->execute()) {
		throw new Exception("unknown error(3). please report to admin");
	}

	return $stmt->fetchAll();
}

// getRank
function getRank($id) {
	$scores = getAllUserScore(true);
	$scores = array_flip($scores); // key: score, value: id
	ksort($scores); // sort by key
	$scores = array_values($scores);

	for ($i = 0; $i < count($scores); $i++) {
		if ($id-1 == $scores[$i]) {
			return count($scores)-$i;
		}
	}
	return 0;
}

// getScoreboard
function getScoreboard() {
	$scores = getAllUserScore();
	$scoreboard = [];
	foreach ($scores as $id => $score) {
		$scoreboard []= [
			'score' => $score,
			'userinfo' => getUserInfo($id+1),
		];
	}
	usort($scoreboard, function($a, $b) { 
		if ($a['score'] == $b['score']) {
			return 0;
		}
		return ($a['score'] > $b['score']) ? -1 : 1;
       	});
	return $scoreboard;
}

function getAllUserCount() {
	$pdo = db();
	$stmt = $pdo->query('select count() from users');
	if (!$stmt) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(7). please report to admin");
	}
	return $stmt->fetchAll()[0]['count()'];
}

// getAllUserScore
// get score of users indexed by id
function getAllUserScore() {
	$pdo = db();
	// get user scores/problems
	$query = 'select submits.id, user_id, problem_id, submits.score as rawscore, handicap, score*handicap as score, max(created_at) from submits join users on user_id = users.id where submits.score is not null group by user_id, problem_id order by problem_id, score';
	$stmt = $pdo->query($query);
	if (!$stmt) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(1). please report to admin");
	}
	$r = $stmt->fetchAll();
	if (count($r) == 0) {
		return [];
	}


	// scores
	$users = getAllUserCount();
	$scores = array_fill(0, $users, 0);
	$pid = 0; // problem id
	$idx = 0; 
	// parse $r
	foreach ($r as $v) {
		if ($pid != $v['problem_id']) {
			$pid = $v['problem_id'];
			$idx = 0;
		}

		$id = $v['user_id']-1;
		$scores[$id] += $users - $idx;
		if ($idx == 0) { // bonus
			$scores[$id] += 5; 
		}
		$idx++;
	}

	return $scores;
}

function submit($params, $user_id) {
	$problem_id = $params['problem_id'];

	$id = uniqid();
	$fname = sprintf('../submits/%s_%d_%d.txt', $id, $problem_id, $user_id);
	$pfile = getProblems()[$problem_id]['file'];
	file_put_contents($fname, $params['spell']); 
	exec(sprintf("nohup php ../submitscript.php %s %s %d %d  1>&2 &", $pfile, $fname, $problem_id, $user_id));
}

function insertSubmit($problem_id, $user_id, $spell, $score) {
	$pdo = db();
	$stmt = $pdo->prepare('insert into submits(problem_id, user_id, score, created_at, input) values (:problem_id, :user_id, :score, :created_at, :input)');

	if (!$stmt) {
		throw new Exception("unknown error(6). please report to admin");
	}
	$stmt->bindValue(':problem_id', $problem_id, PDO::PARAM_INT);
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindValue(':created_at', microtime(true), PDO::PARAM_STR); //!!
	$stmt->bindValue(':input', $spell, PDO::PARAM_STR);
	$stmt->bindValue(':score', $score, PDO::PARAM_STR);

	if (!$stmt->execute()) {
		throw new Exception("unknown error(6). please report to admin");
	}
}

function updateHandicap($handicap, $id) {
	$pdo = db();
	$stmt = $pdo->prepare('update users set handicap = :handicap where id = :id');
	if (!$stmt) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(7). please report to admin");
	}
	$stmt->bindValue(':handicap', $handicap/100.0, PDO::PARAM_STR);
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);

	if (!$stmt->execute()) {
		error_log($pdo->errorInfo()[2]);
		throw new Exception("unknown error(7). please report to admin");
	}
}
