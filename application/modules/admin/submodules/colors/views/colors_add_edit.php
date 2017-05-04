<?php
require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['colors'] = $_POST['colors'];
}

?>

<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="colors_edit_add">
    
      <input type="hidden" name="colors[colors_id]" value="<?=(isset($data['colors']['colors_id']) && $data['colors']['colors_id'] != '' ? $data['colors']['colors_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-colors"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-colors"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['colors']['colors_id'];?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('colors_info');?></h2>
          
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
              <th><?=$this->lang->line('content_title');?></th>
              <td><input type="text" name="colors[title]" value="<?=(isset($data['colors']['title']) ? $data['colors']['title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['colors']['sub_active'])): ?>
                
                  <?php if($data['colors']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="colors[sub_active]" value="1" checked="checked">Ja
                    <input type="radio" name="colors[sub_active]" value="0">Nee
                  
                  <?php else: ?>
                  
                    <input type="radio" name="colors[sub_active]" value="1">Ja
                    <input type="radio" name="colors[sub_active]" value="0" checked="checked">Nee
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="colors[sub_active]" value="1" checked="checked">Ja
                  <input type="radio" name="colors[sub_active]" value="0">Nee
                
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <th><?=$this->lang->line('color');?></th>
              <td>
                <input class="colorSelector" type="hidden" name="colors[color]" value="<?=(isset($data['colors']['color']) ? $data['colors']['color'] : '');?>">
                <div id='colorSelector'><div style='background-color:#<?=(isset($data['colors']['color']) ? $data['colors']['color'] : '');?>;'></div></div>		
              </td>
            </tr>

          </table>

        </div>

      </div>

    </form>

  </div>

</div>
<?php require_once ELEM_DIR . 'admin_footer.php'; ?>