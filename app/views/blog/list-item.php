<?php
/**
 * @var array $params
 */

?>
<div class="panel panel-default">
	<img class="img-responsive" src="/img/blog/<?= $params['item-poster']?>" />
	<div class="panel-heading" style="font-size: 23px;">
		<a href="<?= $params['item-link'] ?>"><?= $params['item-title']?></a>
	</div>
	<div class="panel-body">
		<p><?= $params['item-cut-content']?></p>
		<div class="pull-right">Добавлено: <?= $params['item-created']?></div>
		<div>Автор: <?= $params['item-author']?></div>
		<hr/>
		<div class="pull-right">
			<div class="comments">
				<i class="fa fa-comments" style="margin-right: 10px;"></i><?= $params['item-comments']?>
			</div>
			<div class="views">
				<i class="fa fa-eye" style="margin-right: 10px;"></i><?= $params['item-views']?>
			</div>
		</div>
		<a href="<?= $params['item-link'] ?>" class="btn btn-primary">Читать далее</a>
	</div>
</div>
