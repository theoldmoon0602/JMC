<div>
<h2><?php o($_SESSION['username']); ?></h2>

<form action='#' method="post">
<dl>
	<dt>Handicap weight:</dt>
	<dd><input type="number" max="110" min="100" value="<?php o(intval(getUserInfo($_SESSION['id'])['handicap']*100)); ?>" name="handicap" required>%</dd>
	<dt><input type="submit" name="user" value="update"></dt>
</dl>
</form>

<a href="?logout">Logout</a>

<h2>Submits</h2>
<?php $submits = getUserSubmits($_SESSION['id']); ?>
<table>
	<tr><th>No.</th><th>Problem</th><th>Score</th><th>Submitted at</th></tr>
	<?php foreach ($submits as $i => $s) { ?>
	<tr>
		<th><?php o($i+1); ?></th>
		<td><?php o(getProblems()[$s['problem_id']]['name']); ?></td>
		<td><?php o($s['score']); ?></td>
		<td><?php o(date('Y-m-d H:i:s', $s['created_at'])); ?></td>
	</tr>
	<?php } ?>
</table>
</div>
