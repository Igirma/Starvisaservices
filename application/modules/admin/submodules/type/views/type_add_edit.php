<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['type'] = $_POST['type'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="type[users_type_id]" value="<?=(isset($data['type']['users_type_id']) && $data['type']['users_type_id'] != '' ? $data['type']['users_type_id'] : '');?>">

      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">
        
        <?php if($this->url->segment(2) == 'add') { ?>

          <div class="header">
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-projects"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php } ?>
        
        <?php if($this->url->segment(2) == 'edit'){ ?>

            <div class="header">
              <input type="hidden" name="type[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language) { ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])) { ?>
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['type']['users_type_id']?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    <?php } ?>

                <?php } ?>
                
              </div>
              
            </div>

        <?php } ?>
      
      </div>
      
      <div class="column">
      
        <div class="subheader">

          <h2>Type of visa info</h2>
          
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
              <th>Title</th>
              <td><input type="text" name="type[users_type_name]" value="<?=(isset($data['type']['users_type_name']) ? $data['type']['users_type_name'] : '');?>"></td>
            </tr>

            <!--
            <tr>
              <th>Entries</th>
              <td>
                <?php if (isset($data['entries']) && $data['entries'] !== false) { ?>
                <?php $entries_exist = (isset($data['type']['entries']) && $data['type']['entries'] !== false); ?>
                <table class="borderless multiple-select-parent entries-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="entries_list" size="20" class="multiple-select entries_list" multiple>
                    <?php if (!$entries_exist) { ?>
                    <?php foreach ($data['entries'] as $entry) { ?>
                      <option value="<?php echo $entry['user_entry_id']; ?>"><?php echo $entry['user_entry_name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <?php foreach ($data['entries'] as $entry) { ?>
                    <?php if (!in_array($entry['user_entry_id'], $data['type']['entries_selected'])) { ?>
                      <option value="<?php echo $entry['user_entry_id']; ?>"><?php echo $entry['user_entry_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="type[entries][]" size="20" class="multiple-select entries_target" multiple>
                    <?php if ($entries_exist) { ?>
                    <?php foreach ($data['type']['entries'] as $user_entry_id => $user_entry_name) { ?> 
                      <option value="<?php echo $user_entry_id; ?>" selected="selected"><?php echo $user_entry_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th>Countries</th>
              <td>
                <?php if (isset($data['countries']) && $data['countries'] !== false) { ?>
                <?php $countries_exist = (isset($data['type']['countries']) && $data['type']['countries'] !== false); ?>
                <table class="borderless multiple-select-parent countries-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="countries_list" size="20" class="multiple-select countries_list" multiple>
                    <?php if (!$countries_exist) { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                    <?php if (!in_array($country['users_country_id'], $data['type']['countries_selected'])) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="type[countries][]" size="20" class="multiple-select countries_target" multiple>
                    <?php if ($countries_exist) { ?>
                    <?php foreach ($data['type']['countries'] as $users_country_id => $users_country_name) { ?> 
                      <option value="<?php echo $users_country_id; ?>" selected="selected"><?php echo $users_country_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th>Country groups</th>
              <td>
                <?php if (isset($data['groups']) && $data['groups'] !== false) { ?>
                <?php $groups_exist = (isset($data['type']['groups']) && $data['type']['groups'] !== false); ?>
                <table class="borderless multiple-select-parent groups-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="groups_list" size="20" class="multiple-select groups_list" multiple>
                    <?php if (!$groups_exist) { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                    <?php if (!in_array($group['user_country_group_id'], $data['type']['groups_selected'])) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="type[groups][]" size="20" class="multiple-select groups_target" multiple>
                    <?php if ($groups_exist) { ?>
                    <?php foreach ($data['type']['groups'] as $user_country_group_id => $user_country_group_name) { ?> 
                      <option value="<?php echo $user_country_group_id; ?>" selected="selected"><?php echo $user_country_group_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } ?>
              </td>
            </tr>
            -->
          </table>
        
        </div>
        
      </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>