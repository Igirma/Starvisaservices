<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data);

if(!empty($_POST))
{
	$data['/filters'] = $_POST['filter'];
	/*
	foreach($data['posted_filters'] as $opt => $value){
		if($opt != 'options')
			$data['filters'][$opt] = $value;
		//else
			//print_r($value);
	}
	*/
	$data['filters']['filter_item_id'] = $_POST['filter'];
}
?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="filters_edit_add">
		
			<input type="hidden" name="filter[filter_item_id]" value="<?=(isset($data['filters']['filter_item_id']) ? $data['filters']['filter_item_id'] : '');?>">
			
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('filters_menu_save');?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $this->url->segment(3);?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-info"></div>
							<h1><?=$this->lang->line('filters_add');?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="filter[language][code]" value="<?=$data['filter']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['filter']['language']['code']?>"></div>
							<h1><?=$this->lang->line('filters_edit');?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(3);?>/<?=$data['filters']['filter_id']?>/<?=$language['language_id'];?>">
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
				
					<h2><?=$this->lang->line('filters_info');?></h2>
					
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
							<th><?=$this->lang->line('filters_title');?></th>
							<td><input type="text" name="filter[title]" value="<?=(isset($data['filters']['title']) ? $data['filters']['title'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('active');?></th>
							<td>
								<?php if(isset($data['filters']['sub_active'])): ?>
								
									<?php if($data['filters']['sub_active'] == 1): ?>
									
										<input type="radio" name="filter[sub_active]" value="1" checked="checked">Ja
										<input type="radio" name="filter[sub_active]" value="0">Nee
									
									<?php else: ?>
									
										<input type="radio" name="filter[sub_active]" value="1">Ja
										<input type="radio" name="filter[sub_active]" value="0" checked="checked">Nee
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="filter[sub_active]" value="1" checked="checked">Ja
									<input type="radio" name="filter[sub_active]" value="0">Nee
								
								<?php endif; ?>
							</td>
						</tr>
						
						<?php if($this->url->segment(2) == 'add'): ?>

							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
								<td>
									<input type="text" name="filter[options][1]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							
							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>2</span></th>
								<td>
									<input type="text" name="filter[options][2]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
								<td>
									<input type="text" name="filter[options][3]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							<tr class='option_row hidden'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
								<td>
									<input type="text" name="filter[options][]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>

						<?php endif; ?>
						
						<?php if($this->url->segment(2) == 'edit'): ?>
							
							<?php if(!empty($data['filters']['options'])): ?>
								<?php
								$filter_id = 0;
								?>
								<?php foreach($data['filters']['options'] as $k => $options): ?>
									<?php if($_POST && $k == (count($data['filters']['options'])) && $this->url->segment(5) != $this->config->item('default_language')) {
										continue;
									}
									?>
									<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span><?=$filter_id+1;?></span></th>
									<td>
										<input type="text" name="filter[options][<?=$options['filter_item_id'];?>]" class="product_options_input" value="<?=(isset($options['title']) ? $options['title'] : '');?>">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<?php 
								$filter_id++;
								endforeach; ?>
								<tr class='option_row hidden'>
									<th><?=$this->lang->line('option');?> <span class='nummer'><?=$filter_id;?></span></th>
									<td>
										<input type="text" name="filter[options][]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
							<?php else: ?>
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
									<td>
										<input type="text" name="filter[options][1]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>2</span></th>
									<td>
										<input type="text" name="filter[options][2]" value="" class="product_options_input"> 
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
									<td>
										<input type="text" name="filter[options][3]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<tr class='option_row hidden'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
									<td>
										<input type="text" name="filter[options][]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>

							<?php endif; ?>
							
						
						<?php endif; ?>
						
						<tr>
							<th></th>
							<td>
								<div class="orange_button" id='overview'>
									<div class="orange_button_left"></div>
									<div class="orange_button_con">
										<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
										<div class="orange_button_space"></div>
										<a href="#" onclick="return false;" class='font_white option_add'><?=$this->lang->line('option_add');?></a>
									</div>
									<div class="orange_button_right"></div>
								</div>
							</td>
						</tr>
						
					
					</table>
				
				</div>
				
			</div>
			
		</form>
	
	</div>

</div>


<?php require_once ELEM_DIR . 'admin_footer.php'; ?>