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
        <h1>Countries groups overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 38%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Group name</p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p>Order</p>
              </th>
              <th style="width: 12%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['groups']) && $data['groups'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['groups']); ?>
            
            <?php foreach($data['groups'] as $group): ?>
              
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $group['user_country_group_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($group['user_country_group_name'], 100);?></span>

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

                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$group['user_country_group_order'];?>/<?=$group['user_country_group_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x == $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$group['user_country_group_order'];?>/<?=$group['user_country_group_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x != $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$group['user_country_group_order'];?>/<?=$group['user_country_group_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$group['user_country_group_order'];?>/<?=$group['user_country_group_id'];?>">
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

                  <?php if($group['user_country_group_active'] == 1): ?>
                  
                  <input type="radio" name="active[<?=$group['user_country_group_id'];?>]" checked="checked" value="1">Yes
                  <input type="radio" name="active[<?=$group['user_country_group_id'];?>]" value="0">No

                  <?php else: ?>
                  
                  <input type="radio" name="active[<?=$group['user_country_group_id'];?>]" value="1">Yes
                  <input type="radio" name="active[<?=$group['user_country_group_id'];?>]" checked="checked" value="0">No
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>
                <div class="spacer"></div>
                
                <p>
                
                  <?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $group['user_country_group_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
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