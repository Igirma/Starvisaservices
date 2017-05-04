<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['holiday'] = $_POST['holiday'];
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="holiday_edit_add">
    
      <input type="hidden" name="holiday[holiday_id]" value="<?=(isset($data['holiday']['holiday_id']) && $data['holiday']['holiday_id'] != '' ? $data['holiday']['holiday_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
            <h1>Add holiday</h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1>Edit holiday</h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['holiday']['holiday_id']?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>

                <?php endforeach; ?>
                
              </div>
              
            </div>

        <?php endif; ?>
      
      </div>
      
      <div class="column">
      
        <div class="subheader">

          <h2>Holiday information</h2>
          
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
              <th>Holiday name</th>
              <td><input type="text" name="holiday[holiday_name]" value="<?=(isset($data['holiday']['holiday_name']) ? $data['holiday']['holiday_name'] : '');?>"></td>
            </tr>

            <tr>
              <th>Country</th>
              <td>
                <select name="holiday[holiday_country]">
                <?php foreach ($data['countries'] as $country) { ?>
                  <option value="<?php echo $country['name']; ?>"<?php if (isset($data['holiday']['holiday_country']) && $data['holiday']['holiday_country'] == $country['name']) echo ' selected="selected"'; ?>><?php echo $country['name']; ?></option>
                <?php } ?>
                </select>
              </td>
            </tr>
            
            <tr>
              <th>Day / Month</th>
              <td>
              
                <select name="holiday[holiday_day]">
                <?php for ($i = 1; $i <= 31; $i++) { ?>
                  <option value="<?php echo $i; ?>"<?php if (isset($data['holiday']['holiday_day']) && $data['holiday']['holiday_day'] == $i) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
                <?php } ?>
                </select>
                
                <select name="holiday[holiday_month]">
                <?php for ($i = 1; $i <= 12; $i++) { ?>
                  <option value="<?php echo $i; ?>"<?php if (isset($data['holiday']['holiday_month']) && $data['holiday']['holiday_month'] == $i) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
                <?php } ?>
                </select>
              
              </td>
            </tr>

          </table>
        
        </div>
        
      </div>
  
    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>