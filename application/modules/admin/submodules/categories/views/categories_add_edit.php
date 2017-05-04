<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data);

if(!empty($_POST))
{
	$filters = array();
	$products_options = array();
	if($_POST['category']['title'] == ''){
		$filters = $data['category']['filters'];
		$products_options = $data['category']['products_options'];
	}
	$data['category'] = $_POST['category'];
	if(isset($_POST['category']['filters'])) $selected_filters = $_POST['category']['filters'];
	else $selected_filters = '';
	if(isset($_POST['category']['products_options'])) $selected_products_options = $_POST['category']['products_options'];
	else $selected_products_options = '';
	$data['category']['filters'] =  $filters;
	$data['category']['products_options'] = $products_options;
	$data['category']['filters_post_selected'] = $selected_filters;
	$data['category']['products_options_post_selected'] = $selected_products_options;
	
}
?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="category_edit_add">
		
			<input type="hidden" name="category[category_id]" value="<?=(isset($data['category']['category_id']) ? $data['category']['category_id'] : '');?>">
			
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('save_category');?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $this->url->segment(3);?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-info"></div>
							<h1><?=$this->lang->line('add_category');?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="category[language][code]" value="<?=$data['category']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['category']['language']['code']?>"></div>
							<h1><?=$this->lang->line('edit_category');?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(3);?>/<?=$data['category']['category_id']?>/<?=$language['language_id'];?>">
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
				
					<h2><?=$this->lang->line('category_info');?></h2>
					
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
						
						<?php if($this->config->item('max_category_depth') > 1): ?>
						<tr class="hidden">
							
								<th><?=$this->lang->line('parent_id')?></th>
								
								<td>
									<select name="category[parent_id]">
									
											<option value="0" <?=(!isset($data['category']['parent_id']) ? 'selected="selected"' : '');?>><?=$this->lang->line('choose_category')?></option>
											
											<?php foreach($data['drop_down'] as $category): ?>
											
											<?php if($data['category']['category_id'] != $category['category_id'] &&  $data['count_children']['sub_sub'] < 1): ?>
											
												<option <?=(isset($data['category']['parent_id']) && $data['category']['parent_id'] == $category['category_id'] ? 'selected="selected"' : '');?> value="<?=$category['category_id']?>"><?=$category['title']?></option>
											
											<?php else: ?>
												
												<option disabled="disabled" value="<?=$category['category_id']?>"><?=$category['title']?></option>
											
											<?php endif; ?>
											
												<?php if($this->config->item('max_category_depth') > 2): ?>
											
													<?php foreach($category['children'] as $child): ?>
														
														<?php if($data['category']['category_id'] != $child['category_id'] && $data['count_children']['sub'] < 1): ?>
															
															<option <?=(isset($data['category']['parent_id']) && $data['category']['parent_id'] == $child['category_id'] ? 'selected="selected"' : '');?> value="<?=$child['category_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['title']?></option>
														
														<?php elseif($data['count_children']['sub'] > 0): ?>
															
															<option disabled="disabled" value="<?=$child['category_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['title']?></option>
														
														<?php else: ?>
															
															<option disabled="disabled" value="<?=$child['category_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['title']?></option>
														
														<?php endif; ?>
													
													<?php endforeach; ?>
													
												<?php endif; ?>
												
											<?php endforeach; ?>

									</select>
									
								</td>
								
							</tr>
						
						<?php else: ?>
						
							<input type="hidden" name="category[parent_id]" value="0" >
						
						<?php endif; ?>

						<tr>
							<th><?=$this->lang->line('content_title');?></th>
							<td><input type="text" name="category[title]" value="<?=(isset($data['category']['title']) ? $data['category']['title'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('active');?></th>
							<td>
								<?php if(isset($data['category']['sub_active'])): ?>
								
									<?php if($data['category']['sub_active'] == 1): ?>
									
										<input type="radio" name="category[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
										<input type="radio" name="category[sub_active]" value="0"><?=$this->lang->line('no')?>
									
									<?php else: ?>
									
										<input type="radio" name="category[sub_active]" value="1"><?=$this->lang->line('yes')?>
										<input type="radio" name="category[sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="category[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
									<input type="radio" name="category[sub_active]" value="0"><?=$this->lang->line('no')?>
								
								<?php endif; ?>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_description');?></th>
							<td><textarea name="category[description]"><?=(isset($data['category']['description']) ? $data['category']['description'] : '');?></textarea></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_text');?></th>
							<td><textarea name="category[content]" class="editor"><?=(isset($data['category']['content']) ? $data['category']['content'] : '');?></textarea></td>
						</tr>
<?php foreach (range(1, 3) as $i) { ?>
            <tr class="hidden">
              <th>Gr&#259;maj / Valoare (<?php echo $i; ?>)</th>
              <td>
                <input type="text" name="category[option_<?php echo $i; ?>]" value="<?=(isset($data['category']['option_' . $i]) ? $data['category']['option_' . $i] : '');?>" style="width: 40%;">
                <input type="text" name="category[value_<?php echo $i; ?>]" value="<?=(isset($data['category']['value_' . $i]) ? $data['category']['value_' . $i] : '');?>" style="width: 40%; margin-left: 1%;">
              </td>
            </tr>
<?php } ?>
						
						<tr class="hidden">
							<th><?=$this->lang->line('meta_title');?></th>
							<td><input class="count[63]" type="text" name="category[meta_title]" value="<?=(isset($data['category']['meta_title']) ? $data['category']['meta_title'] : '');?>"></td>
						</tr>
						
						<tr class="hidden">
							<th><?=$this->lang->line('meta_desc');?></th>
							<td><input class="count[156]" type="text" name="category[meta_desc]" value="<?=(isset($data['category']['meta_desc']) ? $data['category']['meta_desc'] : '');?>"></td>
						</tr>
						
						<tr class="hidden">
							<th><?=$this->lang->line('meta_keyw');?></th>
							<td><input class="count[256]" type="text" name="category[meta_keyw]" value="<?=(isset($data['category']['meta_keyw']) ? $data['category']['meta_keyw'] : '');?>"></td>
						</tr>
						<tr class="hidden">
							<th><?=$this->lang->line('slug');?></th>
							<td><input type="text" name="category[slug]" value="<?=(isset($data['category']['slug']) ? $data['category']['slug'] : '');?>"></td>
						</tr>
					
					</table>
				
				</div>
				
			</div>
			
			<?php if(isset($data['category']['filters']) && count($data['category']['filters']) > 0 || $this->url->segment(3) == 'products'): ?>
			
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
						<?php if($this->url->segment(3) == 'products'): ?>
						
						<tr>
							<th><?=$this->lang->line('discount_primary');?></th>
							<td><input type="checkbox" name="category[discount_primary]" value="1" <?=((isset($data['category']['discount_primary']) && $data['category']['discount_primary'] == 1) ? 'checked' : '');?>></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('discount_percent');?></th>
							<td><input type="text" name="category[discount_percent]" value="<?=(isset($data['category']['discount_percent']) ? $data['category']['discount_percent'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('discount_price');?></th>
							<td><input type="text" name="category[discount_price]" value="<?=(isset($data['category']['discount_price']) ? $data['category']['discount_price'] : '');?>"></td>
						</tr>
						
						<?php if(isset($data['category']['products_options']) && count($data['category']['products_options']) > 0): ?>
								<tr>
									<th><?=$this->lang->line('product_options');?></th>
									<td>
										<?php foreach($data['category']['products_options'] as $products_options): ?>

											<?php if($products_options['selected'] == 1 || (isset($data['category']['products_options_post_selected']) && $data['category']['products_options_post_selected'] && count($data['category']['products_options_post_selected']) > 0 && in_array($products_options['product_options_id'],$data['category']['products_options_post_selected']))): ?>
											<label><input type="checkbox" name="category[products_options][]" value="<?=$products_options['product_options_id'];?>" checked="checked"> <?php echo $products_options['title'];?></label><br>
											<?php else: ?>
											<label><input type="checkbox" name="category[products_options][]" value="<?=$products_options['product_options_id'];?>"> <?php echo $products_options['title'];?></label><br>
											<?php endif; ?>
										<?php endforeach; ?>
									</td>
								</tr>
							<?php endif; ?>
						<?php endif; ?>
						
						<?php if($this->url->segment(3) == 'products'): ?>
							<?php if(isset($data['category']['filters']) && count($data['category']['filters']) > 0): 
								$filter_value = '';
								?>
							<tr>
								<th><?=$this->lang->line('filters');?></th>
								<td>
									<?php foreach($data['category']['filters'] as $filters): ?>
										<?php
										if($filter_value != $filters['filer_title'])
											echo "<b>".$filters['filer_title']."</b><br>";
										$filter_value = $filters['filer_title'];
										?>
										<?php if($filters['selected'] == 1 || (isset($data['category']['filters_post_selected']) && is_array($data['category']['filters_post_selected']) && in_array($filters['filter_item_id'],$data['category']['filters_post_selected']))): ?>
										<label><input type="checkbox" name="category[filters][]" value="<?=$filters['filter_item_id'];?>" checked="checked"> <?php echo $filters['title'];?></label><br>
										<?php else: ?>
										<label><input type="checkbox" name="category[filters][]" value="<?=$filters['filter_item_id'];?>"> <?php echo $filters['title'];?></label><br>
										<?php endif; ?>
									<?php endforeach; ?>
								</td>
							</tr>
							<?php endif; ?>		
						<?php else: ?>		
							<?php if(isset($data['category']['filters']) && count($data['category']['filters']) > 0): 
								//debug($data['category']['filters']);
								$filter_value = '';
								?>
								<tr>
									<th><?=$this->lang->line('filters');?></th>
									<td>
										<?php foreach($data['category']['filters'] as $filters): ?>
											<?php
											if($filter_value != $filters['filter_id']){
												?>
												<?php if($filters['selected'] == 1 || (isset($data['category']['filters_post_selected']) && in_array($filters['filter_id'],$data['category']['filters_post_selected']))): ?>
												<label><input type="checkbox" name="category[filters][]" value="<?=$filters['filter_id'];?>" checked="checked"> <?php echo $filters['filer_title'];?></label><br>
												<?php else: ?>
												<label><input type="checkbox" name="category[filters][]" value="<?=$filters['filter_id'];?>"> <?php echo $filters['filer_title'];?></label><br>
												<?php endif; 
											}
											$filter_value = $filters['filter_id'];
											
										 endforeach; ?>
									</td>
								</tr>
								<?php endif; ?>		
							<?php endif; ?>				
					</table>
				
				</div>
				
			</div>
			<?php endif; ?>			
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
				
					<?php if(!empty($data['media'])): ?>
					
						<?php $x = 0; ?>
						<?php foreach($data['media'] as $media): ?>
							<div class="thumb">
							
								<img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>">
								<input type="hidden" name="media[][media_id]" value="<?=$media['media_id'];?>">
								
								<a href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $this->url->segment(2) . '/' . $this->url->segment(3) . '/' . $media['media_id'] . '/' . $media['table_id'] . '/' . $this->url->segment(5);?>" class="delete">
									<img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
								</a>
								
								<div class="set_thumbnail">
								
									<input type="radio" name="set_thumbnail" value="<?=$media['media_id'];?>" <?=($media['album_thumb'] == 1 ? 'checked="checked"' : '');?>><small> Thumb</small>
								
								</div>
								
								<div class="filename">
								
									<?=(strlen($media['filename']) > 15 ? substr($media['filename'], 0, 15) . '...' : $media['filename']);?>
									
								</div>
								
								<div class="order_media">
								
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER;?>/order_media/<?=$this->url->segment(3);?>/left/<?=$media['table_id'];?>/<?=$data['category']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER;?>/order_media/<?=$this->url->segment(3);?>/right/<?=$media['table_id'];?>/<?=$data['category']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
								
								</div>
								
								<div class="edit_image">
								
									<img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
									<a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['category']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
								
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
			
		</form>
	
	</div>

</div>

<script type="text/javascript">
	MIN_IMG_W =	<?=CATEGORIES_CROP_MAX_W;?>;
	MIN_IMG_H = <?=CATEGORIES_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>