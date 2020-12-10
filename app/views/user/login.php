<?php
/**
 * @var array $params
 */
?>
<!-- содержимое страницы start -->
<form method="post" action="/login">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input type="text" name="login" class="form-control text-center" placeholder="Логин"
                   value="<?= $params['login'] ?>" autofocus/>
        </div>
    </div>
    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-6 col-lg-offset-3">
            <input type="password" name="paswd" class="form-control text-center" placeholder="Пароль" value=""/>
        </div>
    </div>
    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-6 col-lg-offset-3">
            <input type="submit" name="auth" class="btn btn-block btn-md btn-success" value="Войти"/>
        </div>
    </div>
	<?php if ($params['error']): ?>
      <div class="row" style="margin-top: 15px;">
				<?php switch ($params['error-type']):
					case "empty-all": ?>
              <div class="col-lg-6 col-lg-offset-3">
                  <div class="alert alert-danger text-center">Не указан логин и пароль</div>
              </div>
						<?php break;
					case "empty-login": ?>
              <div class="col-lg-6 col-lg-offset-3">
                  <div class="alert alert-danger text-center">Не указан логин</div>
              </div>
						<?php break;
					case "empty-password": ?>
              <div class="col-lg-6 col-lg-offset-3">
                  <div class="alert alert-danger text-center">Не указан пароль</div>
              </div>
						<?php break;
					case "mismatch": ?>
              <div class="col-lg-6 col-lg-offset-3">
                  <div class="alert alert-danger text-center">Неверная пара логин/пароль</div>
              </div>
						<?php break; endswitch; ?>
      </div>
	<?php endif; ?>
</form>
<!-- / содержимое страницы end -->