<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['delivery'] = $_POST['delivery'];
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="delivery[delivery_method_id]" value="<?=(isset($data['delivery']['delivery_method_id']) && $data['delivery']['delivery_method_id'] != '' ? $data['delivery']['delivery_method_id'] : '');?>">

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
              <input type="hidden" name="delivery[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language) { ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])) { ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['delivery']['delivery_method_id']?>/<?=$language['language_id'];?>">
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

          <h2>Delivery method info</h2>
          
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
              <td><input type="text" name="delivery[delivery_method_name]" value="<?=(isset($data['delivery']['delivery_method_name']) ? $data['delivery']['delivery_method_name'] : '');?>"></td>
            </tr>
            <tr>
              <th>Price</th>
              <td><input type="text" name="delivery[delivery_method_price]" value="<?=(isset($data['delivery']['delivery_method_price']) ? $data['delivery']['delivery_method_price'] : '');?>"></td>
            </tr>
          </table>
        
        </div>
        
      </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>