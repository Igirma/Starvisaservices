<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['course'] = $_POST['course'];
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="course[course_id]" value="<?=(isset($data['course']['course_id']) && $data['course']['course_id'] != '' ? $data['course']['course_id'] : '');?>">

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
              <input type="hidden" name="course[language][code]" value="<?=$data['course']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['course']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language) { ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])) { ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['course']['course_id']?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('course_info');?></h2>
          
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
              <th><?=$this->lang->line('course_title');?></th>
              <td><input type="text" name="course[title]" value="<?=(isset($data['course']['title']) ? $data['course']['title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('course_function');?></th>
              <td><input type="text" name="course[subtitle]" value="<?=(isset($data['course']['subtitle']) ? $data['course']['subtitle'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('course_gender');?></th>
              <td>
                <?php if(isset($data['course']['gender'])){ ?>
                
                  <?php if($data['course']['gender'] == 'm'){ ?>
                  
                    <input type="radio" name="course[gender]" value="m" checked="checked"> <?=$this->lang->line('course_gender_m');?>
                    <input type="radio" name="course[gender]" value="f"> <?=$this->lang->line('course_gender_w');?>
                  
                  <?php } else { ?>
                  
                    <input type="radio" name="course[gender]" value="m"> <?=$this->lang->line('course_gender_m');?>
                    <input type="radio" name="course[gender]" value="f" checked="checked"> <?=$this->lang->line('course_gender_w');?>
                  
                  <?php } ?>
                  
                <?php }else{ ?>
                
                  <input type="radio" name="course[gender]" value="m" checked="checked"> <?=$this->lang->line('course_gender_m');?>
                  <input type="radio" name="course[gender]" value="f"> <?=$this->lang->line('course_gender_w');?>
                
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['course']['sub_active'])){ ?>
                
                  <?php if($data['course']['sub_active'] == 1){ ?>
                  
                    <input type="radio" name="course[sub_active]" value="1" checked="checked">Yes
                    <input type="radio" name="course[sub_active]" value="0">No
                  
                  <?php } else { ?>
                  
                    <input type="radio" name="course[sub_active]" value="1">Yes
                    <input type="radio" name="course[sub_active]" value="0" checked="checked">No
                  
                  <?php } ?>
                  
                <?php }else{ ?>
                
                  <input type="radio" name="course[sub_active]" value="1" checked="checked">Yes
                  <input type="radio" name="course[sub_active]" value="0">No
                
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_description');?></th>
              <td><textarea class="count[1500]" name="course[description]"><?=(isset($data['course']['description']) ? $data['course']['description'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th>Content</th>
              <td><textarea class="editor" name="course[content_left]"><?=(isset($data['course']['content_left']) ? $data['course']['content_left'] : '');?></textarea></td>
            </tr>
            
            <tr class="hidden">
              <th>Detail page content</th>
              <td><textarea class="editor" name="course[content_right]"><?=(isset($data['course']['content_right']) ? $data['course']['content_right'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="course[slug]" value="<?=(isset($data['course']['slug']) ? $data['course']['slug'] : '');?>"></td>
            </tr>
          
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
        
          <?php if(!empty($data['course']['media'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['course']['media'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['course']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['course']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['course']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['course']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
  MIN_IMG_W = <?=COURSES_CROP_MAX_W;?>;
  MIN_IMG_H = <?=COURSES_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>