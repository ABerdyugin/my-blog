<?php
/**
 * @var array $params
 */
?>

<div class="media">
	<a class="pull-left" href="#">
		<img class="media-object" src="/img/user/avatar.png" style="width: 64px; height: 64px;" />
	</a>
	<div class="media-body">
		<h4 class="media-heading"><?= $params['comment-author']?></h4>
		<?= $params['comment-content']?>
	</div>
</div>

