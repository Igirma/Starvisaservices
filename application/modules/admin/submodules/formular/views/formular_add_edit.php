<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  if(haveFilters(CONTROLLER) && $_POST['formular']['title'] == ''){
    if($_POST['formular']['title'] == ''){
      if(isset($_POST['formular']['filters'])) $filters = $data['formular']['filters'];
      else $filters = '';
    }
    $data['formular'] = $_POST['formular'];
    if(isset($_POST['formular']['filters'])) $selected_filters = $_POST['formular']['filters'];
    else $selected_filters = '';
    $data['formular']['filters'] =  $filters;
    $data['formular']['filters_post_selected'] = $selected_filters;
  
  }else{
    $data['formular'] = $_POST['formular'];
  }
  $data['formular']['date_created'] = strtotime($_POST['formular']['date_created']);
}

?>

<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="formular_edit_add">
    
      <input type="hidden" name="formular[formular_id]" value="<?=(isset($data['formular']['formular_id']) && $data['formular']['formular_id'] != '' ? $data['formular']['formular_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-formular"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="formular[language_id]" value="<?=$data['formular']['language_id']?>" />
              <input type="hidden" name="formular[language][code]" value="<?=$data['formular']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['formular']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['formular']['formular_id']?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>

                <?php endforeach; ?>
                
              </div>
              
            </div>

        <?php endif; ?>
      
      </div>
      
      
      <?php if($this->url->segment(2) == 'edit'): ?>
      <div class="column">
      
        <div class="subheader">

          <h2><?=$this->lang->line('items');?></h2>
          
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
        
        <div class="subcolumn" id='overview'>
        
          <table <?php echo (($data['formular']['language_id'] != $this->config->item('default_language'))?"class='hidden'":"");?>>

            <tr>
              <td colspan='2'>
                <div class="orange_button" id='overview'>
                  <div class="orange_button_left"></div>
                  <div class="orange_button_con">
                    <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
                    <div class="orange_button_space"></div>
                    <a href="<?=SITE_URL . LANG_CODE . '/admin/add_formular_item/' . $data['formular']['formular_id'] . '/' . $data['formular']['language_id'];?>" class="modal_ajax font_white" onclick="return false;"><?=$this->lang->line('add_item');?></a>
                  </div>
                  <div class="orange_button_right"></div>
                </div>
              </td>
            </tr>
          </table>
          <table>
            <thead>
                <tr>
                  <th style="width: 5%;text-align:center;">Nr</th>
                  <th class="text_left" style="width: 45%;">
                    <p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
                  </th>
                  <th style="width: 10%;text-align:center;">
                    <p><?=$this->lang->line('order');?></p>
                  </th>
                  <th style="width: 10%;text-align:center;">
                    <p><?=$this->lang->line('is_email');?></p>
                  </th>
                  <th style="width: 10%;text-align:center;">
                    <p><?=$this->lang->line('mandatory');?></p>
                  </th>
                  <th style="width: 10%;text-align:center;">
                    <p><?=$this->lang->line('active');?></p>
                  </th>
                  <th style="width: 10%;text-align:center;">
                    <p><?=$this->lang->line('delete');?></p>
                  </th>
                </tr>
              </thead>
              <tbody>
                  
                <?php $i = 0; ?>
                <?php $count_items = 0;
                if(isset($data['form_items']['items'])) $count_items = count($data['form_items']['items']); 
                ?>
                      
                <?php if(!empty($data['form_items']['items'])): ?>
                  <?php foreach($data['form_items']['items'] as $l => $items): ?>
                  
                  <tr>
                    <td style='width:5%;text-align:center;'><?=++$i;?></td>		
                    <td class="text_left" style="width: 45%;">	
                      <?php if(permission(CONTROLLER, 'edit')):?>
                        <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/add_formular_item/' . $data['formular']['formular_id'] . '/' . $data['formular']['language_id'] .'/'. $items['formular_item_id'];?>" class="modal_ajax" onclick="return false;" style="display:block;">
                      <?php endif; ?>
                      
                      <span style="line-height: 40px; padding-left: 10px;">
                        <?php if($items['title'] == ''){ ?>
                          <span class='small_text'>(<?php echo $data['form_items']['default'][$l]['title'];?>)</span>
                        <?php } ?>
                        <?=str_shorten($items['title'], 80);?>
                      </span>

                      <?php if(permission(CONTROLLER, 'edit')):?>
                        </a>
                      <?php endif; ?>
                    
                    </td>
                        
                    <td class="order" style="width: 10%;text-align:center;">
                      <p>		
                        <div class="up_down">
                            
                          <?php if(permission(CONTROLLER, 'edit')): ?>
                              
                            <?php if($i == 1 && $i != $count_items): ?>

                            <input class="order_down_button" type="submit" name="order_down[<?php echo $items['formular_item_id'];?>]" value="">
                                
                            <?php endif; ?>
                                
                            <?php if($i != 1 && $i == $count_items): ?>
                                
                            <input class="order_up_button" type="submit" name="order_up[<?php echo $items['formular_item_id'];?>]" value="">
                                
                            <?php endif; ?>
                                
                            <?php if($i != 1 && $i != $count_items): ?>
                                
                            <input class="order_up_button" type="submit" name="order_up[<?php echo $items['formular_item_id'];?>]" value="">
                            <input class="order_down_button" type="submit" name="order_down[<?php echo $items['formular_item_id'];?>]" value="">
                                
                            <?php endif; ?>
                              
                          <?php endif; ?>
                              
                          <div class="clear"></div>
                              
                        </div>
                          
                      </p>
                        
                    </td>
                        
                    <td style="width: 10%;text-align:center;">
                        
                      <p>
                        
                      <?php if(permission(CONTROLLER, 'edit')): ?>
                        
                        <?php if($items['is_email'] == 1): ?>
                            
                        <input type="radio" name="is_email[<?=$items['formular_item_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="is_email[<?=$items['formular_item_id'];?>]" value="0"><?=$this->lang->line('no');?>
                            
                        <?php else: ?>
                            
                        <input type="radio" name="is_email[<?=$items['formular_item_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="is_email[<?=$items['formular_item_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                            
                        <?php endif; ?>
                            
                      <?php endif; ?>
                          
                      </p>
                            
                    </td>
                              
                    <td style="width: 10%;text-align:center;">
                        
                      <p>
                        
                      <?php if(permission(CONTROLLER, 'edit')): ?>
                        
                        <?php if($items['mandatory'] == 1): ?>
                            
                        <input type="radio" name="mandatory_items[<?=$items['formular_item_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="mandatory_items[<?=$items['formular_item_id'];?>]" value="0"><?=$this->lang->line('no');?>
                            
                        <?php else: ?>
                            
                        <input type="radio" name="mandatory_items[<?=$items['formular_item_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="mandatory_items[<?=$items['formular_item_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                            
                        <?php endif; ?>
                            
                      <?php endif; ?>
                          
                      </p>
                            
                    </td>
                          
                    <td style="width: 10%;text-align:center;">
                        
                      <p>
                        
                      <?php if(permission(CONTROLLER, 'edit')): ?>
                        
                        <?php if($items['active'] == 1): ?>
                            
                        <input type="radio" name="active_items[<?=$items['formular_item_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="active_items[<?=$items['formular_item_id'];?>]" value="0"><?=$this->lang->line('no');?>
                            
                        <?php else: ?>
                            
                        <input type="radio" name="active_items[<?=$items['formular_item_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                            
                        <input type="radio" name="active_items[<?=$items['formular_item_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                            
                        <?php endif; ?>
                            
                      <?php endif; ?>
                          
                      </p>
                            
                    </td>
                        
                    <td style="width: 10%;text-align:center;">
                      <?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete_button_fake" href=""><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
                      <?=((permission(CONTROLLER, 'delete')) ? '<input class="delete_button hidden" type="submit" name="delete_item['.$items['formular_item_id'].']" value="'.$this->lang->line('delete').'">' : '');?>
                    </td>
                      
                  </tr>
                      
                  <?php endforeach; ?>
                <?php endif; ?>
                  
              </tbody>
                
            </table>
        </div>
        
      </div>
      <?php endif; ?>
      
      <div class="column">
      
        <div class="subheader">

          <h2><?=$this->lang->line('formular_info');?></h2>
          
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

            <?php if(($this->config->item('max_page_depth')+1) > 1): ?>
            
            <tr>
              
                <th><?=$this->lang->line('page_id')?></th>
                
                <td>	
                  <div class='padded_content'>
                    <?php foreach($data['drop_down'] as $formular): ?>
                      
                        <input type='checkbox' name='formular[page_id][]' value='<?php echo $formular['page_id'];?>' <?=(isset($data['formular']['page_id']) && in_array($formular['page_id'], $data['formular']['page_id']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;<?=$formular['menu_title']?><br>
                    
                        <?php if(($this->config->item('max_page_depth')+1) > 2): ?>
                      
                          <?php foreach($formular['children'] as $child): ?>
                            
                              <input type='checkbox' name='formular[page_id][]' <?=(isset($data['formular']['page_id']) && in_array($child['page_id'], $data['formular']['page_id']) ? 'checked="checked"' : '');?> value="<?=$child['page_id']?>">&nbsp;&nbsp;<?=$child['menu_title']?><br>	
                          
                          <?php endforeach; ?>
                          
                        <?php endif; ?>
                        
                      <?php endforeach; ?>
                  </div>
                </td>
                
              </tr>
            
            <?php else: ?>
            
              <input type="hidden" name="formular[page_id]" value="0" >
            
            <?php endif; ?>
            
            <tr>
              <th><?=$this->lang->line('content_title');?></th>
              <td><input type="text" name="formular[title]" value="<?=(isset($data['formular']['title']) ? $data['formular']['title'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['formular']['sub_active'])): ?>
                
                  <?php if($data['formular']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="formular[sub_active]" value="1" checked="checked">Ja
                    <input type="radio" name="formular[sub_active]" value="0">Nee
                  
                  <?php else: ?>
                  
                    <input type="radio" name="formular[sub_active]" value="1">Ja
                    <input type="radio" name="formular[sub_active]" value="0" checked="checked">Nee
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="formular[sub_active]" value="1" checked="checked">Ja
                  <input type="radio" name="formular[sub_active]" value="0">Nee
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('date_created');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="formular[date_created]" value="<?=(isset($data['formular']['date_created']) ? date('d-m-Y', $data['formular']['date_created']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('send_email');?></th>
              <td>
                <input type="checkbox" name="formular[mail_for_admin]" value="1" <?=(isset($data['formular']['mail_for_admin']) && $data['formular']['mail_for_admin'] == 1 ? 'checked' : '');?>> <?=$this->lang->line('send_email_to_admin');?> 
                &nbsp;&nbsp;
                <input type="checkbox" name="formular[mail_for_user]" value="1" <?=(isset($data['formular']['mail_for_user']) && $data['formular']['mail_for_user'] == 1 ? 'checked' : '');?>> <?=$this->lang->line('send_email_to_user');?> 
                
              </td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_subject_admin');?></th>
              <td><input type="text" name="formular[subject_admin]" value="<?=(isset($data['formular']['subject_admin']) ? $data['formular']['subject_admin'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_admin');?></th>
              <td><textarea name="formular[content_admin]" class="editor"><?=(isset($data['formular']['content_admin']) ? $data['formular']['content_admin'] : '');?></textarea></td>
            </tr>

            <tr>
              <th><?=$this->lang->line('content_subject_user');?></th>
              <td><input type="text" name="formular[subject_user]" value="<?=(isset($data['formular']['subject_user']) ? $data['formular']['subject_user'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_user');?></th>
              <td><textarea name="formular[content_user]" class="editor"><?=(isset($data['formular']['content_user']) ? $data['formular']['content_user'] : '');?></textarea></td>
            </tr>

          </table>
        
        </div>
        
      </div>

    </form>

  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>