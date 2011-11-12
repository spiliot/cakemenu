<div class="cakemenu index">
  <h2><?php echo __('Menu tree'); ?></h2>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th><?php echo __('ID'); ?></th>
      <th><?php echo __('Menu'); ?></th>
      <th><?php echo __('Link'); ?></th>
      <th><?php echo __('Actions'); ?></th>
    </tr>
    <?php
    $i = 0;
    foreach ($menu_list as $key => $node):
      ?>
      <tr>
        <td style="text-align: center;"><?php echo $key; ?>&nbsp;</td>
        <td style="text-align: left"><?php echo $node; ?>&nbsp;</td>
        <td style="text-align: left">
          <?php
          if (in_array($key, array_keys($links))) {
            echo $links[$key];
          }
          ?>&nbsp;
        </td>
        <td class="actions">
          <?php echo $this->Html->link(__('up'), array('action' => 'move', $key, 'up')); ?> |
          <?php echo $this->Html->link(__('down'), array('action' => 'move', $key, 'down')); ?> | 
          <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $key)); ?> | 
  <?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $key), null, sprintf(__('Are you sure you want to delete # %s?'), $key)); ?>
        </td>
      </tr>
<?php endforeach; ?>
  </table>
</div>
<div class="actions">
  <h3><?php __('Actions'); ?></h3>
  <ul>
    <li><?php echo $this->Html->link(sprintf(__('New %s'), __('Menu')), array('action' => 'edit')); ?></li>
    <li><?php echo $this->Html->link(__('Preview'), array('action' => 'preview')); ?></li>
    <li><?php echo $this->Html->link(__('Recover hierarchy'), array('action' => 'recover')); ?></li>
  </ul>
</div>