<div>
<h3><?php o($_SESSION['username']); ?></h3>

<form action='#' method="post">
<dl>
	<dt>Handicap weight:</dt>
	<dd><input type="number" max="110" min="100" value="<?php o(intval(getUserInfo($_SESSION['id'])['handicap']*100)); ?>" name="handicap" required>%</dd>
	<dt><input type="submit" name="user" value="update"></dt>
</dl>
</form>

<a href="?logout">Logout</a>
</div>
