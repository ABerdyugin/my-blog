<?php
/**
 * @var array $params
 */

use core\View;

?>
<!-- содержимое страницы start -->
<div class="panel panel-default">
    <?php if ($params['post-poster']): ?>
        <img class="img-responsive" src="/img/<?= $params['post-poster'] ?>"/>
    <?php endif; ?>
    <div class="panel-heading" style="font-size: 23px;"><?= $params['post-title'] ?></div>
    <div class="panel-body">
        <?= $params['post-cut-content'] ?>
        <hr/>
        <?= $params['post-content'] ?>
        <script type="text/javascript">
            (function () {
                if (window.pluso) if (typeof window.pluso.start == "function") return;
                if (window.ifpluso == undefined) {
                    window.ifpluso = 1;
                    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                    s.type = 'text/javascript';
                    s.charset = 'UTF-8';
                    s.async = true;
                    s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
                    var h = d[g]('body')[0];
                    h.appendChild(s);
                }
            })();
        </script>
        <div class="pluso" data-background="#ebebeb" data-options="big,square,line,horizontal,nocounter,theme=04"
             data-services="vkontakte,odnoklassniki,facebook,formspring,twitter,juick,livejournal,friendfeed,yazakladki,liveinternet,memori,pinterest,tumblr,misterwong,bobrdobr,moikrug,webmoney,bookmark,yandex,pinme,google,vkrugu,moimir,myspace,surfingbird,moemesto,yahoo,webdiscover,stumbleupon,readability,blogger,springpad,delicious,digg,evernote,instapaper,linkedin,googlebookmark,email,print"></div>
        <hr/>
        <div class="pull-right">
            <div class="comments">
                <i class="fa fa-comments" style="margin-right: 10px;"></i><?= $params['post-comments'] ?>
            </div>
            <div class="views">
                <i class="fa fa-eye" style="margin-right: 10px;"></i><?= $params['post-views'] ?>
            </div>
        </div>
        <div>
            <div>Автор: <?= $params['post-author'] ?></div>
            <div>Добавлено: <?= $params['post-created'] ?></div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <?php
        foreach ($params['post-comment-list'] as $comment) {
            View::chunk('comment.list-item', $comment);
        }
        ?>
        <hr/>
        <form method="post">
            <div class="form-group">
                <textarea class="form-control" rows="3" placeholder="Текст комментария..."></textarea>
            </div>
            <button type="submit" class="btn btn-block btn-info btn-default">Отправить</button>
        </form>
    </div>
</div>
<!-- / содержимое страницы end -->
