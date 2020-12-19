<?php
/**
 * @var array $params
 */

use app\models\User;
use app\controllers\BlogController as BC;
use app\controllers\UserController as UC;

?>
<!-- right.html start -->
<form action="/search" method="get" style="margin-bottom: 20px;">

	<input type="text" name="search_text" class="form-control" value="" placeholder="Поиск"/>
</form>

<div class="panel panel-primary">
	<div class="panel-heading">Навигация</div>
	<div class="list-group">
		<a href="/" class="list-group-item">Главная</a>
		<a href="/blog" class="list-group-item">Блог</a>
	</div>
</div>
<?php if(User::logged() ):?>
<div class="panel panel-primary">
    <div class="panel-heading">Управление материалами</div>
    <div class="panel-body">
        <a href="/blog/add/" class="btn btn-success btn-md btn-block">Добавить материал</a>
        <?php if((BC::isAuthor() || UC::isAdminNow()) && $params['post-id'] > 0): ?>
        <a href="/blog/<?= $params['post-id']; ?>/edit/" class="btn btn-info btn-md btn-block">Изменить</a>
        <a href="/blog/<?= $params['post-id']; ?>/delete/" class="btn btn-danger btn-md btn-block"  onclick="return confirm('Удалить материал?')">Удалить</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<!-- / right.html end -->

