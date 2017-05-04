<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['members'] = $_POST['members'];
	$data['members']['date_birth'] = strtotime($_POST['members']['date_birth']);
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="members_edit_add">
		
			<input type="hidden" name="members[member_id]" value="<?=(isset($data['members']['member_id']) && $data['members']['member_id'] != '' ? $data['members']['member_id'] : '');?>">

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
						<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-members"></div>
						<h1><?=$this->lang->line('header_edit');?></h1>
					
					</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="members[language][code]" value="<?=$data['members']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['members']['language']['code']?>"></div>
							<h1><?=$this->lang->line('header_add');?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['members']['member_id']?>/<?=$language['language_id'];?>">
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

					<h2><?=$this->lang->line('members_info');?></h2>
					
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

						<?php if(($this->config->item('max_category_depth')+1) > 1): ?>
						
						<tr>
							
								<th><?=$this->lang->line('category_id')?></th>
								
								<td>
								
									<div class='padded_content'>
										<?php foreach($data['drop_down'] as $members): ?>
												
											<label><input type='checkbox' name='members[category_id][]' value='<?php echo $members['category_id'];?>' <?=(isset($data['members']['category_id']) && in_array($members['category_id'], $data['members']['category_id']) ? 'checked="checked"' : '');?>>&nbsp;&nbsp;<?=$members['title']?></label><br>
												
											<?php if(($this->config->item('max_category_depth')+1) > 2): ?>
												
												<?php foreach($members['children'] as $child): ?>
															
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type='checkbox' name='members[category_id][]' <?=(isset($data['members']['category_id']) && in_array($child['category_id'], $data['members']['category_id']) ? 'checked="checked"' : '');?> value="<?=$child['category_id']?>">&nbsp;&nbsp;<?=$child['title']?></label><br>
																	
												<?php endforeach; ?>
														
											<?php endif; ?>
													
										<?php endforeach; ?>
									</div>
								</td>
								
							</tr>
						
						<?php endif; ?>
						
						<tr>
							<th><?=$this->lang->line('sub_active');?></th>
							<td>
								<?php if(isset($data['members']['sub_active'])): ?>
								
									<?php if($data['members']['sub_active'] == 1): ?>
									
										<input type="radio" name="members[sub_active]" value="1" checked="checked">Ja
										<input type="radio" name="members[sub_active]" value="0">Nee
									
									<?php else: ?>
									
										<input type="radio" name="members[sub_active]" value="1">Ja
										<input type="radio" name="members[sub_active]" value="0" checked="checked">Nee
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="members[sub_active]" value="1" checked="checked">Ja
									<input type="radio" name="members[sub_active]" value="0">Nee
								
								<?php endif; ?>
							</td>
						</tr>

						<tr>
							<th><?=$this->lang->line('date_birth');?></th>
							<td><input class="datepicker" readonly="readonly" type="text" name="members[date_birth]" value="<?=(isset($data['members']['date_birth']) ? date('d-m-Y', $data['members']['date_birth']) : date('d-m-Y', time()));?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('company_name');?></th>
							<td><input type="text" name="members[company_name]" value="<?=(isset($data['members']['company_name']) ? $data['members']['company_name'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('firstname');?></th>
							<td><input type="text" name="members[firstname]" value="<?=(isset($data['members']['firstname']) ? $data['members']['firstname'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('lastname');?></th>
							<td><input type="text" name="members[lastname]" value="<?=(isset($data['members']['lastname']) ? $data['members']['lastname'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('email');?></th>
							<td><input type="text" name="members[email]" value="<?=(isset($data['members']['email']) ? $data['members']['email'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('telephone');?></th>
							<td><input type="text" name="members[telephone]" value="<?=(isset($data['members']['telephone']) ? $data['members']['telephone'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('mobile');?></th>
							<td><input type="text" name="members[mobile]" value="<?=(isset($data['members']['mobile']) ? $data['members']['mobile'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('website');?></th>
							<td><input type="text" name="members[website]" value="<?=(isset($data['members']['website']) ? $data['members']['website'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('street');?></th>
							<td><input type="text" name="members[street]" value="<?=(isset($data['members']['street']) ? $data['members']['street'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('postal');?></th>
							<td><input type="text" name="members[postal]" value="<?=(isset($data['members']['postal']) ? $data['members']['postal'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('city');?></th>
							<td><input type="text" name="members[city]" value="<?=(isset($data['members']['city']) ? $data['members']['city'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_description');?></th>
							<td><textarea class="editor" name="members[description]"><?=(isset($data['members']['description']) ? $data['members']['description'] : '');?></textarea></td>
						</tr>

					</table>
				
				</div>
				
			</div>

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
							
								<a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['members']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
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
								
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['members']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
									<a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['members']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
								
								</div>
								
								<div class="edit_image">
								
									<img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
									<a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['members']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
								
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
	MIN_IMG_W =	<?=MEMBERS_CROP_MAX_W;?>;
	MIN_IMG_H = <?=MEMBERS_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>