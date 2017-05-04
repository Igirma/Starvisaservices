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
        <h1>Visa types overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>

            <tr>

              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 57%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Title</p>
              </th>
              <!--
              <th style="width: 42%;">
                <div class="spacer"></div>
                <p>Entries</p>
              </th>
              -->
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Order</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Delete</p>
              </th>

            </tr>

          </thead>

          <tbody>
          
          <?php if(isset($data['types']) && $data['types'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['types']); ?>
            
            <?php foreach ($data['types'] as $type): ?>
            
            
            
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $type['users_type_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($type['users_type_name'], 60);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
              
              <!--
              <td>
                
                <div class="spacer"></div>
                <span style="line-height: 40px; padding-left: 10px; padding-right: 10px;"><?php echo ($type['entries'] !== false ? str_shorten($type['entries'], 200) : '<i>No entries selected</i>'); ?></span>

              </td>
              -->
              
              <td class="order">
              
                <div class="spacer"></div>
                
                <p>
                
                  <div class="up_down">
                  
                    <?php if(permission(CONTROLLER, 'edit')): ?>
                    
                      <?php if($x == 1 && $x != $count_items): ?>

                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$type['users_type_order'];?>/<?=$type['users_type_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x == $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$type['users_type_order'];?>/<?=$type['users_type_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x != $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$type['users_type_order'];?>/<?=$type['users_type_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$type['users_type_order'];?>/<?=$type['users_type_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                    
                    <?php endif; ?>
                    
                    <div class="clear"></div>
                    
                  </div>
                
                </p>
              
              </td>
              
              <td>

                <div class="spacer"></div>
                <p><?=(permission(CONTROLLER, 'delete') ? '<a title="Delete" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $type['users_type_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>

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