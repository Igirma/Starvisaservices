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
        <h1><?=$this->lang->line('overview_header');?></h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 13%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('country_title');?></p>
              </th>
              <th style="width: 6%;">
                <div class="spacer"></div>
                <p>Code</p>
              </th>
              <th style="width: 29%;">
                <div class="spacer"></div>
                <p>Country group(s)</p>
              </th>
              <th style="width: 29%;">
                <div class="spacer"></div>
                <p>Nationality group(s)</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['countries'])): ?>
            
            <?php $x = 1; ?>
            
            <?php foreach($data['countries'] as $country): ?>
              
            <tr>
              
              <td>
                <?=$x?>
                <?php $x++; ?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $country['users_country_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($country['users_country_name'], 20);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
  
              <td>

                <div class="spacer"></div>
                <span style="line-height: 40px;"><?=$country['users_country_code'];?></span>

              </td>

              <td>
                
                <div class="spacer"></div>
                <span style="line-height: 40px; padding-left: 10px; padding-right: 10px;"><?php echo ($country['groups'] !== false ? str_shorten($country['groups'], 40) : '<i>No groups selected</i>'); ?></span>

              </td>
              
              <td>
                
                <div class="spacer"></div>
                <span style="line-height: 40px; padding-left: 10px; padding-right: 10px;"><?php echo ($country['nationalities'] !== false ? str_shorten($country['nationalities'], 40) : '<i>No groups selected</i>'); ?></span>

              </td>

              <td>
              
                <div class="spacer"></div>
                
                <p>
              
                <?php if(permission(CONTROLLER, 'edit')): ?>
                
                  <?php if($country['users_country_active'] == 1): ?>
                  
                  <input type="radio" name="active[<?=$country['users_country_id'];?>]" checked="checked" value="1">Yes
                  <input type="radio" name="active[<?=$country['users_country_id'];?>]" value="0">No

                  <?php else: ?>
                  
                  <input type="radio" name="active[<?=$country['users_country_id'];?>]" value="1">Yes
                  <input type="radio" name="active[<?=$country['users_country_id'];?>]" checked="checked" value="0">No
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>
                <div class="spacer"></div>
                
                <p class="hidden">
                
                  <?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $country['users_country_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
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