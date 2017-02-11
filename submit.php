<div>
<h2>Submit</h2>
<form action="#" method="post">
<dl>
	<dt>Probelm</dt>
	<dd>
		<select name="problem_id">
			<?php foreach (getProblems() as $i => $p) { ?>
			<option value="<?php o($i); ?>"><?php o($p['name']); ?></option>
			<?php } ?>
		</select>
	</dd>
	<dt>Magic Spell</dt>
	<dd><textarea name="spell" class="spell"></textarea></dd>
	<dt><input type="submit" name="submit" value="cast a spell"></dt>
</dl>
</form>
</div>
