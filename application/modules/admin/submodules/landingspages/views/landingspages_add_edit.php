<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
	$data['landingspage']['form']['landingspage_content'] = $_POST['form']['landingspage_content'];
	$data['landingspage']['form']['landingsmobile_content'] = $_POST['form']['landingsmobile_content'];
	$data['landingspage']['external'] = $_POST['external'];
}

?>

<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data">
					
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a class="preview" href="<?=SITE_URL . landingspageSubSlug($data['landingspage']['form']['landingspage_content']['landingspage_id']) . '/' . $_SESSION['login_salt']?>"><?=$this->lang->line('preview')?></a>
				</div>
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="page[language][0][code]" value="<?=$data['landingspage']['language'][0]['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['landingspage']['language'][0]['code']?>"></div>
							<h1><?=$this->lang->line('header_edit');?></h1>
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['landingspage']['form']['landingspage_content']['landingspage_id'];?>/<?=$language['language_id'];?>">
											<span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>

								<?php endforeach; ?>
								
							</div>
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-<?=CONTROLLER?>"></div>
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

						<tr>
							
							<th><?=$this->lang->line('menu_title')?></th>
							
							<td><input type="text" name="form[landingspage_content][menu_title]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['menu_title']) ? $data['landingspage']['form']['landingspage_content']['menu_title'] : '');?>" ></td>
						
						</tr>
						
						<?php if($this->config->item('max_category_depth') > 0 && isset($data['drop_down_category']) && !empty($data['drop_down_category'])): ?>
							
							<tr>
							
								<th><?=$this->lang->line('category_id')?></th>
								
								<td>
									
									<div class='padded_content'>
										<?php foreach($data['drop_down_category'] as $category): ?>
												
											<label><input type='checkbox' name='landingspage[category_id][]' value='<?php echo $category['category_id'];?>' <?=(isset($data['landingspage']['category_id']) && in_array($category['category_id'], $data['landingspage']['category_id']) ? 'checked="checked"' : '');?> onchange="$(this).parents('form').submit();">&nbsp;&nbsp;<?=$category['title']?></label><br>
														
											<?php if(($this->config->item('max_category_depth')+1) > 2): ?>
														
												<?php foreach($category['children'] as $child): ?>
																	
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type='checkbox' name='landingspage[category_id][]' <?=(isset($data['landingspage']['category_id']) && in_array($child['category_id'], $data['landingspage']['category_id']) ? 'checked="checked"' : '');?> value="<?=$child['category_id']?>" onchange="$(this).parents('form').submit();">&nbsp;&nbsp;<?=$child['title']?></label><br>
												<?php endforeach; ?>
																
											<?php endif; ?>
														
										<?php endforeach; ?>
									</div>
									
									
								</td>
								
							</tr>
						
						<?php else: ?>
						
							<input type="hidden" name="category_id" value="0" >
						
						<?php endif; ?>
						
						<?php if($this->config->item('max_page_depth') > 1): ?>
							
							<tr>
							
								<th><?=$this->lang->line('parent_id')?></th>
								
								<td>
									<select name="parent_id">
									
											<option value="0" <?=(!isset($data['landingspage']['parent_id']) ? 'selected="selected"' : '');?>><?=$this->lang->line('choose_page')?></option>
											
											<?php foreach($data['drop_down'] as $page): ?>
											
											<?php if($data['landingspage']['form']['landingspage_content']['landingspage_id'] != $page['landingspage_id'] &&  $data['count_children']['sub_sub'] < 1): ?>
											
												<option <?=(isset($data['landingspage']['parent_id']) && $data['landingspage']['parent_id'] == $page['landingspage_id'] ? 'selected="selected"' : '');?> value="<?=$page['landingspage_id']?>"><?=$page['menu_title']?></option>
											
											<?php else: ?>
												
												<option disabled="disabled" value="<?=$page['landingspage_id']?>"><?=$page['menu_title']?></option>
											
											<?php endif; ?>
											
												<?php if($this->config->item('max_page_depth') > 2): ?>
											
													<?php foreach($page['children'] as $child): ?>
														
														<?php if($data['landingspage']['form']['landingspage_content']['landingspage_id'] != $child['landingspage_id'] && $data['count_children']['sub'] < 1): ?>
															
															<option <?=(isset($data['landingspage']['parent_id']) && $data['landingspage']['parent_id'] == $child['landingspage_id'] ? 'selected="selected"' : '');?> value="<?=$child['landingspage_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
														
														<?php elseif($data['count_children']['sub'] > 0): ?>
															
															<option disabled="disabled" value="<?=$child['landingspage_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
														
														<?php else: ?>
															
															<option disabled="disabled" value="<?=$child['landingspage_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['menu_title']?></option>
														
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
								
								<?php if(isset($data['landingspage']['external'])): ?>
									
									<?php if($data['landingspage']['external'] == 1): ?>
									
										<input type="radio" name="external" class="landings" value="1" checked="checked"><?=$this->lang->line('yes')?>
										<input type="radio" name="external" class="landings" value="0"><?=$this->lang->line('no')?>
										
									<?php else: ?>
										
										<input type="radio" name="external" class="landings" value="1"><?=$this->lang->line('yes')?>
										<input type="radio" name="external" class="landings" value="0" checked="checked"><?=$this->lang->line('no')?>
										
									<?php endif; ?>
								
								<?php else: ?>
									
									<input type="radio" name="external" class="landings" value="1"><?=$this->lang->line('yes')?>
									<input type="radio" name="external" class="landings" value="0" checked="checked"><?=$this->lang->line('no')?>
									
								<?php endif; ?>
								
							</td>
							
						</tr>
					
						<tr>
							<th><?=$this->lang->line('sub_active');?></th>
							<td>
								<?php if(isset($data['landingspage']['form']['landingspage_content']['sub_active'])): ?>
								
									<?php if($data['landingspage']['form']['landingspage_content']['sub_active'] == 1): ?>
									
										<input type="radio" name="form[landingspage_content][sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
										<input type="radio" name="form[landingspage_content][sub_active]" value="0"><?=$this->lang->line('no')?>
									
									<?php else: ?>
									
										<input type="radio" name="form[landingspage_content][sub_active]" value="1"><?=$this->lang->line('yes')?>
										<input type="radio" name="form[landingspage_content][sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="form[landingspage_content][sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
									<input type="radio" name="form[landingspage_content][sub_active]" value="0"><?=$this->lang->line('no')?>
								
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
				
					<input type="hidden" name="form[landingspage_content][landingspage_id]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['landingspage_id']) ? $data['landingspage']['form']['landingspage_content']['landingspage_id'] : '');?>">
				
					<table>
						
						<!-- Page content form fields -->
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_title')?></th>
							
							<td><input type="text" name="form[landingspage_content][overview_title]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['overview_title']) ? $data['landingspage']['form']['landingspage_content']['overview_title'] : '');?>" ></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_description')?></th>
							
							<td><textarea name="form[landingspage_content][overview_description]"><?=(isset($data['landingspage']['form']['landingspage_content']['overview_description']) ? $data['landingspage']['form']['landingspage_content']['overview_description'] : '');?></textarea></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_text')?></th>
							
							<td><input type="text" name="form[landingspage_content][overview_text]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['overview_text']) ? $data['landingspage']['form']['landingspage_content']['overview_text'] : '');?>" ></td>
						
						</tr>

						<tr>
							
							<th><?=$this->lang->line('content_title')?></th>
							
							<td><input type="text" name="form[landingspage_content][content_title]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['content_title']) ? $data['landingspage']['form']['landingspage_content']['content_title'] : '');?>" ></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('content_description')?></th>
							
							<td><textarea name="form[landingspage_content][content_description]"><?=(isset($data['landingspage']['form']['landingspage_content']['content_description']) ? $data['landingspage']['form']['landingspage_content']['content_description'] : '');?></textarea></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('content_text')?></th>
							
							<td><textarea class="editor" name="form[landingspage_content][content_text]"><?=(isset($data['landingspage']['form']['landingspage_content']['content_text']) ? $data['landingspage']['form']['landingspage_content']['content_text'] : '');?></textarea></td>
						
						</tr>

						<tr class="hidden">
							
							<th><?=$this->lang->line('ex_name')?></th>
							
							<td><input type="text" name="form[landingspage_content][ex_name]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['ex_name']) ? $data['landingspage']['form']['landingspage_content']['ex_name'] : '');?>" ></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('ex_url')?></th>
							
							<td><input type="text" name="form[landingspage_content][ex_url]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['ex_url']) ? $data['landingspage']['form']['landingspage_content']['ex_url'] : 'http://');?>" ></td>
						
						</tr>		
						
						<tr>
							
							<th><?=$this->lang->line('slug')?></th>
							
							<td><input type="text" name="form[landingspage_content][slug]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['slug']) ? $data['landingspage']['form']['landingspage_content']['slug'] : '');?>" ></td>
						
						</tr>
						
						<tr>
						
							<th>
								<?=$this->lang->line('meta_title');?>
								<br>
								
							</th>
							
							<td>
								<input class="count[63]" type="text" name="form[landingspage_content][meta_title]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['meta_title']) ? $data['landingspage']['form']['landingspage_content']['meta_title'] : '');?>" >
							</td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('meta_desc')?></th>
							
							<td><textarea class="count[156]" name="form[landingspage_content][meta_desc]"><?=(isset($data['landingspage']['form']['landingspage_content']['meta_desc']) ? $data['landingspage']['form']['landingspage_content']['meta_desc'] : '');?></textarea></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('meta_keyw')?></th>
							
							<td><input class="count[256]" type="text" name="form[landingspage_content][meta_keyw]" value="<?=(isset($data['landingspage']['form']['landingspage_content']['meta_keyw']) ? $data['landingspage']['form']['landingspage_content']['meta_keyw'] : '');?>" ></td>
						
						</tr>
						
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
							
							<td><input type="text" name="form[landingsmobile_content][menu_title]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['menu_title']) ? $data['landingspage']['form']['landingsmobile_content']['menu_title'] : '')?>"></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_title');?></th>
							
							<td><input type="text" name="form[landingsmobile_content][overview_title]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['overview_title']) ? $data['landingspage']['form']['landingsmobile_content']['overview_title'] : '')?>" ></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_description')?></th>
							
							<td><textarea name="form[landingsmobile_content][overview_description]"><?=(isset($data['landingspage']['form']['landingsmobile_content']['overview_description']) ? $data['landingspage']['form']['landingsmobile_content']['overview_description'] : '');?></textarea></td>
						
						</tr>
						
						<tr class="hidden">
							
							<th><?=$this->lang->line('overview_text')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][overview_text]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['overview_text']) ? $data['landingspage']['form']['landingsmobile_content']['overview_text'] : '');?>" ></td>
						
						</tr>

						<tr>
							
							<th><?=$this->lang->line('content_title')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][content_title]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['content_title']) ? $data['landingspage']['form']['landingsmobile_content']['content_title'] : '');?>" ></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('content_description')?></th>
							
							<td><textarea name="form[landingsmobile_content][content_description]"><?=(isset($data['landingspage']['form']['landingsmobile_content']['content_description']) ? $data['landingspage']['form']['landingsmobile_content']['content_description'] : '');?></textarea></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('content_text')?></th>
							
							<td><textarea class="editor" name="form[landingsmobile_content][content_text]"><?=(isset($data['landingspage']['form']['landingsmobile_content']['content_text']) ? $data['landingspage']['form']['landingsmobile_content']['content_text'] : '');?></textarea></td>
						
						</tr>

						<tr class="hidden">
							
							<th><?=$this->lang->line('ex_name')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][ex_name]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['ex_name']) ? $data['landingspage']['form']['landingsmobile_content']['ex_name'] : '');?>" ></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('ex_url')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][ex_url]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['ex_url']) ? $data['landingspage']['form']['landingsmobile_content']['ex_url'] : 'http://');?>" ></td>
						
						</tr>		
						
						<tr>
							
							<th><?=$this->lang->line('slug')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][slug]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['slug']) ? $data['landingspage']['form']['landingsmobile_content']['slug'] : '');?>" ></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('meta_title')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][meta_title]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['meta_title']) ? $data['landingspage']['form']['landingsmobile_content']['meta_title'] : '');?>" ></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('meta_desc')?></th>
							
							<td><textarea name="form[landingsmobile_content][meta_desc]"><?=(isset($data['landingspage']['form']['landingsmobile_content']['meta_desc']) ? $data['landingspage']['form']['landingsmobile_content']['meta_desc'] : '');?></textarea></td>
						
						</tr>
						
						<tr>
							
							<th><?=$this->lang->line('meta_keyw')?></th>
							
							<td><input type="text" name="form[landingsmobile_content][meta_keyw]" value="<?=(isset($data['landingspage']['form']['landingsmobile_content']['meta_keyw']) ? $data['landingspage']['form']['landingsmobile_content']['meta_keyw'] : '');?>" ></td>
						
						</tr>
					
					</table>
					
				</div>
			
			</div>

			<?php
			if(haveFilters(CONTROLLER))
			if(isset($data['landingspage']['filters']) && $data['landingspage']['filters'] && count($data['landingspage']['filters']) > 0 
				&& ((isset($data['landingspage']['category_id']) && haveCategories(CONTROLLER)) || !haveCategories(CONTROLLER)))
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
							//debug($data['landingspage']['filters']);
							?>
								<?php foreach($data['landingspage']['filters'] as $filters): ?>	
								
								<?php 
								if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
								
									<tr>
									
										<th><?php echo $filters['title'];?></th>
										
										<td> 
										
											<?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
												
												<?php $x = 1; ?>
												
												<?php foreach($filters['subelements'] as $filters_element): ?>
													
													<label><input type='checkbox' name="landingspage[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['landingspage']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['landingspage']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
													
														<?php echo $filters_element['filter_item_title'];?></label><br>
													
													<?php $x++; ?>
													
												<?php endforeach; ?>
												
											<?php endif; ?>		
											
										</td>
										
									</tr>	
									
								<?php endif; ?>			
								
							<?php endforeach; ?>
							
							<?php else: ?>		
									
								<?php foreach($data['landingspage']['filters'] as $filters): ?>	
								
										<?php 
										if(isset($filters['subelements']) && count($filters['subelements']) > 0 && is_array($filters['subelements'])): ?>
										
											<tr>
											
												<th><?php echo $filters['title'];?></th>
												
												<td> 
												
													<?php if(isset($filters['subelements']) && count($filters['subelements']) > 0): ?>
														
														<?php $x = 1; ?>
														
														<?php foreach($filters['subelements'] as $filters_element): ?>
															
															<label><input type='checkbox' name="landingspage[filters][<?=$filters_element['filter_id']?>][]" value='<?=$filters_element['filter_item_id_number']?>' <?php echo ((in_array($filters_element['filter_item_id_number'],$filters['selected']) || (isset($data['landingspage']['filters_post_selected']) && in_array($filters_element['filter_item_id_number'],$data['landingspage']['filters_post_selected'][$filters['filter_id']])))?'checked':'');?>>
															
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
					
					<?php if(isset($data['landingspage']['media'])): ?>
					
						<?php $x = 0; ?>
						<?php foreach($data['landingspage']['media'] as $media): ?>
							<div class="thumb">
							
								<a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['landingspage']['form']['landingspage_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
								
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['landingspage']['form']['landingspage_content']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['landingspage']['form']['landingspage_content']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
								
								</div>
								
								<div class="edit_image">
								
									<img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
									<a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['landingspage']['form']['landingspage_content']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
								
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

		</form>
		
	</div>

</div>

<script type="text/javascript">
	MIN_IMG_W =	<?=PAGES_CROP_MAX_W;?>;
	MIN_IMG_H = <?=PAGES_CROP_MAX_H;?>;
</script>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>