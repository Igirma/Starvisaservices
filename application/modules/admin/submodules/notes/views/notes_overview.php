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
        <h1>Notices overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>

            <tr>

              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 47%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Title</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Order</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Delete</p>
              </th>

            </tr>

          </thead>

          <tbody>
          
          <?php if(isset($data['notes']) && $data['notes'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['notes']); ?>
            
            <?php foreach ($data['notes'] as $note): ?>
            
            
            
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $note['users_notes_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
				<span style="line-height: 40px; padding-left: 10px;">
					<?=str_shorten($note['users_notes_title'], 60);?> 
					<?php if($note_subtitle = str_shorten($note['users_notes_subtitle'], 60)):?>
						<em>(<?=$note_subtitle?>)</em>
					<?php endif; ?>
				</span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
              
              <td class="order">
              
                <div class="spacer"></div>
                
                <p>
                
                  <div class="up_down">
                  
                    <?php if(permission(CONTROLLER, 'edit')): ?>
                    
                      <?php if($x == 1 && $x != $count_items): ?>

                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$note['users_notes_order'];?>/<?=$note['users_notes_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x == $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$note['users_notes_order'];?>/<?=$note['users_notes_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x != $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$note['users_notes_order'];?>/<?=$note['users_notes_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$note['users_notes_order'];?>/<?=$note['users_notes_id'];?>">
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
              
                <?php if(permission(CONTROLLER, 'edit')): ?>
                
                  <?php if($note['users_notes_active'] == 1): ?>
                  
                  <input type="radio" name="active[<?=$note['users_notes_id'];?>]" checked="checked" value="1">Yes
                  <input type="radio" name="active[<?=$note['users_notes_id'];?>]" value="0">No

                  <?php else: ?>
                  
                  <input type="radio" name="active[<?=$note['users_notes_id'];?>]" value="1">Yes
                  <input type="radio" name="active[<?=$note['users_notes_id'];?>]" checked="checked" value="0">No
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>

                <div class="spacer"></div>
                <p><?=(permission(CONTROLLER, 'delete') ? '<a title="Delete" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $note['users_notes_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>

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