<ul class="pagination justify-content-end float-right">
  <?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '', 'class' => 'page-item')); ?>
</ul>