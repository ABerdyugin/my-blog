<?php

use app\controllers\BlogController as BC;

/**
 * @var array $params
 */
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
<script src="/js/tinymce.min.js" referrerpolicy="origin"></script>
<form action="<?= $params['route'] ?>" method="post" enctype="multipart/form-data">
    <?php // TODO добавить CSRF проверку. но в текущей задаче этого не требуется  ?>
    <? ?>
  <div class="form-group">
    <label for="post-poster">Постер</label>
    <input type="file" name="post-poster" id="post-poster" class="form-control">
  </div>
    <?php if ($params['action'] == 'insert'): ?>
      <input type="hidden" name="act" value="insert">
    <?php elseif ($params['action'] == "edit"): ?>
      <input type="hidden" name="act" value="update">
      <input type="hidden" name="post-id" value="<?= $params['post-id'] ?>">
        <?php if ($params['post-poster'] != ''): ?>
        <div class="form-group">
          <label for="delete-poster">Удалить постер</label>
          <input type="checkbox" name="delete-poster" id="delete-poster" value="1">
        </div>
        <?php endif; ?>
    <?php endif; ?>
  <div class="form-group">
    <label for="post-title">Заголовок *</label>
    <input type="text" class="form-control" name="post-title" id="post-title" value="<?= $params['post-title'] ?>">
  </div>
  <div class="form-group">
    <label for="post-cutcontent">Анонс</label>
    <textarea name="post-cutcontent" id="post-cutcontent" cols="30" rows="3"
              class="form-control"><?= $params['post-cutcontent'] ?></textarea>
  </div>
  <div class="form-group">
    <label for="post-content">Полный текст *</label>
    <textarea name="post-content" id="post-content" cols="30" rows="5"
              class="form-control"><?= $params['post-content'] ?></textarea>
  </div>
  <div class="form-group">
    <button type="submit" class="btn btn-success"
            id="submit-form"><?= $params['action'] == 'insert' ? "Добавить" : "Внести изменения" ?></button>
    <a href="/blog/<?= $params['action'] == 'edit' ? $params['post-id'] : "" ?>" class="btn btn-danger pull-right"
       onclick="return confirm('Отменить добавление материала?')">Отмена</a>
  </div>
  <div class="form-group">
    <p>поля отмеченные звездочкой "*" обязательны для заполнения</p>
  </div>

</form>
<script>
    tinymce.init({
        selector: 'textarea',
        height: 150,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code help wordcount'
        ],
        mobile: {
            theme: 'mobile'
        },
        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tiny.cloud/css/codepen.min.css'
        ],
    });
    document.getElementById("submit-form").addEventListener("click", ev => {
        let postContent = document.getElementById("post-content");
        console.log(postContent.value);
    })
</script>
