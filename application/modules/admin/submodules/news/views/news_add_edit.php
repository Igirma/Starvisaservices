<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  if(haveFilters(CONTROLLER) && $_POST['news']['title'] == ''){
    if($_POST['news']['title'] == ''){
      if(isset($_POST['news']['filters'])) $filters = $data['news']['filters'];
      else $filters = '';
    }
    $data['news'] = $_POST['news'];
    if(isset($_POST['news']['filters'])) $selected_filters = $_POST['news']['filters'];
    else $selected_filters = '';
    $data['news']['filters'] =  $filters;
    $data['news']['filters_post_selected'] = $selected_filters;
  
  }else{
    $data['news'] = $_POST['news'];
  }
  $data['news']['start_date'] = strtotime($_POST['news']['start_date']);
  $data['news']['end_date'] = strtotime($_POST['news']['end_date']);
}
?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="news_edit_add">
    
      <input type="hidden" name="news[news_id]" value="<?=(isset($data['news']['news_id']) ? $data['news']['news_id'] : '');?>">
      
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
              <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-news"></div>
              <h1><?=$this->lang->line('add_news')?></h1>
            
            </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="news[language][code]" value="<?=$data['news']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['news']['language']['code']?>"></div>
              <h1><?=$this->lang->line('edit_news')?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['news']['news_id']?>/<?=$language['language_id'];?>">
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
        
          <h2><?=$this->lang->line('header_info');?></h2>
          
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
              <td><input type="text" name="news[title]" value="<?=(isset($data['news']['title']) ? $data['news']['title'] : '');?>"></td>
            </tr>
            
            <?php if(($this->config->item('max_category_depth')+1) > 1): ?>
            
            <tr class="hidden ">
              
                <th><?=$this->lang->line('category_id')?></th>
                
                <td>
                
                  <div class='padded_content'>
                    <?php foreach($data['drop_down'] as $category): ?>
                        
                      <label><input type='checkbox' name='news[category_id][]' value='<?php echo $category['category_id'];?>' <?=(isset($data['news']['category_id']) && in_array($category['category_id'], $data['news']['category_id']) ? 'checked="checked"' : '');?> onchange="$(this).parents('form').submit();">&nbsp;&nbsp;<?=$category['title']?></label><br>
                            
                      <?php if(($this->config->item('max_category_depth')+1) > 2): ?>
                            
                        <?php foreach($category['children'] as $child): ?>
                                  
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type='checkbox' name='news[category_id][]' <?=(isset($data['news']['category_id']) && in_array($child['category_id'], $data['news']['category_id']) ? 'checked="checked"' : '');?> value="<?=$child['category_id']?>" onchange="$(this).parents('form').submit();">&nbsp;&nbsp;<?=$child['title']?></label><br>
                        <?php endforeach; ?>
                                
                      <?php endif; ?>
                            
                    <?php endforeach; ?>
                  </div>
                  
                </td>
                
              </tr>
            
            <?php else: ?>
            
              <input type="hidden" name="news[category_id]" value="0" >
            
            <?php endif; ?>
            <tr>
              <th><?=$this->lang->line('active');?></th>
              <td>
                <?php if(isset($data['news']['sub_active'])): ?>
                
                  <?php if($data['news']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="news[sub_active]" value="1" checked="checked"> <?=$this->lang->line('yes')?>
                    <input type="radio" name="news[sub_active]" value="0"> <?=$this->lang->line('no')?>
                  
                  <?php else: ?>
                  
                    <input type="radio" name="news[sub_active]" value="1"> <?=$this->lang->line('yes')?>
                    <input type="radio" name="news[sub_active]" value="0" checked="checked"> <?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="news[sub_active]" value="1" checked="checked"> <?=$this->lang->line('yes')?>
                  <input type="radio" name="news[sub_active]" value="0"> <?=$this->lang->line('no')?>
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th>Nieuws pagina</th>
              <td>
                <input type="checkbox" name="news[page_news]" id="p_news" value="1"<?php if(!isset($data['news']['page_news']) || isset($data['news']['page_news']) && $data['news']['page_news'] == 1){ ?> checked="checked"<?php } ?>> <label for="p_news">Acties</label> &nbsp;&nbsp;
                <input type="checkbox" name="news[page_school]" id="p_school" value="1"<?php if(isset($data['news']['page_school']) && $data['news']['page_school'] == 1){ ?> checked="checked"<?php } ?>> <label for="p_school">School</label>
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('start_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="news[start_date]" value="<?=(isset($data['news']['start_date']) ? date('d-m-Y', $data['news']['start_date']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('end_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="news[end_date]" value="<?=(isset($data['news']['end_date']) ? date('d-m-Y', $data['news']['end_date']) : date('d-m-Y', strtotime('+1 month', time())));?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('no_end_date');?></th>
              <td>
                <?php if(isset($data['news']['no_end_date'])): ?>
                
                  <?php if($data['news']['no_end_date'] == 1): ?>
                  
                    <input type="radio" name="news[no_end_date]" value="0"> <?=$this->lang->line('yes')?>
                    <input type="radio" name="news[no_end_date]" value="1" checked="checked"> <?=$this->lang->line('no')?>
                    
                  <?php else: ?>
                  
                    <input type="radio" name="news[no_end_date]" value="0" checked="checked"> <?=$this->lang->line('yes')?>
                    <input type="radio" name="news[no_end_date]" value="1"> <?=$this->lang->line('no')?>
                    
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="news[no_end_date]" value="0"> <?=$this->lang->line('yes')?>
                  <input type="radio" name="news[no_end_date]" value="1" checked="checked"> <?=$this->lang->line('no')?>
                  
                <?php endif; ?>
              </td>
            </tr>
            
            
            <tr>
              <th><?=$this->lang->line('content_description');?></th>
              <td><textarea class="count[120]" name="news[description]"><?=(isset($data['news']['description']) ? $data['news']['description'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_text');?></th>
              <td style="padding-top: 5px; padding-bottom: 5px;"><textarea name="news[content]" class="editor"><?=(isset($data['news']['content']) ? $data['news']['content'] : '');?></textarea></td>
            </tr>
            
            <tr class="hidden">
              <th>Tekst onder de foto</th>
              <td style="padding-top: 5px; padding-bottom: 5px;"><textarea name="news[extra_content]" class="editor"><?=(isset($data['news']['extra_content']) ? $data['news']['extra_content'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="news[meta_title]" value="<?=(isset($data['news']['meta_title']) ? $data['news']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('meta_desc');?></th>
              <td><input class="count[156]" type="text" name="news[meta_desc]" value="<?=(isset($data['news']['meta_desc']) ? $data['news']['meta_desc'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="news[meta_keyw]" value="<?=(isset($data['news']['meta_keyw']) ? $data['news']['meta_keyw'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="news[slug]" value="<?=(isset($data['news']['slug']) ? $data['news']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>
      
      <?php
      if(haveFilters(CONTROLLER))
      if(isset($data['news']['filters']) && $data['news']['filters'] && count($data['news']['filters']) > 0 
        && ((isset($data['news']['category_id']) && haveCategories(CONTROLLER)) || !haveCategories(CONTROLLER)))
      {
        ?>
        <div class="column">
        
          <div class="subheader">
          
            <h2><?=$this->lang->line('filters')?></h2>
            
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
              
              <?php if(haveCategories(CONTROLLER)): 
              //debug($data['news']['filters']);
              ?>
                <?php foreach($data['news']['filters'] as $filters): ?>	
                
                <?php 
                if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                
                  <tr>
                  
                    <th><?php echo $filters['title'];?></th>
                    
                    <td> 
                    
                      <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                        
                        <?php $x = 1; ?>
                        
                        <?php foreach($filters['subelements'] as $filters_element): ?>
                          
                          <label><input type='checkbox' name="news[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['news']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['news']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                          
                            <?php echo $filters_element['filter_item_title'];?></label><br>
                          
                          <?php $x++; ?>
                          
                        <?php endforeach; ?>
                        
                      <?php endif; ?>		
                      
                    </td>
                    
                  </tr>	
                  
                <?php endif; ?>			
                
              <?php endforeach; ?>
              
              <?php else: ?>		
                  
                <?php foreach($data['news']['filters'] as $filters): ?>	
                
                    <?php 
                    if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                    
                      <tr>
                      
                        <th><?php echo $filters['title'];?></th>
                        
                        <td> 
                        
                          <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                            
                            <?php $x = 1; ?>
                            
                            <?php foreach($filters['subelements'] as $filters_element): ?>
                              
                              <label><input type='checkbox' name="news[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['news']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['news']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                              
                                <?php echo $filters_element['filter_item_title'];?></label><br>
                              
                              <?php $x++; ?>
                              
                            <?php endforeach; ?>
                            
                          <?php endif; ?>		
                          
                        </td>
                        
                      </tr>	
                      
                    <?php endif; ?>			
                    
                  <?php endforeach; ?>
                <?php endif; ?>			
                    
            </table>
          
          </div>
          
        </div>
        
        <?php
      }
      ?>
      
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
        
          <?php if(!empty($data['media']['photos']) && count($data['media']['photos']) > 0): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['photos'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['news']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['news']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['news']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['news']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                <textarea maxlength="110" name="twitter_post_text"><?=(isset($data['news']['description']) ? str_shorten($data['news']['description'], 110) : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="twitter_post_link" value="<?=(isset($data['news']['slug']) ? $this->googleurl->makeRequest($data['news']['slug']) : '');?>">
                <br>
                <input style="margin-bottom: 10px;" type="submit" value="<?=$this->lang->line('social_media_btn')?>">
              </td>
            </tr>
            
            </tr>

          </table>
        
        </div>
        
      </div>
      
    </form>
    
    <form action="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/postLinkedin/<?=(isset($data['news']['news_id'])?$data['news']['news_id']:0)?>/<?=$this->url->segment(4);?>/<?php echo end($this->url->segments);?>" method="post">
        
      <a href='' id='linkedin'></a>
        <div class="column hidden">
          
          <div class="subheader">
          
            <h2><?=$this->lang->line('post_linkedin')?></h2>
            
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
                  <img src="<?=SITE_URL . ELEM_DIR?>img_admin/linkedin_50.png">
                </th>
                <td>
                  <textarea name="linkedin_post_text"><?=(isset($data['news']['description']) ? $data['news']['description'] : '');?></textarea>
                  <input style="margin-bottom: 10px;" type="text" readonly name="linkedin_post_link" value="<?=(isset($data['news']['slug']) ? $this->googleurl->makeRequest($data['news']['slug']) : '');?>">
                  <br>
                  <input style="margin-bottom: 10px;" type="submit" value="<?=$this->lang->line('social_media_btn')?>">
                </td>
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
                <textarea name="facebook_post_text"><?=(isset($data['news']['description']) ? $data['news']['description'] : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="facebook_post_link" value="<?=(isset($data['news']['slug']) ? $this->googleurl->makeRequest($data['news']['slug']) : '');?>">
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
  MIN_IMG_W =	<?=NEWS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=NEWS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>