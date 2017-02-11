<?php
require_once('../functions.php');

session_start();


$msgs = [];
$errors = [];

if (isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {
	try {
		$msgs []= login($_POST);
	} catch (Exception $e) {
		$errors []= $e->getMessage();
	}
}
else if (isset($_POST['register']) && isset($_POST['username']) && isset($_POST['password'])) {
	try {
		$msgs []= register($_POST);
	} catch (Exception $e) {
		$errors []= $e->getMessage();
	}
}

if (isset($_POST['submit']) &&
	isset($_POST['problem_id']) &&
	0 <= $_POST['problem_id'] && $_POST['problem_id'] < count(getProblems()) &&
	isset($_POST['spell']) &&
	isset($_SESSION['id']) &&
	checktime()) {

	try {
		submit($_POST, $_SESSION['id']);
		$msgs []= 'submitted';
	} catch (Exception $e) {
		$errors []= $e->getMessage();
	}
}

if (isset($_POST['user']) &&
	isset($_POST['handicap']) &&
	100 <= $_POST['handicap'] && $_POST['handicap'] <= 110 &&
	checktime(strtotime("+1 day"))) {

	try {
		updateHandicap($_POST['handicap'], $_SESSION['id']);
		$msgs []= 'updated';
	} catch (Exception $e) {
		$errors []= $e->getMessage();
	}
}

$content = '../main.php';
if (isset($_SESSION['id'])) {
	if (isset($_GET['challenge']) && checktime()) {
		$content = '../challenge.php';
	}
	else if (isset($_GET['submit']) && checktime()) {
		$content = '../submit.php';
	}
	else if (isset($_GET['scoreboard'])) {
		$content = '../scoreboard.php';
	}
	else if (isset($_GET['user'])) {
		$content = '../user.php';
	}
	else if (isset($_GET['logout'])) {
		unset($_SESSION['username']);
		unset($_SESSION['id']);
	}
}
else {
	if (isset($_GET['login'])) {
		$content = '../login.php';
	}
}

?>

<!doctype html>
<html>
<head>
	<style>
		.scores {
			display: none;
			width: 100%;
		}
		.scores tr:first-child,.scoreboard tr:first-child {
			background-color: #faffac;
		}
		.score-selector:hover {
			cursor: pointer;
		}
		#container {
			max-width: 1024px;
			margin: 0 auto;
		}
		h1 {
			border-bottom: 1px double #ccc;
			font-family: "Courier New",Courier,Consolas,Inconsolata,monospace;
		}	
		nav>ul {
			list-style: none;
			padding: 0;

			display: flex;
			flex-direction: row;
			justify-content: space-between;
		}
		nav>ul>li {
			flex: auto;
			background-color: #2d5ee9;
			border-left: 2px #000 solid;
			padding: 4px;
		}
		nav>ul>li:first-child {
			border-left: none;
		}
		nav>ul>li:hover {
			background-color: #3c3e9e;
		}
		nav>ul>li>a {
			color: #efefef;
			font-weight: bold;
			text-shadow:  0 0 0 #99e9ec;
			display: block;
			font-size: larger;
			text-align: center;
		}
		pre {
			border: 1px solid #ccc;
			background-color: #eee;
			border-radius: 4px;
			padding: 1em;
		}
		i {
			text-decoration: none;
			border: 1px solid #ccc;
			background-color: #eee;
			padding: 0 4px;
		}
		table {
			width: 100%;
			border-radius: 5px;
			border: 1px solid #ccc;
		}
		tr {
			width: 100%;
		}
		tr:nth-child(even) {
			background-color: #ccc;
			margin: 0;
		}
		th,td {
			text-align: left;
			min-width: 100px;
		}
		.spell {
			width: 100%;
		}
	</style>
</head>
<body>
<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<div id="container">
	<header>
		<h1><a href="./">Joken Marathon Match #1</a></h1>
		<nav>
			<ul>
				<li><a href="?challenge">Challenges</a></li>
				<li><a href="?submit">Submit</a></li>
				<li><a href="?scoreboard">Scoreboard</a></li>
				<?php if (isset($_SESSION['id'])) { ?>
				<li><a href="?user"><?php o($_SESSION['username'] . "/rank:" . getRank($_SESSION['id'])); ?></a></li>
				<?php } else { ?>
				<li><a href="?login">Login/Register</a></li>
				<?php } ?>
			</ul>
		</nav>
	</header>
	<main>
		<ul class="messages">
		<?php foreach ($msgs as $m) { ?>
			<li><?php o($m); ?></li>
		<?php } ?>
		</ul>
		<ul class="errors">
		<?php foreach ($errors as $e) { ?>
			<li><?php o($e); ?></li>
		<?php } ?>
		</ul>
		<?php include($content); ?>
	</main>
</div>
</body>
</html>
