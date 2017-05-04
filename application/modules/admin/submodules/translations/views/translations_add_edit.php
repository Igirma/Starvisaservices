<?php require_once(ELEM_DIR . 'admin_header.php');
?>

<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data">
					
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">

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
				
					<input type="hidden" name="form[page_content][page_id]" value="<?=$data['page']['form']['page_content']['page_id'];?>">
				
					<table>
						
						<!-- Page content form fields -->
						
						<tr>
							
							<th>Naam</th>
							
							<td><input type="text" name="form[page_content][menu_title]" value="<?=(isset($data['page']['form']['page_content']['menu_title']) ? $data['page']['form']['page_content']['menu_title'] : '');?>" ></td>
						
						</tr>
						
						
						<tr>
							
							<th>Vertaling</th>
							
							<td><input type="text" name="form[page_content][content_text]"><?=(isset($data['page']['form']['page_content']['content_text']) ? $data['page']['form']['page_content']['content_text'] : '');?></textarea></td>
						
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
			
			
			
		</form>
		
	</div>

</div>

<script type="text/javascript">
	MIN_IMG_W =	<?=PAGES_CROP_MAX_W;?>;
	MIN_IMG_H = <?=PAGES_CROP_MAX_H;?>;
</script>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>