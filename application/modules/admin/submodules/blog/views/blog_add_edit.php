<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  if(haveFilters(CONTROLLER) && $_POST['blog']['title'] == ''){
    if($_POST['blog']['title'] == ''){
      if(isset($_POST['blog']['filters'])) $filters = $data['blog']['filters'];
      else $filters = '';
    }
    $data['blog'] = $_POST['blog'];
    if(isset($_POST['blog']['filters'])) $selected_filters = $_POST['blog']['filters'];
    else $selected_filters = '';
    $data['blog']['filters'] =  $filters;
    $data['blog']['filters_post_selected'] = $selected_filters;
  
  }else{
    $data['blog'] = $_POST['blog'];
  }
  $data['blog']['start_date'] = strtotime($_POST['blog']['start_date']);
  $data['blog']['end_date'] = strtotime($_POST['blog']['end_date']);
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="blog_edit_add">
    
      <input type="hidden" name="blog[blog_id]" value="<?=(isset($data['blog']['blog_id']) ? $data['blog']['blog_id'] : '');?>">
      
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
              <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-blog"></div>
              <h1><?=$this->lang->line('add_blog')?></h1>
            
            </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="blog[language][code]" value="<?=$data['blog']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['blog']['language']['code']?>"></div>
              <h1><?=$this->lang->line('edit_blog')?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['blog']['blog_id']?>/<?=$language['language_id'];?>">
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
              <td><input type="text" name="blog[title]" value="<?=(isset($data['blog']['title']) ? $data['blog']['title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('active');?></th>
              <td>
                <?php if(isset($data['blog']['sub_active'])): ?>
                
                  <?php if($data['blog']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="blog[sub_active]" value="1" checked="checked">Da
                    <input type="radio" name="blog[sub_active]" value="0">Nu
                  
                  <?php else: ?>
                  
                    <input type="radio" name="blog[sub_active]" value="1">Da
                    <input type="radio" name="blog[sub_active]" value="0" checked="checked">Nu
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="blog[sub_active]" value="1" checked="checked">Da
                  <input type="radio" name="blog[sub_active]" value="0">Nu
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('start_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="blog[start_date]" value="<?=(isset($data['blog']['start_date']) ? date('d-m-Y', $data['blog']['start_date']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('end_date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="blog[end_date]" value="<?=(isset($data['blog']['end_date']) ? date('d-m-Y', $data['blog']['end_date']) : date('d-m-Y', strtotime('+1 month', time())));?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_description');?></th>
              <td><textarea class="count[120]" name="blog[description]"><?=(isset($data['blog']['description']) ? $data['blog']['description'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_text');?></th>
              <td><textarea name="blog[content]" class="editor"><?=(isset($data['blog']['content']) ? $data['blog']['content'] : '');?></textarea></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('tags');?></th>
              <td><input type="text" name="blog[tags]" value="<?=(isset($data['blog']['tags']) ? $data['blog']['tags'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="blog[meta_title]" value="<?=(isset($data['blog']['meta_title']) ? $data['blog']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_desc');?></th>
              <td><input class="count[156]" type="text" name="blog[meta_desc]" value="<?=(isset($data['blog']['meta_desc']) ? $data['blog']['meta_desc'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="blog[meta_keyw]" value="<?=(isset($data['blog']['meta_keyw']) ? $data['blog']['meta_keyw'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="blog[slug]" value="<?=(isset($data['blog']['slug']) ? $data['blog']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>
      
      <?php
      if(haveFilters(CONTROLLER))
      if(isset($data['blog']['filters']) && $data['blog']['filters'] && count($data['blog']['filters']) > 0 
        && ((isset($data['blog']['category_id']) && haveCategories(CONTROLLER)) || !haveCategories(CONTROLLER)))
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
              //debug($data['blog']['filters']);
              ?>
                <?php foreach($data['blog']['filters'] as $filters): ?>	
                
                <?php 
                if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                
                  <tr>
                  
                    <th><?php echo $filters['title'];?></th>
                    
                    <td> 
                    
                      <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                        
                        <?php $x = 1; ?>
                        
                        <?php foreach($filters['subelements'] as $filters_element): ?>
                          
                          <input type='checkbox' name="blog[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['blog']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['blog']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                          
                          <?php echo $filters_element['filter_item_title'];?><br>
                          
                          <?php $x++; ?>
                          
                        <?php endforeach; ?>
                        
                      <?php endif; ?>		
                      
                    </td>
                    
                  </tr>	
                  
                <?php endif; ?>			
                
              <?php endforeach; ?>
              
              <?php else: ?>		
                  
                <?php foreach($data['blog']['filters'] as $filters): ?>	
                
                    <?php 
                    if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                    
                      <tr>
                      
                        <th><?php echo $filters['title'];?></th>
                        
                        <td> 
                        
                          <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                            
                            <?php $x = 1; ?>
                            
                            <?php foreach($filters['subelements'] as $filters_element): ?>
                              
                              <input type='checkbox' name="blog[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['blog']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['blog']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                              
                              <?php echo $filters_element['filter_item_title'];?><br>
                              
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
        
          <?php if(isset($data['media'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['blog']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['blog']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['blog']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['blog']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
      
      <?php if(!empty($data['comments'])): ?>
        <div class="column" id='overview hidden'>
        
          <a id="anchor_media"></a>
      
          <div class="subheader">
        
            <h2><?=$this->lang->line('comments');?></h2>
            
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
            
            <table class='normal_format_table'>
              
                <thead>
                
                  <tr>
                  
                    <td style="width: 8%;padding-left:2%;">Nr</td>
                    <td class="text_left" style="width: 10%;">
                      <p style="padding-left: 10px;"><?=$this->lang->line('name');?></p>
                    </td>
                    <td class="text_left" style="width: 10%;">
                      <p style="padding-left: 10px;"><?=$this->lang->line('email');?></p>
                    </td>
                    <td style="width: 10%">
                      <p><?=$this->lang->line('date');?></p>
                    </td>
                    <td class="sortable" style="width: 30%">
                      <p><?=$this->lang->line('content');?></p>
                    </td>	
                    <td style="width: 13%;">
                      <p><?=$this->lang->line('active');?></p>
                    </td>
                    <td style="width: 10%;">
                      <p><?=$this->lang->line('delete');?></p>
                    </td>
                  </tr>							
                </thead>							
                <tbody>							
                  <?php $i = 0; ?>
                  <?php 
                  foreach($data['comments'] as $comment): ?>
                    
                    <tr>					
                      <td style="width: 8%;padding-left:2%;"><?=++$i;?></td>
                      <td class="text_left" style="width: 10%;">
                        <?=$comment['name'];?>
                      </td>
                      
                      <td class="text_left" style="width: 10%">
                        <span style="line-height: 40px; padding-left: 10px;"><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$comment['email'];?>"><?=$comment['email'];?></a></span>
                      </td>
                      
                      <td style="width: 10%">
                        
                        <p>
                          <?=date('d-m-Y', $comment['comment_date']);?>
                        </p>
                      </td>
                      
                      <td style="width: 20%">
                        
                        <p>
                          <?php echo $comment['content'];?>
                        </p>
                      </td>

                      <td style="width: 10%">
                      
                        
                        <p>
                      
                        <?php if(permission(CONTROLLER, 'edit')): ?>
                      
                          <?php if($comment['active'] == 1): ?>
                          
                          <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                          
                          <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="0"><?=$this->lang->line('no');?>
                          
                          <?php else: ?>
                          
                          <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                          
                          <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                          
                          <?php endif; ?>
                          
                        <?php endif; ?>
                        
                        </p>
                          
                      </td>
                      
                      <td style="width: 10%">
                        
                        <p><?=(permission('blog', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . $this->url->current . '/delete/' . $comment['comment_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
                      </td>
                    
                    </tr>
                    
                  
                    <?php if(isset($comment['subitems']) && count($comment['subitems']) > 0){ ?>
                      <?php $j = 0; ?>
                      <?php foreach($comment['subitems'] as $comment): ?>
                        
                        <tr>					
                          <td style="width: 8%;padding-left:2%;"><?=$i;?>. <?=++$j;?></td>
                          <td class="text_left" style="width: 10%;">
                            <?=$comment['name'];?>
                          </td>
                          
                          <td class="text_left" style="width: 10%">
                            <span style="line-height: 40px; padding-left: 10px;"><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$comment['email'];?>"><?=$comment['email'];?></a></span>
                          </td>
                          
                          <td style="width: 10%">
                            
                            <p>
                              <?=date('d-m-Y', $comment['comment_date']);?>
                            </p>
                          </td>
                          
                          <td style="width: 20%">
                            
                            <p>
                              <?php echo $comment['content'];?>
                            </p>
                          </td>

                          <td style="width: 10%">
                          
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                          
                              <?php if($comment['active'] == 1): ?>
                              
                              <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                              
                            <?php endif; ?>
                            
                            </p>
                              
                          </td>
                          
                          <td style="width: 10%">
                            
                            <p><?=(permission('blog', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . $this->url->current . '/delete/' . $comment['comment_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
                          </td>
                        
                        </tr>
                        
                        <?php if(isset($comment['subitems']) && count($comment['subitems']) > 0){ ?>
                          <?php $k = 0; ?>
                          <?php foreach($comment['subitems'] as $comment): ?>
                            
                            <tr>					
                              <td style="width: 8%;padding-left:2%;"><?=$i;?>. <?=$j;?>. <?=++$k;?></td>
                              <td class="text_left" style="width: 10%;">
                                <?=$comment['name'];?>
                              </td>
                              
                              <td class="text_left" style="width: 10%">
                                <span style="line-height: 40px; padding-left: 10px;"><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$comment['email'];?>"><?=$comment['email'];?></a></span>
                              </td>
                              
                              <td style="width: 10%">
                                
                                <p>
                                  <?=date('d-m-Y', $comment['comment_date']);?>
                                </p>
                              </td>
                              
                              <td style="width: 20%">
                                
                                <p>
                                  <?php echo $comment['content'];?>
                                </p>
                              </td>

                              <td style="width: 10%">
                              
                                
                                <p>
                              
                                <?php if(permission(CONTROLLER, 'edit')): ?>
                              
                                  <?php if($comment['active'] == 1): ?>
                                  
                                  <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                  
                                  <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                  
                                  <?php else: ?>
                                  
                                  <input type="radio" name="active[<?=$comment['comment_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                  
                                  <input type="radio" name="active[<?=$comment['comment_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                  
                                  <?php endif; ?>
                                  
                                <?php endif; ?>
                                
                                </p>
                                  
                              </td>
                              
                              <td style="width: 10%">
                                
                                <p><?=(permission('blog', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . $this->url->current . '/delete/' . $comment['comment_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
                              </td>
                            
                            </tr>
                            
                          <?php endforeach; ?>
                          
                        
                        <?php } ?>
                      
                  
                      <?php endforeach; ?>
                      
                    
                    <?php } ?>
                  
                  <?php endforeach; ?>
                    
                </tbody>
              
              </table>
            
          </div>
        </div>
      <?php endif; ?>
      
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
        
        <div class="subcolumn hidden">
        
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
                <textarea maxlength="110" name="twitter_post_text"><?=(isset($data['blog']['description']) ? str_shorten($data['blog']['description'], 110) : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="twitter_post_link" value="<?=(isset($data['blog']['slug']) ? $this->googleurl->makeRequest($data['blog']['slug']) : '');?>">
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
                <textarea name="facebook_post_text"><?=(isset($data['blog']['description']) ? $data['blog']['description'] : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="facebook_post_link" value="<?=(isset($data['blog']['slug']) ? $this->googleurl->makeRequest($data['blog']['slug']) : '');?>">
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
  MIN_IMG_W =	<?=BLOG_CROP_MAX_W;?>;
  MIN_IMG_H = <?=BLOG_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>