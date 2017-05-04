<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['group'] = $_POST['group'];
  debug($data['group']);
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="country_edit_add">
    
      <input type="hidden" name="group[user_country_group_id]" value="<?=(isset($data['group']['user_country_group_id']) && $data['group']['user_country_group_id'] != '' ? $data['group']['user_country_group_id'] : '');?>">

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
              <input type="hidden" name="group[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>

                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['group']['user_country_group_id']?>/1">
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
              <th>Group name</th>
              <td><input type="text" name="group[user_country_group_name]" value="<?=(isset($data['group']['user_country_group_name']) ? $data['group']['user_country_group_name'] : '');?>"></td>
            </tr>

            <tr>
              <th>Countries list</th>
              <td>
                <?php if (isset($data['countries']) && $data['countries'] !== false) { ?>
                <?php $country_groups_exist = (isset($data['group']['countries']) && $data['group']['countries'] !== false); ?>
                <table class="borderless multiple-select-parent countries-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="countries_list" size="20" class="multiple-select countries_list" multiple>
                    <?php if (!$country_groups_exist) { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                    <?php if (!in_array($country['users_country_id'], $data['group']['countries_selected'])) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    <br clear="all">
                    <a href="#" class="select_all">Add all</a>
                    </td>
                    <td style="width: 50%;">
                    <select name="group[countries][]" size="20" class="multiple-select countries_target" multiple>
                    <?php if ($country_groups_exist) { ?>
                    <?php foreach ($data['group']['countries'] as $users_country_id => $users_country_name) { ?> 
                      <option value="<?php echo $users_country_id; ?>" selected="selected"><?php echo $users_country_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    <br clear="all">
                    <a href="#" class="select_all">Remove all</a>
                    </td>
                  </tr>
                </table>
                <?php } ?>
                <br clear="all">
              </td>
            </tr>

          </table>

        </div>
        
      </div>
  
    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>