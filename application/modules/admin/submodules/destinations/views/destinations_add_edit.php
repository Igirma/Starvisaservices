<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['destination'] = $_POST['destination'];
}
$id = isset($data['destination']['users_destinations_selected_id']) && $data['destination']['users_destinations_selected_id'] != '' ? $data['destination']['users_destinations_selected_id'] : '';
$is_country = isset($data['destination']['users_country_group_type']) && $data['destination']['users_country_group_type'] == 'country';
$is_group = isset($data['destination']['users_country_group_type']) && $data['destination']['users_country_group_type'] == 'group';
$is_nationality_country = isset($data['destination']['users_nationality_group_type']) && $data['destination']['users_nationality_group_type'] == 'country';
$is_nationality_group = isset($data['destination']['users_nationality_group_type']) && $data['destination']['users_nationality_group_type'] == 'group';

//$this->url->current;
?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="" id="country_edit_add">
    
      <div class="menu_float">

        <input class="save" type="submit" name="save_stay" value="Save and stay">
		
		<a class="back_button" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/preview_application/' . $this->url->segment(3);?>"><div class="back_button">Preview</div></a>

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
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$id;?>">
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

          <h2>Destination information</h2>
          
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
              <td>

                <table class="borderless multiple-selects">
                  <tr>
                    <td style="width: 48%; padding-left: 2%;">

                      <?php if (isset($data['groups']) && $data['groups'] !== false) { ?>
                      <div class="switch label label-country<?php if ($is_group) echo ' hidden';?>">
                        <b>Country destination</b> or <a href="javascript:;" data-target="groups">Country group</a>
                      </div>
                      <div class="switch label label-country<?php if ($is_country || !isset($data['destination']['users_destination_id'])) echo ' hidden';?>">
                        <b>Country group</b> or <a href="javascript:;" data-target="countries">Country destination</a>
                      </div>
                      <?php } else { ?>
                        <b>Country destination</b>
                      <?php } ?>

                    </td>
                    <td style="width: 48%;">

                      <?php if (isset($data['nationalities_groups']) && $data['nationalities_groups'] !== false) { ?>
                      <div class="switch label label-country<?php if ($is_nationality_group) echo ' hidden';?>">
                        <b>Nationality</b> or <a href="javascript:;" data-target="nationalities_groups">Nationality group</a>
                      </div>
                      <div class="switch label label-country<?php if ($is_nationality_country || !isset($data['destination']['users_nationality_id'])) echo ' hidden';?>">
                        <b>Nationality group</b> or <a href="javascript:;" data-target="nationalities">Nationality</a>
                      </div>
                      <?php } else { ?>
                        <b>Nationality</b>
                      <?php } ?>

                    </td>
                  </tr>
                  <tr>
                    <td style="width: 48%; padding-left: 2%;">

                      <div class="target countries-groups countries<?php if ($is_group) echo ' hidden'; ?>">
                      <?php if (isset($data['countries']) && $data['countries'] !== false) { ?>
                      <select name="destination[countries]" class="select destination countries">
                        <option value="">Select your country</option>
                      <?php foreach ($data['countries'] as $country) { ?>
                        <option value="<?php echo $country['users_country_id']; ?>"<?php if ($is_country && $data['destination']['users_destination_id'] == $country['users_country_id']) echo ' selected="selected"'; ?>><?php echo $country['users_country_name']; ?></option>
                      <?php } ?>
                      </select>
                      <?php } else { ?>
                      There are no countries.
                      <input type="hidden" name="destination[countries]" value="">
                      <?php } ?>
                      </div>
                      
                      <div class="target countries-groups groups<?php if ($is_country || !isset($data['destination']['users_destination_id'])) echo ' hidden'; ?>">
                      <?php if (isset($data['groups']) && $data['groups'] !== false) { ?>
                      <select name="destination[groups]" class="select destination groups">
                        <option value="">Select your country group</option>
                      <?php foreach ($data['groups'] as $group) { ?>
                        <option value="<?php echo $group['user_country_group_id']; ?>"<?php if ($is_group && $data['destination']['users_destination_id'] == $group['user_country_group_id']) echo ' selected="selected"'; ?>><?php echo $group['user_country_group_name']; ?></option>
                      <?php } ?>
                      </select>
                      <?php } else { ?>
                      There are no groups.
                      <input type="hidden" name="destination[groups]" value="">
                      <?php } ?>
                      </div>

                    </td>
                    <td style="width: 48%;">
                    
                      <div class="target countries-groups nationalities<?php if ($is_nationality_group) echo ' hidden'; ?>">
                      <?php if (isset($data['countries']) && $data['countries'] !== false) { ?>
                      <select name="destination[nationality]" class="select destination nationality">
                        <option value="">Select your nationality</option>
                      <?php foreach ($data['countries'] as $country) { ?>
                        <option value="<?php echo $country['users_country_id']; ?>"<?php if ($is_nationality_country && $data['destination']['users_nationality_id'] == $country['users_country_id']) echo ' selected="selected"'; ?>><?php echo $country['users_country_name']; ?></option>
                      <?php } ?>
                      </select>
                      <?php } else { ?>
                      There are no countries.
                      <input type="hidden" name="destination[nationality]" value="">
                      <?php } ?>
                      </div>
                      
                      <div class="target countries-groups nationalities_groups<?php if ($is_nationality_country || !isset($data['destination']['users_destination_id'])) echo ' hidden'; ?>">
                      <?php if (isset($data['nationalities_groups']) && $data['nationalities_groups'] !== false) { ?>
                      <select name="destination[nationalities_groups]" class="select destination groups">
                        <option value="">Select your nationality group</option>
                      <?php foreach ($data['nationalities_groups'] as $group) { ?>
                        <option value="<?php echo $group['user_nationality_group_id']; ?>"<?php if ($is_nationality_group && $data['destination']['users_nationality_id'] == $group['user_nationality_group_id']) echo ' selected="selected"'; ?>><?php echo $group['user_nationality_group_name']; ?></option>
                      <?php } ?>
                      </select>
                      <?php } else { ?>
                      There are no groups.
                      <input type="hidden" name="destination[nationalities_groups]" value="">
                      <?php } ?>
                      </div>

                    </td>
                  </tr>
                </table>

              </td>
            </tr>

          </table>

        </div>
      </div>
    </form>
    

    <?php if (isset($data['format_types'])) echo $data['format_types']; ?>

    <div class="visa-types-content"><?php if (isset($data['format_type_items'])) echo $data['format_type_items']; ?></div>

    <br clear="all">
    <br clear="all">

  </div>

</div>


<?php require_once ELEM_DIR . 'admin_footer.php'; ?>