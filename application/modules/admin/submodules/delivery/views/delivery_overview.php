<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add">Add</a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-projects"></div>
        <h1>Delivery methods overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 54%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Title</p>
              </th>
              <th style="width: 28%;">
                <div class="spacer"></div>
                <p>Price</p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p>Delete</p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['delivery_methods']) && $data['delivery_methods'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['delivery_methods']); ?>
            
            <?php foreach ($data['delivery_methods'] as $delivery): ?>
            
            
            
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $delivery['delivery_method_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($delivery['delivery_method_name'], 60);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
                
              <td>
              
                <div class="spacer"></div>
                
                <p><?php echo $delivery['delivery_method_price']; ?></p>
              
              </td>
              
              <td>
                <div class="spacer"></div>
                
                <p>
                
                  <?=(permission(CONTROLLER, 'delete') ? '<a title="Delete" class="delete" href="' . SITE_URL . 'admin/delivery/delete/' . $delivery['delivery_method_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
                </p>
              </td>
            
            </tr>
            
            <?php endforeach; ?>
          
          <?php endif; ?>
          
          </tbody>
        
        </table>
        
      </form>
    </div>
  </div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>