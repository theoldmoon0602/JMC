<div>
<h2>Submit</h2>
<form action="#" method="post">
<dl>
	<dt>改良する魔方陣</dt>
	<dd>
		<select name="problem_id">
			<?php foreach (getProblems() as $i => $p) { ?>
			<option value="<?php o($i); ?>"><?php o($p['name']); ?></option>
			<?php } ?>
		</select>
	</dd>
	<dt>改良の魔法</dt>
	<dd><textarea name="spell" class="spell"></textarea></dd>
	<dt><input type="submit" name="submit" value="提出する"></dt>
</dl>
</form>
</div>
