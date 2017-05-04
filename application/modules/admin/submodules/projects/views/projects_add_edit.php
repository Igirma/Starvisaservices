<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  if(haveFilters(CONTROLLER) && $_POST['project']['title'] == ''){
    if($_POST['project']['title'] == ''){
      if(isset($_POST['project']['filters'])) $filters = $data['project']['filters'];
      else $filters = '';
    }
    $data['project'] = $_POST['project'];
    if(isset($_POST['project']['filters'])) $selected_filters = $_POST['project']['filters'];
    else $selected_filters = '';
    $data['project']['filters'] =  $filters;
    $data['project']['filters_post_selected'] = $selected_filters;
  
  }else{
    $data['project'] = $_POST['project'];
  }
  $data['project']['project_date'] = strtotime($_POST['project']['project_date']);
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="project[project_id]" value="<?=(isset($data['project']['project_id']) && $data['project']['project_id'] != '' ? $data['project']['project_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-projects"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="project[language][code]" value="<?=$data['project']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['project']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['project']['project_id']?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('project_info');?></h2>
          
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
              <td><input type="text" name="project[title]" value="<?=(isset($data['project']['title']) ? $data['project']['title'] : '');?>"></td>
            </tr>

            <tr>
              <th>Subtitlu</th>
              <td><input type="text" name="project[description]" value="<?=(isset($data['project']['description']) ? htmlentities($data['project']['description']) : '');?>"></td>
            </tr>
            
            <tr>
              <th>Descriere</th>
              <td><textarea name="project[summary]"><?=(isset($data['project']['summary']) ? htmlentities($data['project']['summary']) : '');?></textarea></td>
            </tr>
            
            <tr>
              <th>Website</th>
              <td><input type="text" name="project[website]" value="<?=(isset($data['project']['website']) ? $data['project']['website'] : '');?>"></td>
            </tr>

            <?php if (isset($data['partners']) && count($data['partners']) > 0) { ?>

            <tr>

                <th>Parteneri</th>

                <td>
                  <?php foreach ($data['partners'] as $k => $partner) { ?>
                  <label><input type="checkbox" name="project[partners][]" value="<?php echo $partner['brand_id']; ?>"<?php if (isset($data['partners_selected']) && @in_array($partner['brand_id'], $data['partners_selected'])) echo ' checked="checked"'; ?>> <?php echo $partner['title']; ?></label><br>
                  <?php } ?>
                </td>

            </tr>

            <?php } else { ?>
              <input type="hidden" name="project[partners]" value="0">
            <?php } ?>


            <?php if(($this->config->item('max_category_depth')+1) > 1): ?>
            
            <tr class="hidden">
              
                <th><?=$this->lang->line('category_id')?></th>
                
                <td>
<?php if (isset($data['categories']) && $data['categories'] !== false) { ?>
                  <select name="project[category_id]">
                  <?php foreach ($data['categories'] as $category_id => $title) { ?>
                    <option value="<?php echo $category_id; ?>"<?php if (isset($data['project']['category_id']) && $data['project']['category_id'] == $category_id) echo ' selected="selected"'; ?>><?php echo $title; ?></option>
                  <?php } ?>
                  </select>
<?php } ?>
                </td>
                
            </tr>
            
            <?php else: ?>
            
              <input type="hidden" name="project[category_id]" value="0">
            
            <?php endif; ?>
            
            
            
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['project']['sub_active'])): ?>
                
                  <?php if($data['project']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="project[sub_active]" value="1" checked="checked"> Da
                    <input type="radio" name="project[sub_active]" value="0"> Nu
                  
                  <?php else: ?>
                  
                    <input type="radio" name="project[sub_active]" value="1"> Da
                    <input type="radio" name="project[sub_active]" value="0" checked="checked"> Nu
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="project[sub_active]" value="1" checked="checked"> Da
                  <input type="radio" name="project[sub_active]" value="0"> Nu
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('highlight');?></th>
              <td>
                <?php if(isset($data['project']['highlight'])): ?>
                
                  <?php if($data['project']['highlight'] == 1): ?>
                  
                    <input type="radio" name="project[highlight]" value="1" checked="checked">Da
                    <input type="radio" name="project[highlight]" value="0">Nu
                  
                  <?php else: ?>
                  
                    <input type="radio" name="project[highlight]" value="1">Da
                    <input type="radio" name="project[highlight]" value="0" checked="checked">Nu
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="project[highlight]" value="1" checked="checked">Da
                  <input type="radio" name="project[highlight]" value="0">Nu
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('project_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="project[project_date]" value="<?=(isset($data['project']['project_date']) ? date('d-m-Y', $data['project']['project_date']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr>
              <th>Text con&#355;inut 1</th>
              <td><textarea name="project[content]" class="editor"><?=(isset($data['project']['content']) ? $data['project']['content'] : '');?></textarea></td>
            </tr>

            <tr>
              <th>Text con&#355;inut 2</th>
              <td><textarea name="project[content_2]" class="editor"><?=(isset($data['project']['content_2']) ? $data['project']['content_2'] : '');?></textarea></td>
            </tr>

            <tr>
              <th>Text con&#355;inut 3</th>
              <td><textarea name="project[content_3]" class="editor"><?=(isset($data['project']['content_3']) ? $data['project']['content_3'] : '');?></textarea></td>
            </tr>

            <tr>
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="project[meta_title]" value="<?=(isset($data['project']['meta_title']) ? $data['project']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('meta_desc');?></th>
              <td><input class="count[156]" type="text" name="project[meta_desc]" value="<?=(isset($data['project']['meta_desc']) ? $data['project']['meta_desc'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="project[meta_keyw]" value="<?=(isset($data['project']['meta_keyw']) ? $data['project']['meta_keyw'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="project[slug]" value="<?=(isset($data['project']['slug']) ? $data['project']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>

      <?php
      //debug($data['project']['filters']);
      if(haveFilters(CONTROLLER))
      if(isset($data['project']['filters']) && $data['project']['filters'] && count($data['project']['filters']) > 0 
        && ((isset($data['project']['category_id']) && haveCategories(CONTROLLER)) || !haveCategories(CONTROLLER)))
      {
        ?>
        <div class="column hidden">
        
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
              //debug($data['project']['filters']);
              ?>
                <?php foreach($data['project']['filters'] as $filters): ?>	
                
                <?php 
                if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                
                  <tr>
                  
                    <th><?php echo $filters['title'];?></th>
                    
                    <td> 
                    
                      <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                        
                        <?php $x = 1; ?>
                        
                        <?php foreach($filters['subelements'] as $filters_element): ?>
                          
                          <label><input type='checkbox' name="project[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['project']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['project']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                          
                            <?php echo $filters_element['filter_item_title'];?></label><br>
                          
                          <?php $x++; ?>
                          
                        <?php endforeach; ?>
                        
                      <?php endif; ?>		
                      
                    </td>
                    
                  </tr>	
                  
                <?php endif; ?>			
                
              <?php endforeach; ?>
              
              <?php else: ?>		
                  <?php foreach($data['project']['filters'] as $filters): ?>	
                
                    <?php 
                    if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                    
                      <tr>
                      
                        <th><?php echo $filters['title'];?></th>
                        
                        <td> 
                        
                          <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                            
                            <?php $x = 1; ?>
                            
                            <?php foreach($filters['subelements'] as $filters_element): ?>
                              
                              <label><input type='checkbox' name="project[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['project']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['project']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                              
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
      
          <h2>Logo</h2>
          
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
        
          <?php if(!empty($data['media']['logo'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['logo'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
      
          <h2>Imagine de copert&#259;</h2>
          
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
        
          <?php if(!empty($data['media']['cover'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['cover'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="cover[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Galerie foto con&#355;inut (1)</h2>
          
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
        
          <?php if(!empty($data['media']['photos'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['photos'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="photos[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Galerie foto con&#355;inut (2)</h2>
          
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
        
          <?php if(!empty($data['media']['photos_2'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['photos_2'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="photos_2[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Galerie foto con&#355;inut (3)</h2>
          
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
        
          <?php if(!empty($data['media']['photos_3'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['photos_3'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['project']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['project']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
                  <input type="file" accept="image/*" name="photos_3[]" multiple="multiple" class="file_hack">
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
                <textarea maxlength="110" name="twitter_post_text"><?=(isset($data['project']['description']) ? str_shorten($data['project']['description'], 110) : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="twitter_post_link" value="<?=(isset($data['project']['slug']) ? $this->googleurl->makeRequest($data['project']['slug']) : '');?>">
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
                <textarea name="facebook_post_text"><?=(isset($data['project']['description']) ? $data['project']['description'] : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="facebook_post_link" value="<?=(isset($data['project']['slug']) ? $this->googleurl->makeRequest($data['project']['slug']) : '');?>">
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
  MIN_IMG_W =	<?=PROJECTS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=PROJECTS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>