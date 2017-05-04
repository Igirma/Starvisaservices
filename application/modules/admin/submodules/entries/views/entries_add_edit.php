<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['entry'] = $_POST['entry'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="country_edit_add">
    
      <input type="hidden" name="entry[user_entry_id]" value="<?=(isset($data['entry']['user_entry_id']) && $data['entry']['user_entry_id'] != '' ? $data['entry']['user_entry_id'] : '');?>">

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
              <input type="hidden" name="entry[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>

                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['entry']['user_entry_id']?>/1">
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

          <h2>Entry information</h2>
          
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
              <th>Entry name</th>
              <td><input type="text" name="entry[user_entry_name]" value="<?=(isset($data['entry']['user_entry_name']) ? $data['entry']['user_entry_name'] : '');?>"></td>
            </tr>

            <!--
            <tr>
              <th>Visa types</th>
              <td>
                <?php if (isset($data['types']) && $data['types'] !== false) { ?>
                <?php $types_exist = (isset($data['entry']['types']) && $data['entry']['types'] !== false); ?>
                <table class="borderless multiple-select-parent types-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="types_list" size="20" class="multiple-select types_list" multiple>
                    <?php if (!$types_exist) { ?>
                    <?php foreach ($data['types'] as $type) { ?>
                      <option value="<?php echo $type['users_type_id']; ?>"><?php echo $type['users_type_name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <?php foreach ($data['types'] as $type) { ?>
                    <?php if (!in_array($type['users_type_id'], $data['entry']['types_selected'])) { ?>
                      <option value="<?php echo $type['users_type_id']; ?>"><?php echo $type['users_type_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="entry[types][]" size="20" class="multiple-select types_target" multiple>
                    <?php if ($types_exist) { ?>
                    <?php foreach ($data['entry']['types'] as $users_type_id => $users_type_name) { ?> 
                      <option value="<?php echo $users_type_id; ?>" selected="selected"><?php echo $users_type_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } else { ?>
                There are no visa types. <a href="<?php echo SITE_URL; ?>en/admin/type/add">Add new?</a>
                <input type="hidden" name="entry[types]" value="">
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th>Services</th>
              <td>
                <?php if (isset($data['services']) && $data['services'] !== false) { ?>
                <?php $services_exist = (isset($data['entry']['services']) && $data['entry']['services'] !== false); ?>
                <table class="borderless multiple-select-parent services-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="services_list" size="20" class="multiple-select services_list" multiple>
                    <?php if (!$services_exist) { ?>
                    <?php foreach ($data['services'] as $service) { ?>
                      <option value="<?php echo $service['users_services_id']; ?>"><?php echo $service['users_services_name']; ?></option>
                    <?php } ?>
                    <?php } elseif (isset($data['entry']['services_selected'])) { ?>
                    <?php foreach ($data['services'] as $service) { ?>
                    <?php if (!in_array($service['users_services_id'], $data['entry']['services_selected'])) { ?>
                      <option value="<?php echo $service['users_services_id']; ?>"><?php echo $service['users_services_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="entry[services][]" size="20" class="multiple-select services_target" multiple>
                    <?php if ($services_exist) { ?>
                    <?php foreach ($data['entry']['services'] as $users_services_id => $users_services_name) { ?> 
                      <option value="<?php echo $users_services_id; ?>" selected="selected"><?php echo $users_services_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } else { ?>
                There are no services. <a href="<?php echo SITE_URL; ?>en/admin/services/add">Add new?</a>
                <input type="hidden" name="entry[services]" value="">
                <?php } ?>
              </td>
            </tr>
            -->
            
          </table>

        </div>
        
      </div>
  
    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>