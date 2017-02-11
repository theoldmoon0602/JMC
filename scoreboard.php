<div>
<h2>Scoreboard</h2>
<?php
	$scoreboard = getScoreboard(true);
?>
<table class="scoreboard">
	<?php foreach ($scoreboard as $i => $v) { ?>
	<tr>
	<th><?php o($i+1); ?></th>
	<td><?php o($v['userinfo']['username']); ?></td>
	<td><?php o($v['score']); ?></td>
	</tr>
	<?php } ?>
</table>
</div>
<div>
<h2>Scores</h2>
<?php $uc = getAllUserCount(); ?>
<?php foreach(getProblems() as $i => $p) { ?>
<div>
	<h3 class="score-selector" data-id="<?php o($i);?>"><?php o($p['name']); ?></h3>
	<table class="scores" id="<?php o($i); ?>">
	<?php $ranking = getRankingAbout($i, true); ?>
	<?php foreach ($ranking as $j => $r) { ?>
	<tr>
		<th><?php o($j+1); ?></th>
		<th><?php o(getUserInfo($r['user_id'])['username']); ?></th>
		<td><?php o($r['score'].'='.$r['rawscore'].'*'.$r['handicap']); ?></td>
		<td><?php o(($j==0)?$uc-$j+5:$uc-$j); ?>pt</td>
	</tr>
	<?php } ?>
	</table>
</div>
<?php } ?>
</div>
<script>
$('.score-selector').click(function() {
	var id = $(this).data('id');
	$("#"+id).slideToggle();
});
</script>
