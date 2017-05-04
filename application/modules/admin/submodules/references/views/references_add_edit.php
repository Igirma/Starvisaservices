<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['reference'] = $_POST['reference'];
  $data['reference']['reference_date'] = strtotime($_POST['reference']['reference_date']);
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="references_edit_add">
    
      <input type="hidden" name="reference[reference_id]" value="<?=(isset($data['reference']['reference_id']) && $data['reference']['reference_id'] != '' ? $data['reference']['reference_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-references"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="reference[language][code]" value="<?=$data['reference']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['reference']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['reference']['reference_id']?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('reference_info');?></h2>
          
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
              <td><input type="text" name="reference[title]" value="<?=(isset($data['reference']['title']) ? $data['reference']['title'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['reference']['sub_active'])): ?>
                
                  <?php if($data['reference']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="reference[sub_active]" value="1" checked="checked">Da
                    <input type="radio" name="reference[sub_active]" value="0">Nu
                  
                  <?php else: ?>
                  
                    <input type="radio" name="reference[sub_active]" value="1">Da
                    <input type="radio" name="reference[sub_active]" value="0" checked="checked">Nu
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="reference[sub_active]" value="1" checked="checked">Da
                  <input type="radio" name="reference[sub_active]" value="0">Nu
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('highlight');?></th>
              <td>
                <?php if(isset($data['reference']['highlight'])): ?>
                
                  <?php if($data['reference']['highlight'] == 1): ?>
                  
                    <input type="radio" name="reference[highlight]" value="1" checked="checked">Da
                    <input type="radio" name="reference[highlight]" value="0">Nu
                  
                  <?php else: ?>
                  
                    <input type="radio" name="reference[highlight]" value="1">Da
                    <input type="radio" name="reference[highlight]" value="0" checked="checked">Nu
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="reference[highlight]" value="1" checked="checked">Da
                  <input type="radio" name="reference[highlight]" value="0">Nu
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('reference_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="reference[reference_date]" value="<?=(isset($data['reference']['reference_date']) ? date('d-m-Y', $data['reference']['reference_date']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr>
              <th>Cont utilizator</th>
              <td><input type="text" name="reference[description]" value="<?=(isset($data['reference']['description']) ? $data['reference']['description'] : '');?>"></td>
            </tr>
            
            <tr>
              <th>Adresa web</th>
              <td><input type="text" name="reference[meta_desc]" value="<?=(isset($data['reference']['meta_desc']) ? $data['reference']['meta_desc'] : '');?>"></td>
            </tr>

<?php if (isset($data['blog_data']) && $data['blog_data'] !== false) { ?>
            <tr>
              <th>Postare blog</th>
              <td>
                <select name="reference[blog_id]">
<?php foreach ($data['blog_data'] as $blog) { ?>
                  <option value="<?php echo $blog['blog_id']; ?>"><?php echo $blog['title']; ?></option>
<?php } ?>
                </select>
              </td>
            </tr>
<?php } else { ?>
            <tr class="hidden">
              <th>Postare blog</th>
              <td>
                <input type="hidden" name="reference[blog_id]" value="0">
              </td>
            </tr>
<?php } ?>

            <tr>
              <th><?=$this->lang->line('content_text');?></th>
              <td><textarea name="reference[content]" class="editor"><?=(isset($data['reference']['content']) ? $data['reference']['content'] : '');?></textarea><br></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="reference[meta_title]" value="<?=(isset($data['reference']['meta_title']) ? $data['reference']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="reference[meta_keyw]" value="<?=(isset($data['reference']['meta_keyw']) ? $data['reference']['meta_keyw'] : '');?>"></td>
            </tr>

            <tr class="hidden">
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="reference[slug]" value="<?=(isset($data['reference']['slug']) ? $data['reference']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>

      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Fotografie</h2>
          
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
        
          <?php if(!empty($data['media'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['reference']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
                <input type="hidden" name="media[][media_id]" value="<?=$media['media_id'];?>">
              
                <a href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $media['media_id'] . '/' . $media['table_id'] . '/' . $this->url->segment(4);?>" class="delete">
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                </a>
                
                <div class="set_thumbnail">
                
                  <input type="radio" name="set_thumbnail" value="<?=$media['media_id'];?>" <?=($media['album_thumb'] == 1 ? 'checked="checked"' : '');?>><small> Thumb</small>
                
                </div>
                
                <div class="filename">
                
                  <?=(strlen($media['filename']) > 15 ? substr($media['filename'], 0, 15) . '...' : $media['filename']);?>
                  
                </div>
                
                <div class="order_media">
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['reference']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['reference']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['reference']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="photo[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      <div class="column hidden">
      
        <div class="subheader">

          <h2><?=$this->lang->line('documents');?></h2>
          
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
        
          <?php if(!empty($data['docs'])): ?>
          
            <div class="docs">
            
              <ul>

            <?php foreach($data['docs'] as $doc): ?>
            
              <?php $ext = end(explode('.', $doc['filename'])); ?>
              
                <li>	
                  <a href="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER;?>/docs/<?=$doc['filename'];?>"><?=str_shorten($doc['filename'], 15);?>
                    <div style="position: absolute; top: 0; left: 0;" class="sprite_ex sprite-<?=$ext;?>"></div>
                  </a>
                  <a class="delete" style="display: none; position: absolute; right: 3px; top: 7px;" href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $doc['media_id'] . '/' . $doc['table_id'] . '/' . $this->url->segment(4);?>">
                    <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                  </a>
                </li>

            <?php endforeach; ?>

              </ul>						
            
            </div>
            
          <?php endif; ?>
        
          <table>
            
            <tr>
            
              <th>
                <?=$this->lang->line('new_document');?>
              </th>
              <td>
              
                <div class="file_container">
                  <input type="text" readonly="readonly" class="file_replace_text">
                  <input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
                  <input type="submit" name="save" value="<?=$this->lang->line('upload');?>">
                  <input type="file" name="docs[]" multiple="multiple" class="file_hack">
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
  MIN_IMG_W =	<?=REFERENCES_CROP_MAX_W;?>;
  MIN_IMG_H = <?=REFERENCES_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>