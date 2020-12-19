<?php
/**
 * @var array $params
 */
?>
<nav aria-label="Page navigation">
  <ul class="pagination">
      <?php if ($params['page-list-current'] > 1): ?>
        <li>
          <a href="<?= $params['page-list-link'] ?>page=<?= $params['page-list-current']-1; ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $params['page-list-count']; $i++): ?>
        <li><a href="<?= $params['page-list-link'] ?>page=<?= $i; ?>"><?= $i; ?></a></li>
      <?php endfor; ?>
    <?php if($params['page-list-current'] < $params['page-list-count']):?>
    <li>
      <a href="<?= $params['page-list-link'] ?>page=<?= $params['page-list-current']+1; ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</nav>
