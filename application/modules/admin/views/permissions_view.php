<?php

require_once(ELEM_DIR . 'admin_header.php');

?>

<div id="container">
  <div id="overview">

    <div class="column">
  
      <div class="pie header">
        
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-permissions"></div>
        <h1><?=$this->lang->line('permission_settings');?></h1>
    
      </div>
      
      <form method="post" action="<?=$this->url->current;?>">

        <table id="overview">
        
          <thead>
          
            <tr>
              
              <th style="width: 5%;">
                Nr
              </th>
              <th style="width: 25%;" class="text_left">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('rank_name');?></p>
              </th>
              <th style="width: 30%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('access');?></p>
              </th>
              <th style="width: 30%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('permissions');?></p>
              </th>
              
            </tr>
            
          </thead>
        
          <tbody>
            
            <?php $i = 0; ?>
            <?php foreach($data['rights'] as $right): ?>
            
            <tr>
            
              <td>
                <?=++$i;?>
              </td>
              
              <td class="text_left">
                <span style="line-height: 40px; padding-left: 10px; display: block;"><?=$right['name']?></span>
              </td>
              
              <td>
                
                <input type="hidden" name="set_rules_hack" value="1" />
                
                <?php foreach($right['mods'] as $module): ?>
                  
                  <div class="permission_mod_boxes"><div class="permission_mod_name"><?=$module['info']['name']?></div><div class="permission_choice">JA! <input type="radio" <?=($module['permission'] == '1' ? 'checked' : '')?> name="permission[<?=$right['rights_id']?>][<?=$module['info']['module_id']?>]" value="1" /></div><div class="permission_choice">NEE! <input type="radio" <?=($module['permission'] == '0' ? 'checked' : '')?> name="permission[<?=$right['rights_id']?>][<?=$module['info']['module_id']?>]" value="0" /></div></div>
                  
                <?php endforeach; ?>
                
              </td>
              
              <td>
                
                <?php foreach($right['mods'] as $module): ?>
                  
                  <input type="hidden" name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][add]" value="off" />
                  <input type="hidden" name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][edit]" value="off" />
                  <input type="hidden" name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][delete]" value="off" />

                  <div class="permission_boxes"><input type="checkbox" <?=($module['permissions']['add'] == 1 ? 'checked' : '')?> name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][add]" /> Add</div>
                  <div class="permission_boxes"><input type="checkbox" <?=($module['permissions']['edit'] == 1 ? 'checked' : '')?> name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][edit]" /> Edit</div>
                  <div class="permission_boxes"><input type="checkbox" <?=($module['permissions']['delete'] == 1 ? 'checked' : '')?> name="handle_permissions[<?=$right['rights_id']?>][<?=$module['module_id']?>][delete]" /> Delete<br></div>
                  
                <?php endforeach; ?>
                
              </td>
              
            </tr>
            
            <?php endforeach; ?>
            
          </tbody>
        
        </table>

      </form>
      
    </div>
  </div>
</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>