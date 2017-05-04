<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $filters = array();
  $products_options = array();
  if($_POST['product']['title'] == ''){
    if(isset($_POST['product']['filters'])) $filters = $data['product']['filters'];
    else $filters = false;
    if(isset($_POST['product']['products_options'])) $products_options = $data['product']['products_options'];
    else $products_options = false;
  }
  
  $data['product'] = $_POST['product'];
  $data['product']['date_created'] = strtotime($_POST['product']['date_created']);
  
  if(isset($_POST['product']['filters'])) $selected_filters = $_POST['product']['filters'];
  else $selected_filters = '';
  if(isset($_POST['product']['products_options'])) $selected_products_options = $_POST['product']['products_options'];
  else $selected_products_options = '';
  $data['product']['filters'] =  $filters;
  $data['product']['products_options'] = $products_options;
  $data['product']['filters_post_selected'] = $selected_filters;
  $data['product']['products_options_post_selected'] = $selected_products_options;
  
}
//debug($data['product']['products_options']);

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="products_edit_add">
    
      <input type="hidden" name="product[product_id]" value="<?=(isset($data['product']['product_id']) && $data['product']['product_id'] != '' ? $data['product']['product_id'] : '');?>">

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
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-products"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="product[language][code]" value="<?=$data['product']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['product']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['product']['product_id']?>/<?=$language['language_id'];?>">
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

          <h2><?=$this->lang->line('product_info');?></h2>
          
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
              <td><input type="text" name="product[title]" value="<?=(isset($data['product']['title']) ? $data['product']['title'] : '');?>"></td>
            </tr>
            
            <?php if(($this->config->item('max_category_depth')+1) > 1): ?>
            
            <tr>
              
                <th><?=$this->lang->line('category_id')?></th>
                
                <td>
<?php if (isset($data['categories']) && $data['categories'] !== false) { ?>
                  <select name="product[category_id]">
                  <?php foreach ($data['categories'] as $category_id => $title) { ?>
                    <option value="<?php echo $category_id; ?>"<?php if (isset($data['product']['category_id']) && $data['product']['category_id'] == $category_id) echo ' selected="selected"'; ?>><?php echo $title; ?></option>
                  <?php } ?>
                  </select>
<?php } ?>
                </td>
                
            </tr>
            
            <?php else: ?>
            
              <input type="hidden" name="product[category_id]" value="0" >
            
            <?php endif; ?>
            
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if (isset($data['product']['sub_active'])): ?>
                
                  <?php if($data['product']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="product[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="product[sub_active]" value="0"><?=$this->lang->line('no')?>
                  
                  <?php else: ?>
                  
                    <input type="radio" name="product[sub_active]" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="product[sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="product[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                  <input type="radio" name="product[sub_active]" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class='hidden'>
              <th><?=$this->lang->line('highlight');?></th>
              <td>
                <?php if(isset($data['product']['highlight'])): ?>
                
                  <?php if($data['product']['highlight'] == 1): ?>
                  
                    <input type="radio" name="product[highlight]" value="1" checked="checked">Da
                    <input type="radio" name="product[highlight]" value="0">No
                  
                  <?php else: ?>
                  
                    <input type="radio" name="product[highlight]" value="1">Da
                    <input type="radio" name="product[highlight]" value="0" checked="checked">No
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="product[highlight]" value="1">Da
                  <input type="radio" name="product[highlight]" value="0" checked="checked">No
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class='hidden'>
              <th><?=$this->lang->line('date_created');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="product[date_created]" value="<?=(isset($data['product']['date_created']) ? date('d-m-Y', $data['product']['date_created']) : date('d-m-Y', time()));?>"></td>
            </tr>

            <tr class="hidden">
              <th><?=$this->lang->line('articlenumber');?></th>
              <td><input type="text" name="product[articlenumber]" value="<?=(isset($data['product']['articlenumber']) ? $data['product']['articlenumber'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('EAN');?></th>
              <td><input type="text" name="product[EAN]" value="<?=(isset($data['product']['EAN']) ? $data['product']['EAN'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('has_vat');?></th>
              <td>
                <?php if(isset($data['product']['has_vat'])): ?>
                
                  <?php if($data['product']['has_vat'] == 1): ?>
                  
                    <input type="radio" name="product[has_vat]" value="1" checked="checked">Da
                    <input type="radio" name="product[has_vat]" value="0">No
                  
                  <?php else: ?>
                  
                    <input type="radio" name="product[has_vat]" value="1">Da
                    <input type="radio" name="product[has_vat]" value="0" checked="checked">No
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="product[has_vat]" value="1" checked="checked">Da
                  <input type="radio" name="product[has_vat]" value="0">No
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('price');?></th>
              <td><input type="text" name="product[price]" value="<?=(isset($data['product']['price']) ? $data['product']['price'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('discount_percent');?></th>
              <td><input type="text" name="product[discount_percent]" value="<?=(isset($data['product']['discount_percent']) ? $data['product']['discount_percent'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('discount_price');?></th>
              <td><input type="text" name="product[discount_price]" value="<?=(isset($data['product']['discount_price']) ? $data['product']['discount_price'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('stock');?></th>
              <td><input type="text" name="product[stock]" value="<?=(isset($data['product']['stock']) ? $data['product']['stock'] : 1);?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('delivered');?></th>
              <td>
                <?php if(isset($data['product']['delivered'])): ?>
                
                  <?php if($data['product']['delivered'] == 1): ?>
                  
                    <input type="radio" name="product[delivered]" value="1" checked="checked">Da
                    <input type="radio" name="product[delivered]" value="0">No
                  
                  <?php else: ?>
                  
                    <input type="radio" name="product[delivered]" value="1">Da
                    <input type="radio" name="product[delivered]" value="0" checked="checked">No
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="product[delivered]" value="1" checked="checked">Da
                  <input type="radio" name="product[delivered]" value="0">No
                
                <?php endif; ?>
              </td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('tags');?></th>
              <td><input type="text" name="product[tags]" value="<?=(isset($data['product']['tags']) ? $data['product']['tags'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_description');?></th>
              <td><textarea class="count[120]" name="product[description]"><?=(isset($data['product']['description']) ? $data['product']['description'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('content_text');?></th>
              <td><textarea name="product[content]" class="editor"><?=(isset($data['product']['content']) ? $data['product']['content'] : '');?></textarea></td>
            </tr>

<?php foreach (range(1, 3) as $i) { ?>
            <tr class="hidden">
              <th>Gr&#259;maj / Valoare (<?php echo $i; ?>)</th>
              <td>
                <input type="text" name="product[option_<?php echo $i; ?>]" value="<?=(isset($data['product']['option_' . $i]) ? $data['product']['option_' . $i] : '');?>" style="width: 40%;">
                <input type="text" name="product[value_<?php echo $i; ?>]" value="<?=(isset($data['product']['value_' . $i]) ? $data['product']['value_' . $i] : '');?>" style="width: 40%; margin-left: 1%;">
              </td>
            </tr>
<?php } ?>

            <tr class='hidden'>
              <th><?=$this->lang->line('meta_title');?></th>
              <td><input class="count[63]" type="text" name="product[meta_title]" value="<?=(isset($data['product']['meta_title']) ? $data['product']['meta_title'] : '');?>"></td>
            </tr>
            
            <tr class='hidden'>
              <th><?=$this->lang->line('meta_desc');?></th>
              <td><input class="count[156]" type="text" name="product[meta_desc]" value="<?=(isset($data['product']['meta_desc']) ? $data['product']['meta_desc'] : '');?>"></td>
            </tr>
            
            <tr class='hidden'>
              <th><?=$this->lang->line('meta_keyw');?></th>
              <td><input class="count[256]" type="text" name="product[meta_keyw]" value="<?=(isset($data['product']['meta_keyw']) ? $data['product']['meta_keyw'] : '');?>"></td>
            </tr>
            
            <tr class='hidden'>
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="product[slug]" value="<?=(isset($data['product']['slug']) ? $data['product']['slug'] : '');?>"></td>
            </tr>
          
          </table>
        
        </div>
        
      </div>
      <?php if(isset($data['product']['products_options']) && $data['product']['products_options'] && count($data['product']['products_options']) > 0): ?>
            
      <div class="column hidden">
      
        <div class="subheader">
        
          <h2><?=$this->lang->line('category_options')?></h2>
          
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
            
            <?php 
            if(isset($data['product']['products_options']) && count($data['product']['products_options']) > 0)
            foreach($data['product']['products_options'] as $products_options): ?>
              <?php if($products_options['type'] == 1 || ($products_options['type'] != 1 && isset($products_options['subelements']) && count($products_options['subelements']) > 0)): ?>
                  <tr <?php echo (($products_options['superadmin'] == 1)?((permission_level() == 2)?"style='background:#ffede1'":"class='hidden'"):"");?>>
            
                    <th><?php echo $products_options['title'];?></th>
                    
                    <td>

                      <?php if($products_options['type'] == 1): ?>
                        <?php if(isset($data['product']['products_options_post_selected']) && isset($data['product']['products_options_post_selected'][$products_options['product_options_id']])){ ?>
                        <input type="text" name="product[products_options][<?=$products_options['product_options_id'];?>]" value="<?=(isset($data['product']['products_options_post_selected'][$products_options['product_options_id']]) ? $data['product']['products_options_post_selected'][$products_options['product_options_id']] : '');?>"><br>
                        <?php }else{ ?>
                        <input type="text" name="product[products_options][<?=$products_options['product_options_id'];?>]" value="<?=(isset($products_options['selected_value']) ? $products_options['selected_value'] : '');?>"><br>
                        <?php } ?>
                      <?php endif; ?>
                      
                      <?php if($products_options['type'] == 2 && count($products_options['subelements']) > 0): ?>
                          <select name="product[products_options][<?=$products_options['product_options_id'];?>][]" id='options<?=$products_options['product_options_id'];?>'> 
                            <option value=''></option>
                            <?php foreach($products_options['subelements'] as $products_options_element): ?>
                              <option value='<?php echo $products_options_element['product_options_item_id'];?>' <?php echo ((in_array($products_options_element['product_options_item_id'],$products_options['selected_value']) || (isset($data['product']['products_options_post_selected']) && isset($data['product']['products_options_post_selected'][$products_options['product_options_id']]) && in_array($products_options_element['product_options_item_id'],$data['product']['products_options_post_selected'][$products_options['product_options_id']])))?'selected="selected"':'');?>><?php echo $products_options_element['option_title'];?></option>
                            <?php endforeach; ?>
                          </select>
                      <?php endif; ?>
                      
                      <?php if($products_options['type'] == 3 && count($products_options['subelements']) > 0): ?>
                        <?php foreach($products_options['subelements'] as $products_options_element): ?>
                          <label><input type='checkbox' name="product[products_options][<?=$products_options['product_options_id'];?>][]" value='<?php echo $products_options_element['product_options_item_id'];?>' <?php echo ((in_array($products_options_element['product_options_item_id'],$products_options['selected_value']) || (isset($data['product']['products_options_post_selected']) && isset($data['product']['products_options_post_selected'][$products_options['product_options_id']]) && in_array($products_options_element['product_options_item_id'],$data['product']['products_options_post_selected'][$products_options['product_options_id']])))?'checked':'');?>>
                              <?php echo $products_options_element['option_title'];?></label>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      
                      <?php if($products_options['type'] == 4 && count($products_options['subelements']) > 0): ?>
                        <?php foreach($products_options['subelements'] as $products_options_element): ?>
                          <label><input type='radio' name="product[products_options][<?=$products_options['product_options_id'];?>][]" value='<?php echo $products_options_element['product_options_item_id'];?>' <?php echo ((in_array($products_options_element['product_options_item_id'],$products_options['selected_value']) || (isset($data['product']['products_options_post_selected']) && isset($data['product']['products_options_post_selected'][$products_options['product_options_id']]) && in_array($products_options_element['product_options_item_id'],$data['product']['products_options_post_selected'][$products_options['product_options_id']])))?'checked="checked"':'');?>>
                              <?php echo $products_options_element['option_title'];?></label>	
                        <?php endforeach; ?>
                      <?php endif; ?>
                      
                    </td>
                    
                  </tr>
                  <?php endif; ?>
              <?php endforeach; ?>
              
            
            
          </table>
        
        </div>
        
      </div>
      <?php endif; ?>			
      
      <?php if(isset($data['product']['filters']) && $data['product']['filters'] && count($data['product']['filters']) > 0): ?>
            
      <div class="column hidden">
      
        <div class="subheader">
        
          <h2>Filters</h2>
          
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
            
            
              <?php foreach($data['product']['filters'] as $filters): ?>	
                
                    <?php 
                    if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
                    
                      <tr>
                      
                        <th><?php echo $filters['title'];?></th>
                        
                        <td> 
                        
                          <?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
                            
                            <?php $x = 1; ?>
                            
                            <?php foreach($filters['subelements'] as $filters_element): ?>
                              
                              <label><input type='checkbox' name="product[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['product']['filters_post_selected']) && isset($data['product']['filters_post_selected'][$filters['filter_id']]) && in_array($filters_element['filter_item_id_number'],$data['product']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
                              
                                <?php echo $filters_element['filter_item_title'];?></label><br>
                              
                              <?php $x++; ?>
                              
                            <?php endforeach; ?>
                            
                          <?php endif; ?>		
                          
                        </td>
                        
                      </tr>	
                      
                    <?php endif; ?>			
                    
                  <?php endforeach; ?>
              
              
                
            
          </table>
        
        </div>
        
      </div>
      <?php endif; ?>		
      
      <?php if($this->url->segment(2) == 'edit'): ?>
      <div class="column hidden" id='overview'>
            
          <div class="subheader">
            
                <h2><?=$this->lang->line('prices');?></h2>
                
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
                    
                      <tr class='option_row'>
                      
                        <td style="width: 8%;padding-left:2%;">Nr</td>
                        <td style="width: 30%;">
                          <p><?=$this->lang->line('pieces');?></p>
                        </td>
                        <td style="width: 30%;">
                          <p><?=$this->lang->line('price');?></p>
                        </td>
                        <td style="width: 20%;text-align:center;">
                          <p><?=$this->lang->line('delete');?></p>
                        </td>
                      </tr>							
                    </thead>							
                    <tbody>							
                      <tr class='option_row_add hidden'>					
                        <td style="width: 8%;padding-left:2%;"><div class='nummer'><?=++$i;?></div></td>
                            
                        <td style="width: 30%">			
                          <p>
                            <input type="hidden" class='product_prices_id' name="prices[id][0]" value="1">
                            <input type="text" name="prices[pieces][0]" value="">
                          </p>
                        </td>
                        
                        <td style="width: 30%">			
                          <p>
                            <input type="text" name="prices[price][0]" value="">
                          </p>
                        </td>
                            
                        <td style="width: 20%" style='text-align:center;'>		
                          <a href='#'  class="price_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
                        </td>
                          
                      </tr>
                      <?php $i = 0; ?>
                      <?php if(!empty($data['prices'])): ?>
                      <?php 
                      foreach($data['prices'] as $price): ?>
                        
                        <tr class='option_row'>					
                          <td style="width: 8%;padding-left:2%;"><?=++$i;?></td>
                          <td style="width: 30%;">
                            <p>
                              <input type="hidden" class='product_prices_id' name="prices[id][<?php echo $price['product_prices_id'];?>]" value="<?=$price['product_prices_id'];?>">
                              <input type="text" name="prices[pieces][<?php echo $price['product_prices_id'];?>]" value="<?=(isset($price['pieces']) ? $price['pieces'] : '');?>">
                            </p>
                          </td>
                          <td style="width: 30%;">
                            <p>
                              <input type="text" name="prices[price][<?php echo $price['product_prices_id'];?>]" value="<?=(isset($price['price']) ? $price['price'] : '');?>">
                            </p>
                          </td>
                          <td style="width: 20%">
                            <a href='#'  class="price_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
                          </td>
                        
                        </tr>
                        
                      <?php endforeach; ?>
                      <?php endif; ?>
                      
                      <tr>
                        <td colspan='5' style='padding-left:2%;'>
                          <div class="orange_button" id='overview'>
                            <div class="orange_button_left"></div>
                            <div class="orange_button_con">
                              <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
                              <div class="orange_button_space"></div>
                              <a href="#" onclick="return false;" class='font_white price_add'><?=$this->lang->line('price_add');?></a>
                            </div>
                            <div class="orange_button_right"></div>
                          </div>
                        </td>
                      </tr>	
                    </tbody>
                  
                  </table>
                
              </div>
            </div>

      <?php endif; ?>
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Cover image</h2>
          
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
        
          <?php if(!empty($data['media']['photo'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['media']['photo'] as $media): ?>
              <div class="thumb">
              
                <img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>">
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['product']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['product']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_aDax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['product']['language_id'];?>" class="modal_aDax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
      
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2>Gallery</h2>
          
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
              
                <img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>">
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
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['product']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['product']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_aDax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['product']['language_id'];?>" class="modal_aDax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
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
      
      
      <div class="column hidden">
      
        <div class="subheader">

          <h2><?=$this->lang->line('product_docs');?></h2>
          
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
                <textarea maxlength="110" name="twitter_post_text"><?=(isset($data['product']['description']) ? str_shorten($data['product']['description'], 110) : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="twitter_post_link" value="<?=(isset($data['product']['slug']) ? $this->googleurl->makeRequest($data['product']['slug']) : '');?>">
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
                <textarea name="facebook_post_text"><?=(isset($data['product']['description']) ? $data['product']['description'] : '');?></textarea>
                <input style="margin-bottom: 10px;" type="text" readonly name="facebook_post_link" value="<?=(isset($data['product']['slug']) ? $this->googleurl->makeRequest($data['product']['slug']) : '');?>">
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

<script type="text/Davascript">
  MIN_IMG_W =	<?=PRODUCTS_CROP_MAX_W;?>;
  MIN_IMG_H = <?=PRODUCTS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>