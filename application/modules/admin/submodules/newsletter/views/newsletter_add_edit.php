<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['newsletter'] = $_POST['newsletter'];
	$data['newsletter']['date'] = strtotime($_POST['newsletter']['date']);
}

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="newsletter_edit_add">
		
			<input type="hidden" name="newsletter[newsletter_id]" value="<?=(isset($data['newsletter']['newsletter_id']) ? $data['newsletter']['newsletter_id'] : '');?>">
			
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
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-newsletter"></div>
							<h1><?=$this->lang->line('add_newsletter')?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="newsletter[language][code]" value="<?=$data['newsletter']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['newsletter']['language']['code']?>"></div>
							<h1><?=$this->lang->line('edit_newsletter')?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['newsletter']['newsletter_id']?>/<?=$language['language_id'];?>">
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
							<td><input type="text" name="newsletter[title]" value="<?=(isset($data['newsletter']['title']) ? $data['newsletter']['title'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('active');?></th>
							<td>
								<?php if(isset($data['newsletter']['sub_active'])): ?>
								
									<?php if($data['newsletter']['sub_active'] == 1): ?>
									
										<input type="radio" name="newsletter[sub_active]" value="1" checked="checked">Ja
										<input type="radio" name="newsletter[sub_active]" value="0">Nee
									
									<?php else: ?>
									
										<input type="radio" name="newsletter[sub_active]" value="1">Ja
										<input type="radio" name="newsletter[sub_active]" value="0" checked="checked">Nee
									
									<?php endif; ?>
									
								<?php else: ?>
								
									<input type="radio" name="newsletter[sub_active]" value="1" checked="checked">Ja
									<input type="radio" name="newsletter[sub_active]" value="0">Nee
								
								<?php endif; ?>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('date');?></th>
							<td><input class="datepicker" readonly="readonly" type="text" name="newsletter[date]" value="<?=(isset($data['newsletter']['date']) ? date('d-m-Y', $data['newsletter']['date']) : date('d-m-Y', time()));?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('event');?></th>
							<td>
								<select name='newsletter[event_id]'>
									<option value=''></option>
									<?php
									if(isset($data['events']) && count($data['events']) > 0){
										foreach($data['events'] as $event){
											?>
											<option value='<?php echo $event['event_id'];?>' <?=((isset($data['newsletter']['event_id']) && $data['newsletter']['event_id'] == $event['event_id']) ? 'selected' : '');?>><?php echo $event['title'];?></option>
											<?php
										}
									}
									?>
								</select>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('news');?></th>
							<td>
								<?php
								if(isset($data['news_items']) && count($data['news_items']) > 0){
									foreach($data['news_items'] as $event){
										?>
										<label><input type='checkbox' name='newsletter[news][]' value='<?php echo $event['news_id'];?>' <?=((isset($data['newsletter']['news']) && count($data['newsletter']['news']) > 0 && in_array($event['news_id'], $data['newsletter']['news'])) ? 'checked' : '');?>>&nbsp;&nbsp;<?php echo $event['title'];?></label><br>
										<?php
									}
								}
								?>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_description');?></th>
							<td><textarea class="count[120]" name="newsletter[description]"><?=(isset($data['newsletter']['description']) ? $data['newsletter']['description'] : '');?></textarea></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_begin');?></th>
							<td><textarea name="newsletter[content_begin]" class="editor"><?=(isset($data['newsletter']['content_begin']) ? $data['newsletter']['content_begin'] : '');?></textarea></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content_end');?></th>
							<td><textarea name="newsletter[content_end]" class="editor"><?=(isset($data['newsletter']['content_end']) ? $data['newsletter']['content_end'] : '');?></textarea></td>
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
		
	</div>

</div>

<script type="text/javascript">
	MIN_IMG_W =	<?=newsletter_CROP_MAX_W;?>;
	MIN_IMG_H = <?=newsletter_CROP_MAX_H;?>;
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>