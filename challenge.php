<?php

$pnum = 0;
?>
<div>
<h2>Challenges</h2>

<table>
<?php $problems = getProblems(); ?>
<?php for ($i = 0; $i < count($problems); $i++) { ?>
	<?php 
		$u = getRankingAbout($i, true); 
		$user = '';
		if (count($u)>0) { 
			$u = $u[0];
			$user = getUserInfo($u['user_id']); 
		}
	?>
	<tr>
		<th><a href="<?php o($problems[$i]['file']); ?>" download><?php o($problems[$i]['name']); ?></a></th>
		<?php if (count($u)>0) {  ?>
		<td><?php o($user['username']); ?></td>
		<td>score: <?php o($u['score']); ?></td>
		<?php } ?>
	</tr>
<?php } ?>
</table>
</div>
