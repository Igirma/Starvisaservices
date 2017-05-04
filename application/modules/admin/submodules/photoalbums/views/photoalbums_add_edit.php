<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['photoalbums'] = $_POST['photoalbums'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="news_edit_add">
      
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
            <div style="position: absolute; left: 11px; top: 10px;" class="sprite sprite-photoalbums"></div>
            <h1><?=$this->lang->line('header_add');?></h1>
          </div>
        
        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>
        
          <div class="header">
            <input type="hidden" name="photoalbums[language][code]" value="<?=$data['photoalbums']['language']['code']?>" />
            <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['photoalbums']['language']['code']?>"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
            <div class="add_edit_languages">
              
              <?php foreach($data['languages'] as $language): ?>

                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                
                  <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['photoalbums']['album_id']?>/<?=$language['language_id'];?>">
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
        
          <h2><?=$this->lang->line('photoalbum_info');?></h2>
          
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
        
          <input type="hidden" name="photoalbums[album_id]" value="<?=(isset($data['photoalbums']['album_id']) ? $data['photoalbums']['album_id'] : '');?>">

            <table>

              <tr>
                <th><?=$this->lang->line('content_title');?></th>
                <td><input class="text" type="text" name="photoalbums[title]" value="<?=(isset($data['photoalbums']['title']) ? $data['photoalbums']['title'] : '');?>"></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('active');?></th>
                <td>
                  <?php if(isset($data['photoalbums']['sub_active'])): ?>
                  
                    <?php if($data['photoalbums']['sub_active'] == 1): ?>
                    
                      <input type="radio" name="sub_active" value="1" checked="checked"><?=$this->lang->line('yes')?>
                      <input type="radio" name="sub_active" value="0"><?=$this->lang->line('no')?>
                    
                    <?php else: ?>
                    
                      <input type="radio" name="sub_active" value="1"><?=$this->lang->line('yes')?>
                      <input type="radio" name="sub_active" value="0" checked="checked"><?=$this->lang->line('no')?>
                    
                    <?php endif; ?>
                    
                  <?php else: ?>
                  
                    <input type="radio" name="sub_active" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="sub_active" value="0"><?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                </td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('content_description');?></th>
                <td><textarea name="photoalbums[description]"><?=(isset($data['photoalbums']['description']) ? $data['photoalbums']['description'] : '');?></textarea></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('content_text');?></th>
                <td><textarea class="editor" name="photoalbums[content]"><?=(isset($data['photoalbums']['content']) ? $data['photoalbums']['content'] : '');?></textarea></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('meta_title');?></th>
                <td><input class="count[63]" type="text" name="photoalbums[meta_title]" value="<?=(isset($data['photoalbums']['meta_title']) ? $data['photoalbums']['meta_title'] : '');?>"></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('meta_desc');?></th>
                <td><input class="count[156]" type="text" name="photoalbums[meta_desc]" value="<?=(isset($data['photoalbums']['meta_desc']) ? $data['photoalbums']['meta_desc'] : '');?>"></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('meta_keyw');?></th>
                <td><input class="count[256]" type="text" name="photoalbums[meta_keyw]" value="<?=(isset($data['photoalbums']['meta_keyw']) ? $data['photoalbums']['meta_keyw'] : '');?>"></td>
              </tr>
              
              <tr>
                <th><?=$this->lang->line('slug');?></th>
                <td><input type="text" name="photoalbums[slug]" value="<?=(isset($data['photoalbums']['slug']) ? $data['photoalbums']['slug'] : '');?>"></td>
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
        
          <?php if(isset($data['media'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['photoalbums']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
                <input type="hidden" name="media[][media_id]" value="<?=$media['media_id'];?>">
              
                <a href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $media['media_id'] . '/' . $media['album_id'] . '/' . $this->url->segment(4);?>" class="delete">
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                </a>
                
                <div class="set_thumbnail">
                
                  <input type="radio" name="set_thumbnail" value="<?=$media['media_id'];?>" <?=($media['album_thumb'] == 1 ? 'checked="checked"' : '');?>><small> Thumb</small>
                
                </div>
                
                <div class="filename">
                
                  <?=(strlen($media['filename']) > 15 ? substr($media['filename'], 0, 15) . '...' : $media['filename']);?>
                  
                </div>
                
                <div class="order_media">
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['album_id'];?>/<?=$data['photoalbums']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['album_id'];?>/<?=$data['photoalbums']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['photoalbums']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
          
          <a name="media"></a>
        
        </div>
      
      </div>
    
    </form>

  </div>

</div>

<script type="text/javascript">
  MIN_IMG_W =	<?=PHOTOALBUMS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=PHOTOALBUMS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>