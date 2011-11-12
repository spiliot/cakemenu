<?php
echo $this->Cakemenu->libs($type);
echo $this->Cakemenu->generate($menu);
;
?>

<div class="actions">
  <h3><?php __('Actions'); ?></h3>
  <ul>
    <li><?php echo $this->Html->link(__('Horizontal'), array('action' => 'preview')); ?></li>
    <li><?php echo $this->Html->link(__('Vertical'), array('action' => 'preview', 'vertical')); ?></li>
    <li><?php echo $this->Html->link(__('Nav bar'), array('action' => 'preview', 'navbar')); ?></li>
    <li><?php echo $this->Html->link(__('Back'), array('action' => 'index')); ?></li>
  </ul>
</div>