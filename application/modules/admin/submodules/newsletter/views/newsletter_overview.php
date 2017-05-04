<?php require_once ELEM_DIR . 'admin_header.php';
 ?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/add"><?=$this->lang->line('add_newsletter');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-newsletter"></div>
				<h1><?=($this->url->segment(2) == 'archive' ? $this->lang->line('overview_archive_header') : $this->lang->line('overview_header'));?></h1>
			
			</div>
			
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
						
							<th style="width: 3%;">
								Nr
							</th>
							<th class="text_left" style="width: 27%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('edit_language');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('date');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('active');?></p>
							</th>
							<th style="width: 25%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('send');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php if(isset($data['newsletter'])): ?>
					
						<?php $i = 0; ?>
						<?php foreach($data['newsletter'] as $newsletter): ?>
						
						<tr>
						
							<td><?=++$i;?></td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $newsletter['newsletter_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($newsletter['title'], 35);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>
							
							<td>
							
								<div class="spacer"></div>
								
								<p>
												
								<?php foreach($data['languages'] as $language): ?>

									<?php $sub_active = $this->db->query('SELECT `newsletter_content`.sub_active FROM `newsletter_content` WHERE `newsletter_content`.language_id = ? AND `newsletter_content`.newsletter_id = ?', array($language['language_id'], $newsletter['newsletter_id'])); ?>
									
									<?php if($sub_active[0]['sub_active'] == 1): ?>
										
										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/newsletter/edit/<?=$newsletter['newsletter_id']?>/<?=$language['language_id'];?>">
											<span class="language sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php else: ?>
									
									<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/newsletter/edit/<?=$newsletter['newsletter_id']?>/<?=$language['language_id'];?>">
											<span class="language_grey sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php endif; ?>

								<?php endforeach; ?>
								
								</p>
							
							</td>
							
							<td>
							
								<div class="spacer"></div>
								
								<p><?=date('d-m-Y', $newsletter['date']);?></p>
								
							</td>
							
							<td>
								
								<div class="spacer"></div>
								
								<p>
							
								<?php if($newsletter['active'] == 1): ?>
								
								<input type="radio" name="active[<?=$newsletter['newsletter_id'];?>]" checked="checked" value="1">Ja
								<input type="radio" name="active[<?=$newsletter['newsletter_id'];?>]" value="0">Nee
								
								<?php else: ?>
								
								<input type="radio" name="active[<?=$newsletter['newsletter_id'];?>]" value="1">Ja
								<input type="radio" name="active[<?=$newsletter['newsletter_id'];?>]" checked="checked" value="0">Nee
								
								<?php endif; ?>
								
								
								</p>
								
							</td>
							
							<td>
								<div class="spacer"></div>
							
								<p>
									<select name='group[<?=$newsletter['newsletter_id'];?>]' style='width:140px;'>
										<option value='0'><?=$this->lang->line('all_members');?></option>
										<?php
										if(isset($data['groups']) && count($data['groups']) > 0){
											foreach($data['groups'] as $item){
												?>
												<option value='<?php echo $item['category_id'];?>'><?=$item['title'];?></option>
												<?php
											}
										}
										?>
									</select>&nbsp;&nbsp;
									<input type='submit' name='newsletter_send' value='<?=$newsletter['newsletter_id'];?>' class='newsletter_send'>
									&nbsp;
									<?=$newsletter['count_sended'];?>
								</p>
							</td>
							
							<td>
								<div class="spacer"></div>
							
								<p><?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/delete/' . $newsletter['newsletter_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
							</td>
						
						</tr>
						
						<?php endforeach; ?>
						
					<?php endif; ?>
					
					</tbody>
				
				</table>
				
			</form>
		</div>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>