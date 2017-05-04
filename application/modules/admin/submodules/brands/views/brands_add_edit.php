<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  if(haveFilters(CONTROLLER) && $_POST['brand']['title'] == ''){
    if($_POST['brand']['title'] == ''){
      if(isset($_POST['brand']['filters'])) $filters = $data['brand']['filters'];
      else $filters = '';
    }
    $data['brand'] = $_POST['brand'];
    if(isset($_POST['brand']['filters'])) $selected_filters = $_POST['brand']['filters'];
    else $selected_filters = '';
    $data['brand']['filters'] =  $filters;
    $data['brand']['filters_post_selected'] = $selected_filters;
  
  }else{
    $data['brand'] = $_POST['brand'];
  }
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="brand[brand_id]" value="<?=(isset($data['brand']['brand_id']) && $data['brand']['brand_id'] != '' ? $data['brand']['brand_id'] : '');?>">

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
              <input type="hidden" name="brand[language][code]" value="<?=$data['brand']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['brand']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language) { ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])) { ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['brand']['brand_id']?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('brand_info');?></h2>

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
              <td><input type="text" name="brand[title]" value="<?=(isset($data['brand']['title']) ? $data['brand']['title'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['brand']['sub_active'])){ ?>
                
                  <?php if($data['brand']['sub_active'] == 1){ ?>
                  
                    <input type="radio" name="brand[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="brand[sub_active]" value="0"><?=$this->lang->line('no')?>
                  
                  <?php } else { ?>
                  
                    <input type="radio" name="brand[sub_active]" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="brand[sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
                  
                  <?php } ?>
                  
                <?php }else{ ?>
                
                  <input type="radio" name="brand[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                  <input type="radio" name="brand[sub_active]" value="0"><?=$this->lang->line('no')?>
                
                <?php } ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th>Geslacht</th>
              <td style="padding-top: 10px; padding-bottom: 10px;">
                <label><input type='checkbox' name='brand[gender_id][]' value='2' <?=(isset($data['brand']['gender_id']) && is_array($data['brand']['gender_id']) && in_array('2', $data['brand']['gender_id']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;Heren</label><br>
                <label><input type='checkbox' name='brand[gender_id][]' value='1' <?=(isset($data['brand']['gender_id']) && is_array($data['brand']['gender_id']) && in_array('1', $data['brand']['gender_id']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;Dames</label><br>
              </td>
            </tr>
            
<?php if (isset($data['stores'])) { ?>
            <tr class="hidden">
              <th>Winkels</th>
              <td style="padding-top: 10px; padding-bottom: 10px;">
<?php foreach ($data['stores'] as $store) { ?>
                <label><input type='checkbox' name='brand[store_id][]' value='<?php echo $store['store_id'];?>' <?=(isset($data['brand']['store_id']) && is_array($data['brand']['store_id']) && in_array($store['store_id'], $data['brand']['store_id']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;<?=$store['title']?></label><br>
<?php } ?>
              </td>
            </tr>
<?php } ?>
            
            <tr>
              <th>Website</th>
              <td><input type="text" name="brand[description]" value="<?=(isset($data['brand']['description']) ? $data['brand']['description'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('content_text');?></th>
              <td><textarea name="brand[content]" class="editor"><?=(isset($data['brand']['content']) ? $data['brand']['content'] : '');?></textarea></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="brand[meta_title]" value="<?=(isset($data['brand']['meta_title']) ? $data['brand']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_desc');?></th>
              <td><input class="count[156]" type="text" name="brand[meta_desc]" value="<?=(isset($data['brand']['meta_desc']) ? $data['brand']['meta_desc'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="brand[meta_keyw]" value="<?=(isset($data['brand']['meta_keyw']) ? $data['brand']['meta_keyw'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="brand[slug]" value="<?=(isset($data['brand']['slug']) ? $data['brand']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>

      <div class="column hidden">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Logo merk</h2>
          
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
        
          <?php if(!empty($data['brand']['media']['logo'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['brand']['media']['logo'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="logo[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
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

          <?php if(!empty($data['brand']['media']['photos'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['brand']['media']['photos'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
    
    <form action="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/postTwitter" method="post">
      
      <div class="column hidden">
        
        <div class="subheader">
        
          <h2><?=$this->lang->line('post_twitter')?></h2>
          
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
                <a class="toggle plus">
                  <div class="sprite sprite-plus"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
        
        </div>
        
        <div class="subcolumn">
        
          <table>
            
            <!-- Twitter post block -->

            <tr>
              <th style="text-align: center;">
                <img src="<?=SITE_URL . ELEM_DIR?>img_admin/twitter_50.png">
              </th>
              <td>
                <textarea maxlength="110" name="twitter_post_text"><?=(isset($data['brand']['description']) ? str_shorten($data['brand']['description'], 110) : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="twitter_post_link" value="<?=(isset($data['brand']['slug']) ? $this->googleurl->makeRequest($data['brand']['slug']) : '');?>">
                <br>
                <input style="margin-bottom: 10px;" type="submit" value="<?=$this->lang->line('social_media_btn')?>">
              </td>
            </tr>
            
            </tr>

          </table>
        
        </div>
        
      </div>
      
    </form>
    
    <form action="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/postFacebook" method="post">
      
      <div class="column hidden">
        
        <div class="subheader">
        
          <h2><?=$this->lang->line('post_facebook')?></h2>
          
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
                <a class="toggle plus">
                  <div class="sprite sprite-plus"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
        
        </div>
        
        <div class="subcolumn">
        
          <table>
            
            <!-- Facebook post block -->
            
            </tr>
          
            <tr>
              <th style="text-align: center;">
                <img src="<?=SITE_URL . ELEM_DIR?>img_admin/facebook_50.png">
              </th>
              <td>
                <textarea name="facebook_post_text"><?=(isset($data['brand']['description']) ? $data['brand']['description'] : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="facebook_post_link" value="<?=(isset($data['brand']['slug']) ? $this->googleurl->makeRequest($data['brand']['slug']) : '');?>">
                <br>
                <input style="margin-bottom: 10px;" type="submit" value="<?=$this->lang->line('social_media_btn')?>">
              </td>
            </tr>

          </table>
        
        </div>
        
      </div>
      
    </form>
  
  </div>

</div>

<script type="text/javascript">
  MIN_IMG_W = <?=BRANDS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=BRANDS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>