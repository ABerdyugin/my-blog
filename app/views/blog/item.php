<?php
/**
 * @var array $params
 */

use app\models\User;
use core\View;
use app\controllers\UserController as UC;

?>
<?php if($params['error']):?>
  <div class="row">
    <div class="col-12 alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
      </button>
        <?= $params['error-text']; ?>
    </div>
  </div>
<?php endif; ?>
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
  <div class="panel-body" id="app-comments">
      <?php
      //foreach ($params['post-comment-list'] as $comment) {            View::chunk('comment.list-item', $comment);        }
      ?>
    <div>
      <div class="media" v-for="item in comments">
        <a class="pull-left" href="#">
          <img class="media-object" src="/img/user/avatar.png" style="width: 64px; height: 64px;"/>
        </a>
        <a v-if="item.show" v-bind:href="'/api/comment/delete/' + item.id" v-on:click="deleteComment(item.id)"
           class="pull-right btn btn-danger"
           onclick="return false">Удалить</a>
        <div class="media-body">
          <h4 class="media-heading">{{ item.login }}</h4>
          {{ item.content }}
        </div>
      </div>
    </div>
    <hr/>
      <?php if (User::logged()): ?>
        <form method="post">
          <input type="hidden" name="id" v-model="postId" value="">
          <div class="form-group">
            <textarea class="form-control" rows="3" placeholder="Текст комментария..." id="comment-content"
                      v-model="newComment"></textarea>
          </div>
          <button type="button" class="btn btn-block btn-info btn-default" v-on:click="pushComment()">Отправить</button>
        </form>
      <?php else: ?>
        <div>
          <p>До добавления комментария необходимо авторизоваться</p>
        </div>

      <?php endif; ?>
  </div>
  <script>
      var App = new Vue({
          el: '#app-comments',
          created() {
              this.fetchData();
          },
          data: {
              userId: <?= $params['user-id'] ?>,
              comments: [],
              newComment: "",
              postId: <?= $params['post-id'] ?>
          },
          methods: {
              fetchData() {
                  axios.get('/api/comment/list/' + this.postId).then(response => {
                      this.comments = response.data;
                  });
              },
              pushComment() {
                  let form = new FormData();
                  form.append('post-id', this.postId);
                  form.append('comment-content', this.newComment);
                  axios.post('/api/comment/add/' + this.postId, form)
                      .then(response => {
                          this.comments = response.data;
                      });
                  this.newComment = '';
                  document.getElementById("comment-content").focus();
              },
              deleteComment(id) {
                  if(confirm("Удалить комментарий?")){
                      let url = '/api/comment/delete/' + this.postId + '/' + id;
                      axios.get(url).then(response => {
                          this.comments = response.data;
                      })
                  }
              }
          }
      });
  </script>
</div>
<!-- / содержимое страницы end -->
