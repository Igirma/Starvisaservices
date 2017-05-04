<?php require_once(ELEM_DIR . 'admin_header.php');

function stripslashes_recursive($value)
{
  $value = is_array($value) ? array_map('stripslashes_recursive', $value) : (strlen($value) > 0 ? str_replace(array('\\', '\"'), array('', '"'), $value) : '');
  return $value;
}

if (!empty($_POST)){
  $_POST = stripslashes_recursive($_POST);

  if(haveFilters(CONTROLLER) && $_POST['form']['page_content']['menu_title'] == ''){

    if($_POST['form']['page_content']['menu_title'] == ''){
      if(isset($_POST['page']['filters'])) $filters = $data['page']['filters'];
      else $filters = '';
    }
    $data['page'] = $_POST['page'];
    if(isset($_POST['page']['filters'])) $selected_filters = $_POST['page']['filters'];
    else $selected_filters = '';
    $data['page']['filters'] =  $filters;
    $data['page']['filters_post_selected'] = $selected_filters;
  
  }
  $data['page']['form']['page_content'] = $_POST['form']['page_content'];
  $data['page']['form']['mobile_content'] = $_POST['form']['mobile_content'];
  $data['page']['external'] = $_POST['external'];
}

?>


<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data">
          
      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <?php if($this->url->segment(2) == 'edit'){ ?>
        <div class="back_button">
          <a class="preview" href="<?=SITE_URL . subSlug($data['page']['form']['page_content']['page_id']) . '/' . $_SESSION['login_salt']?>"><?=$this->lang->line('preview')?></a>
        </div>
        <?php } ?>
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">
      
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="page[language][0][code]" value="<?=$data['page']['language'][0]['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['page']['language'][0]['code']?>"></div>
              <h1><?=$this->lang->line('header_edit');?></h1>
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['page']['form']['page_content']['page_id'];?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>

                <?php endforeach; ?>
                
              </div>
            </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'add'): ?>

            <div class="header">
              <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-pages"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
            </div>

        <?php endif; ?>
      
      </div>
      
      <div class="column">
        
        <div class="subheader">
        
          <h2><?=$this->lang->line('header_settings')?></h2>
          
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
            
            <!-- Page + mobile content form fields -->

            <?php if($this->config->item('max_page_depth') > 1): ?>
              
              <tr>
              
                <th><?=$this->lang->line('parent_id')?></th>
                
                <td>
                  <select name="parent_id">
                  
                      <option value="0" <?=(!isset($data['page']['parent_id']) ? 'selected="selected"' : '');?>><?=$this->lang->line('choose_page')?></option>
                      
                      <?php foreach($data['drop_down'] as $page): ?>
                      
                      <?php if(isset($data['page']['form']) && $data['page']['form']['page_content']['page_id'] != $page['page_id'] && isset($data['count_children']['sub_sub']) && $data['count_children']['sub_sub'] < 1): ?>
                      
                        <option <?=(isset($data['page']['parent_id']) && $data['page']['parent_id'] == $page['page_id'] ? 'selected="selected"' : '');?> value="<?=$page['page_id']?>"><?=$page['menu_title']?></option>
                      
                      <?php else: ?>
                        
                        <option value="<?=$page['page_id']?>"><?=$page['menu_title']?></option>
                      
                      <?php endif; ?>
                      
                        <?php if($this->config->item('max_page_depth') > 2): ?>
                      
                          <?php foreach($page['children'] as $child): ?>
                            
                            <?php if(isset($data['page']['form']) && $data['page']['form']['page_content']['page_id'] != $child['page_id'] && $data['count_children']['sub'] < 1): ?>
                              
                              <option <?=(isset($data['page']['parent_id']) && $data['page']['parent_id'] == $child['page_id'] ? 'selected="selected"' : '');?> value="<?=$child['page_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
                            
                            <?php elseif (isset($data['count_children']['sub']) && $data['count_children']['sub'] > 0): ?>
                              
                              <option disabled="disabled" value="<?=$child['page_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
                            
                            <?php else: ?>
                              
                              <option value="<?=$child['page_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
                            
                            <?php endif; ?>
                          
                          <?php endforeach; ?>
                          
                        <?php endif; ?>
                        
                      <?php endforeach; ?>

                  </select>
                  
                </td>
                
              </tr>
            
            <?php else: ?>
            
              <input type="hidden" name="parent_id" value="0" >
            
            <?php endif; ?>

            <tr>
              
              <th>
                
                <?=$this->lang->line('external')?>
                
              </th>
              
              <td>
                
                <?php if(isset($data['page']['external'])): ?>
                  
                  <?php if($data['page']['external'] == 1): ?>
                  
                    <input type="radio" name="external" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="external" value="0"><?=$this->lang->line('no')?>
                    
                  <?php else: ?>
                    
                    <input type="radio" name="external" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="external" value="0" checked="checked"><?=$this->lang->line('no')?>
                    
                  <?php endif; ?>
                
                <?php else: ?>
                  
                  <input type="radio" name="external" value="1"><?=$this->lang->line('yes')?>
                  <input type="radio" name="external" value="0" checked="checked"><?=$this->lang->line('no')?>
                  
                <?php endif; ?>
                
              </td>
              
            </tr>
          
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['page']['form']['page_content']['sub_active'])): ?>
                
                  <?php if($data['page']['form']['page_content']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="form[page_content][sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="form[page_content][sub_active]" value="0"><?=$this->lang->line('no')?>
                  
                  <?php else: ?>
                  
                    <input type="radio" name="form[page_content][sub_active]" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="form[page_content][sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="form[page_content][sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                  <input type="radio" name="form[page_content][sub_active]" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
              </td>
            </tr>

          </table>
        
        </div>
        
      </div>

      <div class="column">
      
        <div class="subheader">
        
          <h2><?=$this->lang->line('page_content')?></h2>
          
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
        
          <input type="hidden" name="form[page_content][page_id]" value="<?=(isset($data['page']['form']['page_content']['page_id']) ? $data['page']['form']['page_content']['page_id'] : '');?>">
        
          <table>
            
            <!-- Page content form fields -->
            
            <tr>
              
              <th><?=$this->lang->line('menu_title')?></th>
              
              <td><input type="text" name="form[page_content][menu_title]" value="<?=(isset($data['page']['form']['page_content']['menu_title']) ? $data['page']['form']['page_content']['menu_title'] : '');?>" ></td>
            
            </tr>
           
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('overview_description')?></th>
              
              <td><textarea name="form[page_content][overview_description]"><?=(isset($data['page']['form']['page_content']['overview_description']) ? $data['page']['form']['page_content']['overview_description'] : '');?></textarea></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('content_title')?></th>
              
              <td><input type="text" name="form[page_content][content_title]" value="<?=(isset($data['page']['form']['page_content']['content_title']) ? $data['page']['form']['page_content']['content_title'] : '');?>" ></td>
            
            </tr>

            <tr class="hidden">

              <th>Titlu tooltip</th>

              <td><input type="text" name="form[page_content][overview_title]" value="<?=(isset($data['page']['form']['page_content']['overview_title']) ? $data['page']['form']['page_content']['overview_title'] : '');?>" ></td>

            </tr>
            
            
            <?php foreach (array('Submitted', 'Received', 'Processing', 'Completed', 'Closed') as $k => $status) { ?>
            <tr class="hidden">
              
              <th>Order status:<br><b><?php echo $status; ?></b></th>
              
              <td><textarea name="form[page_content][content_status_<?php echo strtolower($status); ?>]"><?=(isset($data['page']['form']['page_content']['content_status_' . strtolower($status)]) ? $data['page']['form']['page_content']['content_status_' . strtolower($status)] : '');?></textarea></td>
            
            </tr>
            <?php } ?>

            <tr>

              <th><?=$this->lang->line('content_description')?></th>

              <td><textarea name="form[page_content][content_description]"><?=(isset($data['page']['form']['page_content']['content_description']) ? $data['page']['form']['page_content']['content_description'] : '');?></textarea></td>

            </tr>

            <tr>

              <th><?=$this->lang->line('content_text')?></th>

              <td style="padding-top: 5px; padding-bottom: 5px;"><textarea class="editor" name="form[page_content][content_text]"><?=(isset($data['page']['form']['page_content']['content_text']) ? $data['page']['form']['page_content']['content_text'] : '');?></textarea></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th>Titlu pagin&#259; homepage</th>
              
              <td><input type="text" name="form[page_content][content_column_left]" value="<?=(isset($data['page']['form']['page_content']['content_column_left']) ? $data['page']['form']['page_content']['content_column_left'] : '');?>"></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th>Subtitlu pagin&#259; homepage</th>
              
              <td><input type="text" name="form[page_content][content_column_right]" value="<?=(isset($data['page']['form']['page_content']['content_column_right']) ? $data['page']['form']['page_content']['content_column_right'] : '');?>"></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th>Titlu / autor pagin&#259; homepage</th>
              
              <td><input type="text" name="form[page_content][overview_text]" value="<?=(isset($data['page']['form']['page_content']['overview_text']) ? $data['page']['form']['page_content']['overview_text'] : '');?>" ></td>
            
            </tr>

            <?php for ($i = 1; $i < 7; $i++) { ?>
            <tr>

              <th style="font-style: italic;">Text <?php echo $i; ?></th>
              
              <td>
                <input type="text" name="form[page_content][title_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['title_' . $i]) ?  htmlentities($data['page']['form']['page_content']['title_' . $i]) : '');?>" style="width: 47%; float: left; margin-right: 1%;">
                <input type="text" name="form[page_content][subtitle_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['subtitle_' . $i]) ? htmlentities($data['page']['form']['page_content']['subtitle_' . $i]) : '');?>" style="width: 47%; float: left;">
              </td>

            </tr>
            <?php } ?>
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('ex_name')?></th>
              
              <td><input type="text" name="form[page_content][ex_name]" value="<?=(isset($data['page']['form']['page_content']['ex_name']) ? $data['page']['form']['page_content']['ex_name'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('ex_url')?></th>
              
              <td><input type="text" name="form[page_content][ex_url]" value="<?=(isset($data['page']['form']['page_content']['ex_url']) ? $data['page']['form']['page_content']['ex_url'] : 'http://');?>" ></td>
            
            </tr>		
            
            <tr>
              
              <th><?=$this->lang->line('slug')?></th>
              
              <td><input type="text" name="form[page_content][slug]" value="<?=(isset($data['page']['form']['page_content']['slug']) ? $data['page']['form']['page_content']['slug'] : '');?>" ></td>
            
            </tr>
            
            <tr>
            
              <th>
                <?=$this->lang->line('meta_title');?>
                <br>
                
              </th>
              
              <td>
                <input class="count[63]" type="text" name="form[page_content][meta_title]" value="<?=(isset($data['page']['form']['page_content']['meta_title']) ? $data['page']['form']['page_content']['meta_title'] : '');?>" >
              </td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('meta_desc')?></th>
              
              <td><textarea class="count[156]" name="form[page_content][meta_desc]"><?=(isset($data['page']['form']['page_content']['meta_desc']) ? $data['page']['form']['page_content']['meta_desc'] : '');?></textarea></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('meta_keyw')?></th>
              
              <td><input class="count[256]" type="text" name="form[page_content][meta_keyw]" value="<?=(isset($data['page']['form']['page_content']['meta_keyw']) ? $data['page']['form']['page_content']['meta_keyw'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              <th>Footer title</th>
              <td><input type="text" name="form[page_content][footer_title_2]" value="<?=(isset($data['page']['form']['page_content']['footer_title_2']) ? $data['page']['form']['page_content']['footer_title_2'] : '');?>"></td>
            </tr>
            <tr>
              <th>Footer subtitle</th>
              <td><input type="text" name="form[page_content][footer_text_2]" value="<?=(isset($data['page']['form']['page_content']['footer_text_2']) ? $data['page']['form']['page_content']['footer_text_2'] : '');?>"></td>
            </tr>
            
            
<?php if (isset($data['videos'])) { ?>
            <tr class="hidden">
              <th>Video's</th>
              <td style="padding-top: 10px; padding-bottom: 10px;">
<?php foreach ($data['videos'] as $video) { ?>
                <label><input type='checkbox' name='page[video_id][]' value='<?php echo $video['video_id'];?>' <?=(isset($data['page']['selected_videos']) && is_array($data['page']['selected_videos']) && in_array($video['video_id'], $data['page']['selected_videos']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;<?=$video['title']?></label><br>
<?php } ?>
              </td>
            </tr>
<?php } ?>
            
          </table>
        
        </div>
      
      </div>

      <div class="column <?=($this->config->item('mobile_website') == 0 ? 'hidden' : '')?>">

        <div class="subheader">
        
          <h2><?=$this->lang->line('mobile_content')?></h2>
          
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
      
        <div class="subcolumn ">
        
          <table>
            
            <!-- Mobile content form fields -->

            <tr>
              
              <th><?=$this->lang->line('menu_title')?></th>
              
              <td><input type="text" name="form[mobile_content][menu_title]" value="<?=(isset($data['page']['form']['mobile_content']['menu_title']) ? $data['page']['form']['mobile_content']['menu_title'] : '')?>"></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('overview_title');?></th>
              
              <td><input type="text" name="form[mobile_content][overview_title]" value="<?=(isset($data['page']['form']['mobile_content']['overview_title']) ? $data['page']['form']['mobile_content']['overview_title'] : '')?>" ></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('overview_description')?></th>
              
              <td><textarea name="form[mobile_content][overview_description]"><?=(isset($data['page']['form']['mobile_content']['overview_description']) ? $data['page']['form']['mobile_content']['overview_description'] : '');?></textarea></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('overview_text')?></th>
              
              <td><input type="text" name="form[mobile_content][overview_text]" value="<?=(isset($data['page']['form']['mobile_content']['overview_text']) ? $data['page']['form']['mobile_content']['overview_text'] : '');?>" ></td>
            
            </tr>

            <tr>
              
              <th><?=$this->lang->line('content_title')?></th>
              
              <td><input type="text" name="form[mobile_content][content_title]" value="<?=(isset($data['page']['form']['mobile_content']['content_title']) ? $data['page']['form']['mobile_content']['content_title'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('content_description')?></th>
              
              <td><textarea name="form[mobile_content][content_description]"><?=(isset($data['page']['form']['mobile_content']['content_description']) ? $data['page']['form']['mobile_content']['content_description'] : '');?></textarea></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('content_text')?></th>
              
              <td><textarea class="editor" name="form[mobile_content][content_text]"><?=(isset($data['page']['form']['mobile_content']['content_text']) ? $data['page']['form']['mobile_content']['content_text'] : '');?></textarea></td>
            
            </tr>

            <tr class="hidden">
              
              <th>Tekst onder de foto</th>
              
              <td style="padding-top: 5px; padding-bottom: 5px;"><textarea class="editor" name="form[mobile_content][content_column_left]"><?=(isset($data['page']['form']['mobile_content']['content_column_left']) ? $data['page']['form']['mobile_content']['content_column_left'] : '');?></textarea></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th>Tekst onder de foto</th>
              
              <td style="padding-top: 5px; padding-bottom: 5px;"><textarea class="editor" name="form[mobile_content][content_column_right]"><?=(isset($data['page']['form']['mobile_content']['content_column_right']) ? $data['page']['form']['mobile_content']['content_column_right'] : '');?></textarea></td>
            
            </tr>
            
            <tr class="hidden">
              
              <th><?=$this->lang->line('ex_name')?></th>
              
              <td><input type="text" name="form[mobile_content][ex_name]" value="<?=(isset($data['page']['form']['mobile_content']['ex_name']) ? $data['page']['form']['mobile_content']['ex_name'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('ex_url')?></th>
              
              <td><input type="text" name="form[mobile_content][ex_url]" value="<?=(isset($data['page']['form']['mobile_content']['ex_url']) ? $data['page']['form']['mobile_content']['ex_url'] : 'http://');?>" ></td>
            
            </tr>		
            
            <tr>
              
              <th><?=$this->lang->line('slug')?></th>
              
              <td><input type="text" name="form[mobile_content][slug]" value="<?=(isset($data['page']['form']['mobile_content']['slug']) ? $data['page']['form']['mobile_content']['slug'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('meta_title')?></th>
              
              <td><input type="text" name="form[mobile_content][meta_title]" value="<?=(isset($data['page']['form']['mobile_content']['meta_title']) ? $data['page']['form']['mobile_content']['meta_title'] : '');?>" ></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('meta_desc')?></th>
              
              <td><textarea name="form[mobile_content][meta_desc]"><?=(isset($data['page']['form']['mobile_content']['meta_desc']) ? $data['page']['form']['mobile_content']['meta_desc'] : '');?></textarea></td>
            
            </tr>
            
            <tr>
              
              <th><?=$this->lang->line('meta_keyw')?></th>
              
              <td><input type="text" name="form[mobile_content][meta_keyw]" value="<?=(isset($data['page']['form']['mobile_content']['meta_keyw']) ? $data['page']['form']['mobile_content']['meta_keyw'] : '');?>" ></td>
            
            </tr>
      
          
          </table>
          
        </div>
      
      </div>
      
      

      <?php
      if(haveFilters(CONTROLLER))
      if(isset($data['page']['filters']) && $data['page']['filters'] && count($data['page']['filters']) > 0 
        && ((isset($data['page']['category_id']) && haveCategories(CONTROLLER)) || !haveCategories(CONTROLLER)))
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
              
              <?php if(haveCategories(CONTROLLER)): ?>
                <?php foreach($data['page']['filters'] as $filters): ?>	
                
                <?php 
                if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                
                  <tr>
                  
                    <th><?php echo $filters['title'];?></th>
                    
                    <td> 
                    
                      <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                        
                        <?php $x = 1; ?>
                        
                        <?php foreach($filters['subelements'] as $filters_element): ?>
                          
                          <label><input type='checkbox' name="page[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['page']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['page']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>	
                            <?php echo $filters_element['filter_item_title'];?></label><br>
                          
                          <?php $x++; ?>
                          
                        <?php endforeach; ?>
                        
                      <?php endif; ?>		
                      
                    </td>
                    
                  </tr>	
                  
                <?php endif; ?>			
                
              <?php endforeach; ?>
              
              <?php else: ?>		
                  
                <?php foreach($data['page']['filters'] as $filters): ?>	
                
                    <?php 
                    if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                    
                      <tr>
                      
                        <th><?php echo $filters['title'];?></th>
                        
                        <td> 
                        
                          <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                            
                            <?php $x = 1; ?>
                            
                            <?php foreach($filters['subelements'] as $filters_element): ?>
                              
                              <label><input type='checkbox' name="page[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['page']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['page']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                              
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
      
      <!--
      <div class="column hidden">
      
        <a id="anchor_media"></a>
        
        <div class="subheader">
      
          <h2>Columns (optional)</h2>
          
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
        
        <div class="subcolumn" style="display: none;">

          <div class="clear"></div>
        
          <table>
<?php for ($i = 1; $i <= 3; $i++) { ?>
            <tr>
              <th>Title / Subtitle (<?php echo $i; ?>)</th>
              <td>
                <input type="text" name="form[page_content][title_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['title_' . $i]) ? $data['page']['form']['page_content']['title_' . $i] : '');?>" style="width: 42%;">
                <input type="text" name="form[page_content][subtitle_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['subtitle_' . $i]) ? $data['page']['form']['page_content']['subtitle_' . $i] : '');?>" style="width: 42%; margin-left: 1%;">
              </td>
            </tr>
<?php } ?>
          </table>
        
        </div>
      
      </div>
      -->
      
      <div class="column hidden">
      
        <a id="anchor_media"></a>
        
        <div class="subheader">
      
          <h2>Video</h2>
          
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
        
        <div class="subcolumn" style="display: none;">

          <div class="clear"></div>
        
          <table>
<?php for ($i = 1; $i <= 6; $i++) { ?>
            <tr>
              <th>URL / Title (<?php echo $i; ?>)</th>
              <td>
                <input type="text" name="form[page_content][video_url_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['video_url_' . $i]) ? $data['page']['form']['page_content']['video_url_' . $i] : '');?>" style="width: 42%;">
                <input type="text" name="form[page_content][video_text_<?php echo $i; ?>]" value="<?=(isset($data['page']['form']['page_content']['video_text_' . $i]) ? $data['page']['form']['page_content']['video_text_' . $i] : '');?>" style="width: 42%; margin-left: 1%;">
              </td>
            </tr>
<?php } ?>
          </table>
        
        </div>
      
      </div>
      
      <div class="column hidden">
      
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
          
          <?php if (isset($data['page']['media']['photos'])) { ?>
          
            <?php $x = 0; ?>
            <?php foreach ($data['page']['media']['photos'] as $media) { ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
                </div>

              </div>
              
              <?php $x++; ?>
            
            <?php } ?>
          
          <?php } ?>
          
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
                  <input type="file" name="photos[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      
      
      <div class="column hidden">
      
        <a id="anchor_media"></a>
        
        <div class="subheader">
      
          <h2>Page header</h2>
          
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
          
          <?php if (isset($data['page']['media']['slide'])) { ?>
          
            <?php $x = 0; ?>
            <?php foreach ($data['page']['media']['slide'] as $media) { ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
                </div>

              </div>
              
              <?php $x++; ?>
            
            <?php } ?>
          
          <?php } ?>
          
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
                  <input type="file" name="slide[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
      
      


      <div class="column">
      
        <a id="anchor_media"></a>
        
        <div class="subheader">
      
          <h2>Page background image</h2>
          
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
          
          <?php if (isset($data['page']['media']['home'])) { ?>
          
            <?php $x = 0; ?>
            <?php foreach ($data['page']['media']['home'] as $media) { ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['page']['form']['page_content']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['page']['form']['page_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
                </div>

              </div>
              
              <?php $x++; ?>
            
            <?php } ?>
          
          <?php } ?>
          
          <div class="clear"></div>
        
          <table>
            
            <tr>
            
              <th>
                New image<br><small>1280&times;850 px</small>
              </th>
              <td>
              
                <div class="file_container">
                  <input type="text" readonly="readonly" class="file_replace_text">
                  <input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
                  <input type="submit" name="upload" value="<?=$this->lang->line('upload');?>">
                  <input type="file" name="home[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>

          <div class="clear"></div>

          <table>
            <tr class="hidden">
              <th>Homepage title</th>
              <td><input type="text" name="form[page_content][footer_title_1]" value="<?=(isset($data['page']['form']['page_content']['footer_title_1']) ? $data['page']['form']['page_content']['footer_title_1'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th>Homepage text</th>
              <td><textarea name="form[page_content][footer_text_1]"><?=(isset($data['page']['form']['page_content']['footer_text_1']) ? $data['page']['form']['page_content']['footer_text_1'] : '');?></textarea></td>
            </tr>
            
            <tr class="hidden">
              <th>Titlu subsol (1)</th>
              <td><input type="text" name="form[mobile_content][footer_title_1]" value="<?=(isset($data['page']['form']['mobile_content']['footer_title_1']) ? $data['page']['form']['mobile_content']['footer_title_1'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th>Text subsol (1)</th>
              <td><textarea name="form[mobile_content][footer_text_1]"><?=(isset($data['page']['form']['mobile_content']['footer_text_1']) ? $data['page']['form']['mobile_content']['footer_text_1'] : '');?></textarea></td>
            </tr>
            <tr class="hidden">
              <th>Titlu subsol (2)</th>
              <td><input type="text" name="form[mobile_content][footer_title_2]" value="<?=(isset($data['page']['form']['mobile_content']['footer_title_2']) ? $data['page']['form']['mobile_content']['footer_title_2'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th>Text subsol (2)</th>
              <td><textarea class="editor" name="form[mobile_content][footer_text_2]"><?=(isset($data['page']['form']['mobile_content']['footer_text_2']) ? $data['page']['form']['mobile_content']['footer_text_2'] : '');?></textarea></td>
            </tr>

          </table>

        </div>
      
      </div>

      
      <div class="column">
      
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
        
          <?php if(!empty($data['page']['docs'])): ?>
          
            <div class="docs">
            
              <ul>

            <?php foreach($data['page']['docs'] as $doc): ?>
            
              <?php $ext = @end(explode('.', $doc['filename'])); ?>
              
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
      
      
      
      
      <div class="column"<?php if (!isSuperadmin()) ' style="display: none;"'; ?>>
        
        <div class="subheader">
        
          <h2>Controller rights/settings</h2>

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
        
        <div class="subcolumn" style="display: none;">
        
          <table>
          
            <tr>
              
              <th>Controller</th>
              <td><input type="text" name="controller" value="<?=(isset($data['page']['controller']) ? $data['page']['controller'] : 'pages');?>" ></td>
            
            </tr>
            <tr>
              
              <th>Deletable?</th>
              
              <td>
                
                <?php if (isset($data['page']['deletable'])): ?>
                  
                  <?php if ($data['page']['deletable'] == 1): ?>
                  
                    <input type="radio" name="deletable" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="deletable" value="0"><?=$this->lang->line('no')?>
                    
                  <?php else: ?>
                    
                    <input type="radio" name="deletable" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="deletable" value="0" checked="checked"><?=$this->lang->line('no')?>
                    
                  <?php endif; ?>
                
                <?php else: ?>
                  
                  <input type="radio" name="deletable" value="1" checked="checked"><?=$this->lang->line('yes')?>
                  <input type="radio" name="deletable" value="0"><?=$this->lang->line('no')?>
                  
                <?php endif; ?>
                
              </td>
              
            </tr>

          </table>
        
        </div>
        
      </div>

    </form>
    
  </div>

</div>

<script type="text/javascript">
  MIN_IMG_W =	<?=PAGES_CROP_MAX_W;?>;
  MIN_IMG_H = <?=PAGES_CROP_MAX_H;?>;
</script>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>