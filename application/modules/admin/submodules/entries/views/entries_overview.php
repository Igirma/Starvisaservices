<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_country');?></a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
        <h1>Entries overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 58%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Entry name</p>
              </th>
              <!--
              <th style="width: 38%;">
                <div class="spacer"></div>
                <p>Visa type(s)</p>
              </th>
              -->
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Order</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['entries']) && $data['entries'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['entries']); ?>
            
            <?php foreach($data['entries'] as $entry): ?>
              
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $entry['user_entry_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($entry['user_entry_name'], 100);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>

              <!--
              <td>
                
                <div class="spacer"></div>
                <span style="line-height: 40px; padding-left: 10px; padding-right: 10px;"><?php echo ($entry['entry_types'] !== false ? str_shorten($entry['entry_types'], 200) : '<i>No visa types selected</i>'); ?></span>

              </td>
              -->
  
               <td class="order">
              
                <div class="spacer"></div>
                
                <p>
                
                  <div class="up_down">
                  
                    <?php if(permission(CONTROLLER, 'edit')): ?>
                    
                      <?php if($x == 1 && $x != $count_items): ?>

                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$entry['user_entry_order'];?>/<?=$entry['user_entry_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x == $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$entry['user_entry_order'];?>/<?=$entry['user_entry_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x != $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$entry['user_entry_order'];?>/<?=$entry['user_entry_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$entry['user_entry_order'];?>/<?=$entry['user_entry_id'];?>">
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
                
                <p>
                
                  <?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $entry['user_entry_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
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