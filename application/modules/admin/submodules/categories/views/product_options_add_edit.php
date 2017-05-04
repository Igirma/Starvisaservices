<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data);

if(!empty($_POST))
{
	$data['product_options'] = $_POST['product_options'];
	$data['product_options']['product_options_id'] = $_POST['product_options'];
}
?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="product_options_edit_add">
		
			<input type="hidden" name="product_options[product_options_id]" value="<?=(isset($data['product_options']['product_options_id']) ? $data['product_options']['product_options_id'] : '');?>">
			
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('product_options_menu_save');?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $this->url->segment(3);?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'add_product_options'): ?>

						<div class="header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-info"></div>
							<h1><?=$this->lang->line('product_options_add');?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit_product_options'): ?>

						<div class="header">
							<input type="hidden" name="product_options[language][code]" value="<?=$data['product_options']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['product_options']['language']['code']?>"></div>
							<h1><?=$this->lang->line('product_options_edit');?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit_product_options/<?=$this->url->segment(3);?>/<?=$data['product_options']['product_options_id']?>/<?=$language['language_id'];?>">
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
				
					<h2><?=$this->lang->line('product_options_info');?></h2>
					
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
							<th><?=$this->lang->line('product_options_title');?></th>
							<td><input type="text" name="product_options[title]" value="<?=(isset($data['product_options']['title']) ? $data['product_options']['title'] : '');?>"></td>
						</tr>
						
						<tr <?php echo ((permission_level() == 2)?"style='background:#ffede1'":"class='hidden'");?>>
							<th><?=$this->lang->line('superadmin');?></th>
							<td><input type="checkbox" name="product_options[superadmin]" value="1" <?=((isset($data['product_options']['superadmin']) && $data['product_options']['superadmin'] == 1) ? 'checked' : '');?>></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('active');?></th>
							<td>
								<?php if(isset($data['product_options']['sub_active'])): ?>
								
									<?php if($data['product_options']['sub_active'] == 1): ?>
									
										<input type="radio" name="product_options[sub_active]" value="1" checked="checked">Ja
										<input type="radio" name="product_options[sub_active]" value="0">Nee
									
									<?php else: ?>
									
										<input type="radio" name="product_options[sub_active]" value="1">Ja
										<input type="radio" name="product_options[sub_active]" value="0" checked="checked">Nee
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="product_options[sub_active]" value="1" checked="checked">Ja
									<input type="radio" name="product_options[sub_active]" value="0">Nee
								
								<?php endif; ?>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('product_options_title_type');?></th>
							<td>
								<?php if(isset($data['product_options']['sub_active'])): ?>
									<input type="radio" name="product_options[type]" value="1" <?php echo (($data['product_options']['type'] == 1 || !$data['product_options']['type'])?'checked="checked"':'');?>><?=$this->lang->line('product_options_title_type_1');?>
									<input type="radio" name="product_options[type]" value="2" <?php echo (($data['product_options']['type'] == 2)?'checked="checked"':'');?>><?=$this->lang->line('product_options_title_type_2');?>
									<input type="radio" name="product_options[type]" value="3" <?php echo (($data['product_options']['type'] == 3)?'checked="checked"':'');?>><?=$this->lang->line('product_options_title_type_3');?>
									<input type="radio" name="product_options[type]" value="4" <?php echo (($data['product_options']['type'] == 4)?'checked="checked"':'');?>><?=$this->lang->line('product_options_title_type_4');?>
								<?php else: ?>
									<input type="radio" name="product_options[type]" value="1" checked="checked"><?=$this->lang->line('product_options_title_type_1');?>
									<input type="radio" name="product_options[type]" value="2"><?=$this->lang->line('product_options_title_type_2');?>
									<input type="radio" name="product_options[type]" value="3"><?=$this->lang->line('product_options_title_type_3');?>
									<input type="radio" name="product_options[type]" value="4"><?=$this->lang->line('product_options_title_type_4');?>
								<?php endif; ?>
							</td>
						</tr>
						
						<?php if($this->url->segment(2) == 'add_product_options'): ?>

							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
								<td>
									<input type="text" name="product_options[options][1]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							
							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>2</span></th>
								<td>
									<input type="text" name="product_options[options][2]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							<tr class='option_row'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
								<td>
									<input type="text" name="product_options[options][3]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
							<tr class='option_row hidden'>
								<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
								<td>
									<input type="text" name="product_options[options][]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>

						<?php endif; ?>
						
						<?php if($this->url->segment(2) == 'edit_product_options'): ?>
							
							<?php if(!empty($data['product_options']['options'])): ?>
								<?php foreach($data['product_options']['options'] as $k => $options): ?>
									<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span><?=$k+1;?></span></th>
									<td>
										<input type="text" class="product_options_input" name="product_options[options][<?=$options['product_options_item_id'];?>]" value="<?=(isset($options['title']) ? $options['title'] : '');?>">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<tr class='option_row hidden'>
								<th><?=$this->lang->line('option');?> <span class='nummer'><?=$k+1;?></span></th>
								<td>
									<input type="text" name="product_options[options][]" value="" class="product_options_input">
									<span class='spacer_delete'></span>
									<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
								</td>
							</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
									<td>
										<input type="text" name="product_options[options][1]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>2</span></th>
									<td>
										<input type="text" name="product_options[options][2]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<tr class='option_row'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
									<td>
										<input type="text" name="product_options[options][3]" value="" class="product_options_input">
										<span class='spacer_delete'></span>
										<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
									</td>
								</tr>
								<tr class='option_row hidden'>
									<th><?=$this->lang->line('option');?> <span class='nummer'>3</span></th>
									<td>
										<input type="text" name="product_options[options][]" value="" class="product_options_input">
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