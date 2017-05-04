<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['price'] = $_POST['price'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="country_edit_add" onSubmit="$('#togglefield').removeAttr('disabled')">
    
      <input type="hidden" name="price[users_price_id]" value="<?=(isset($data['price']['users_price_id']) && $data['price']['users_price_id'] != '' ? $data['price']['users_price_id'] : '');?>">

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
              <input type="hidden" name="price[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>

                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['price']['users_price_id']?>/1">
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

          <h2>Price information</h2>
          
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
              <th>Price type name</th>
              <td><input type="text" id="togglefield" name="price[users_price_name]" value="<?=(isset($data['price']['users_price_name']) ? $data['price']['users_price_name'] : '');?>" <?=($data['price']['users_price_order'] == 999 ? 'disabled' : '');?>></td>
            </tr>

            <tr>
              <th>Price description</th>
              <td><input type="text" name="price[users_price_description]" value="<?=(isset($data['price']['users_price_description']) ? $data['price']['users_price_description'] : '');?>"></td>
            </tr>
            
			<?php if($data['price']['users_price_order'] != 999): ?>
            <tr>
              <th>Price VAT?</th>
              <td><input type="checkbox" name="price[users_price_vat]" value="1"<?=(isset($data['price']['users_price_vat']) && $data['price']['users_price_vat'] == 1 ? ' checked="checked"' : '');?>></td>
            </tr>
			<?php endif; ?>
			
          </table>

        </div>
        
      </div>
  
    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>