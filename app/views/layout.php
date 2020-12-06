<?php
/**
 * @var View $this
 * @var array $params
 */

use core\View;

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title><?= $params['site-title'] ?>Site Template - Main Page</title>

    <!-- link.html start -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="/css/bootstrap-flatly.min.css?_=1" type="text/css"/>
    <link rel="stylesheet" href="/css/style.css" type="text/css"/>
    <!-- / link.html end -->
</head>
<body>
<div id="wrapper">
    <div class="container">
			<?php
			View::chunk('main.top-menu');
			?>
        <div class="row">
            <div class="col-lg-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= $params['page-header'] ?>
                    </div>
                    <div class="panel-body">
                        <?= $params['page-content'] ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
							<?php
                            View::chunk("main.right-menu");
							?>
            </div>
        </div>
    </div>
</div>

<!-- footer.html start -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<!-- / footer.html end -->
</body>
</html>