<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['country'] = $_POST['country'];
  
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="country_edit_add">
    
      <input type="hidden" name="country[users_country_id]" value="<?=(isset($data['country']['users_country_id']) && $data['country']['users_country_id'] != '' ? $data['country']['users_country_id'] : '');?>">

      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">
        
        <?php if($this->url->segment(2) == 'add'): ?>

          <div class="header">
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="country[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['country']['users_country_id']?>/1">
                      <span class="padding_extra_languages language sprite_en"></span>
                    </a>
                    
                    <?php endif; ?>

                <?php endforeach; ?>
                
              </div>
              
            </div>

        <?php endif; ?>
      
      </div>
      
      <div class="column">
      
        <div class="subheader">

          <h2><?=$this->lang->line('country_info');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="Info">
                  <div style="position: relative; top: 15px;" class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle min">
                  <div style="position: relative; top: 15px; left: -1px;" class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
        
        </div>

        <div class="subcolumn">

          <table>

            <tr>
              <th>Country title</th>
              <td><input type="text" name="country[users_country_name]" value="<?=(isset($data['country']['users_country_name']) ? $data['country']['users_country_name'] : '');?>"></td>
            </tr>

            <tr>
              <th>Country code</th>
              <td><input type="text" name="country[users_country_code]" value="<?=(isset($data['country']['users_country_code']) ? $data['country']['users_country_code'] : '');?>"></td>
            </tr>
            
            <tr>
              <th>Groups list (destinations)</th>
              <td>
                <?php if (isset($data['groups']) && $data['groups'] !== false) { ?>
                <?php $groups_exist = (isset($data['country']['groups']) && $data['country']['groups'] !== false); ?>
                <table class="borderless multiple-select-parent groups-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="groups_list" size="20" class="multiple-select groups_list" multiple>
                    <?php if (!$groups_exist) { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } elseif (isset($data['country']['groups_selected'])) { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                    <?php if (!in_array($group['user_country_group_id'], $data['country']['groups_selected'])) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="country[groups][]" size="20" class="multiple-select groups_target" multiple>
                    <?php if ($groups_exist) { ?>
                    <?php foreach ($data['country']['groups'] as $user_country_group_id => $user_country_group_name) { ?> 
                      <option value="<?php echo $user_country_group_id; ?>" selected="selected"><?php echo $user_country_group_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } else { ?>
                There are no country groups. <a href="<?php echo SITE_URL; ?>en/admin/country_groups/add">Add new?</a>
                <input type="hidden" name="country[groups][]" value="">
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th>Groups list (nationalities)</th>
              <td>
                <?php if (isset($data['nationalities']) && $data['nationalities'] !== false) { ?>
                <?php $groups_exist = (isset($data['country']['nationalities']) && $data['country']['nationalities'] !== false); ?>
                <table class="borderless multiple-select-parent nationalities-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="nationalities_list" size="20" class="multiple-select groups_list" multiple>
                    <?php if (!$groups_exist) { ?>
                    <?php foreach ($data['nationalities'] as $group) { ?>
                      <option value="<?php echo $group['user_nationality_group_id']; ?>"><?php echo $group['user_nationality_group_name']; ?></option>
                    <?php } ?>
                    <?php } elseif (isset($data['country']['nationalities_selected'])) { ?>
                    <?php foreach ($data['nationalities'] as $group) { ?>
                    <?php if (!in_array($group['user_nationality_group_id'], $data['country']['nationalities_selected'])) { ?>
                      <option value="<?php echo $group['user_nationality_group_id']; ?>"><?php echo $group['user_nationality_group_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="country[nationalities][]" size="20" class="multiple-select groups_target" multiple>
                    <?php if ($groups_exist) { ?>
                    <?php foreach ($data['country']['nationalities'] as $user_nationality_group_id => $user_nationality_group_name) { ?> 
                      <option value="<?php echo $user_nationality_group_id; ?>" selected="selected"><?php echo $user_nationality_group_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } else { ?>
                There are no nationalities groups. <a href="<?php echo SITE_URL; ?>en/admin/nationality_groups/add">Add new?</a>
                <input type="hidden" name="country[nationalities][]" value="">
                <?php } ?>
              </td>
            </tr>

          </table>

        </div>
        
      </div>
  
    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>