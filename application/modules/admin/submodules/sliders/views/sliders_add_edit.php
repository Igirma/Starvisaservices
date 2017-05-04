<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['slider'] = $_POST['slider'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-sliders"></div>
            <h1><?=$this->lang->line('header_add');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="slider[language][code]" value="<?=$data['slider']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['slider']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_edit');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['slider']['slider_id']?>/<?=$language['language_id'];?>">
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
        
          <h2>Slide information</h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="Info">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
        
        </div>
        
        <div class="subcolumn">
        
          <table>

            <tr>
              <th><?=$this->lang->line('content_title');?></th>
              <td><input type="text" name="slider[title]" value="<?=(isset($data['slider']['title']) ? $data['slider']['title'] : '');?>"></td>
            </tr>

            <tr>
              <th>Subtitle</th>
              <td><input type="text" name="slider[description]" value="<?=(isset($data['slider']['description']) ? $data['slider']['description'] : '');?>"></td>
            </tr>

            <tr>
              <th><?=$this->lang->line('active');?></th>
              <td>
                <?php if(isset($data['slider']['sub_active'])): ?>
                
                  <?php if($data['slider']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="sub_active" value="1" checked="checked"> <?=$this->lang->line('yes')?>&nbsp;&nbsp;
                    <input type="radio" name="sub_active" value="0"> <?=$this->lang->line('no')?>
                  
                  <?php else: ?>
                  
                    <input type="radio" name="sub_active" value="1"> <?=$this->lang->line('yes')?>&nbsp;&nbsp;
                    <input type="radio" name="sub_active" value="0" checked="checked"> <?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="sub_active" value="1" checked="checked"> <?=$this->lang->line('yes')?>&nbsp;&nbsp;
                  <input type="radio" name="sub_active" value="0"> <?=$this->lang->line('no')?>
                
                <?php endif; ?>
              </td>
            </tr>
            
            
            <?php if (isset($data['drop_down']) && !empty($data['drop_down']) && count($data['drop_down']) > 0): ?>
            
            <tr class="hidden">
            
              <th>Pagina's</th>
              
              <td>
              <br>

              <?php foreach($data['drop_down'] as $page): ?>

              <label><input type='checkbox' name='slider[page_id][]' value='<?php echo $page['page_id'];?>' <?=(isset($data['pages_selected']) && in_array($page['page_id'], $data['pages_selected']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;<?=$page['menu_title']?></label><br>

              <?php if (isset($page['children']) && !empty($page['children']) && count($page['children']) > 0): ?>

                <?php foreach ($page['children'] as $child): ?>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type='checkbox' name='slider[page_id][]' <?=(isset($data['pages_selected']) && in_array($child['page_id'], $data['pages_selected']) ? 'checked="checked"' : '');?> value="<?=$child['page_id']?>">&nbsp;&nbsp;<?=$child['menu_title']?></label><br>
                <?php endforeach; ?>

              <?php endif; ?>
                
              <?php endforeach; ?>

              <br>
              </td>
              
            </tr>
          
            <?php endif; ?>

            <tr class="hidden">
              <th><?=$this->lang->line('content_text');?></th>
              <td><textarea name="slider[content]" class="editor"><?=(isset($data['slider']['content']) ? $data['slider']['content'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('button_text');?></th>
              <td><input type="text" name="slider[button_text]" value="<?=(isset($data['slider']['button_text']) ? $data['slider']['button_text'] : '');?>"></td>
            </tr>
          
            <?php if (isset($data['drop_down']) && !empty($data['drop_down']) && count($data['drop_down']) > 0): ?>
            
            <tr>

              <th><?=$this->lang->line('button_url');?></th>
              <td>

              <input type="text" name="slider[button_url]" value="<?=(isset($data['slider']['button_url']) ? $data['slider']['button_url'] : '');?>" style="width: 40%;">
              
              &nbsp;&nbsp;<span>or link to page</span>&nbsp;&nbsp;
              
              <select name="slider[button_page_id]" style="height: 26px; padding-left: 8px;">
              <option value="0">---</option>
              <?php foreach($data['drop_down'] as $page): ?>
              <option value="<?php echo $page['page_id']; ?>"<?php echo (isset($data['slider']['button_page_id']) && $data['slider']['button_page_id'] == $page['page_id'] ? ' selected="selected"' : ''); ?>><?=$page['menu_title']?></option>
              <?php if (isset($page['children']) && !empty($page['children']) && count($page['children']) > 0) { ?>
                  <?php foreach($page['children'] as $children) { ?>
                      <option value="<?php echo $children['page_id']; ?>"<?php echo (isset($data['slider']['button_page_id']) && $data['slider']['button_page_id'] == $children['page_id'] ? ' selected="selected"' : ''); ?>>- <?=$children['menu_title']?></option>
                      <?php if (isset($children['children']) && !empty($children['children']) && count($children['children']) > 0) { ?>
                      <?php foreach($children['children'] as $child) { ?>
                        <option value="<?php echo $child['page_id']; ?>"<?php echo (isset($data['slider']['button_page_id']) && $data['slider']['button_page_id'] == $child['page_id'] ? ' selected="selected"' : ''); ?>>- - <?=$child['menu_title']?></option>
                      <?php } ?>
                      <?php } ?>
                  <?php } ?>
              <?php } ?>
              <?php endforeach; ?>
              </select>

              </td>
              
            </tr>
            
            <?php else: ?>
            
            <tr class="hidden">
              <th><?=$this->lang->line('button_url');?></th>
              <td><input type="text" name="slider[button_url]" value="<?=(isset($data['slider']['button_url']) ? $data['slider']['button_url'] : '');?>"></td>
            </tr>
          
            <?php endif; ?>

          </table>
        
        </div>
        
      </div>
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2><?=$this->lang->line('images');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="Info">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
      
        </div>
        
        <div class="subcolumn">
        
          <?php if(isset($data['media'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['slider']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
                <input type="hidden" name="media[][media_id]" value="<?=$media['media_id'];?>">
              
                <a href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $media['media_id'] . '/' . $media['table_id'] . '/' . $this->url->segment(4);?>" class="delete">
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                </a>
                
                <div class="set_thumbnail">
                
                  <input type="radio" name="set_thumbnail" value="<?=$media['media_id'];?>" <?=($media['album_thumb'] == 1 ? 'checked="checked"' : '');?>><small> <?=$this->lang->line('slider_active');?></small>
                
                </div>
                
                <div class="filename">
                
                  <?=(strlen($media['filename']) > 15 ? substr($media['filename'], 0, 15) . '...' : $media['filename']);?>
                  
                </div>
                
                <div class="order_media">
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['slider']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['slider']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['slider']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
                </div>

              </div>
              
              <?php $x++; ?>
            
            <?php endforeach; ?>
          
          <?php endif; ?>
          
          <div class="clear"></div>
        
          <table>
            
            <tr>
            
              <th>
                <?=$this->lang->line('new_photo');?>
              </th>
              <td>
              
                <div class="file_container">
                  <input type="text" readonly="readonly" class="file_replace_text">
                  <input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
                  <input type="submit" name="upload" value="<?=$this->lang->line('upload');?>">
                  <input type="file" name="photo[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
    </form>
  
  </div>

</div>

<script type="text/javascript">
  MIN_IMG_W =	<?=SLIDERS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=SLIDERS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>