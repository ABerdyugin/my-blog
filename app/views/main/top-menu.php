<?php

use app\models\User;
?>
<!-- header.html start -->
<div class="navbar navbar-default">
	<div class="navbar-header">
		<a href="/" class="navbar-brand">SiteTemplate</a>
		<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	<div class="navbar-collapse collapse" id="navbar-main">
		<ul class="nav navbar-nav">
			<li>
				<a href="/">Главная</a>
			</li>
			<li>
				<a href="/blog">Блог</a>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
            <?php if(!User::logged()):?>
			<li>
				<a href="/login">Войти</a>
			</li>
            <?php else:?>
			<li>
				<a href="/logout">Выйти</a>
			</li>
            <?php endif;?>
		</ul>
	</div>
</div>
<!-- / header.html end -->
