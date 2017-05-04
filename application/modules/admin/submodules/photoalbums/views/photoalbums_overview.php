<?php require_once ELEM_DIR . 'admin_header.php'; 
?>

<?=$this->alert->show();?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_photoalbum');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 10px;" class="sprite sprite-photoalbums"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
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
								<p style="padding-left: 10px;"><?=$this->lang->line('photoalbum_title');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('picture_count');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('edit_language');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('order');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('active');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php if(isset($data['photoalbums'])): ?>
					
						<?php $i = 0; ?>
							
						<?php $count = count($data['photoalbums']); ?>
						
						<?php foreach($data['photoalbums'] as $photoalbum): ?>
						
							<?++$i;?>
						
							<tr>
								
								<td>
									<?=$i?>
								</td>
								
								<td class="text_left">
									
									<?php if(permission(CONTROLLER, 'edit')):?>
										<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $photoalbum['album_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
									<?php endif; ?>
									
									<div class="spacer"></div>
								
									<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($photoalbum['title'], 25);?></span>

									<?php if(permission(CONTROLLER, 'edit')):?>
										</a>
									<?php endif; ?>

								</td>
								
								<td>
									<div class="spacer"></div>
									<p><?=$photoalbum['picture_count'];?></p>
								</td>
								
								<td>
									
									<div class="spacer"></div>
									
									<p>
								
									<?php foreach($data['languages'] as $language): ?>

										<?php $sub_active = $this->db->query('SELECT `album_content`.sub_active FROM `album_content` WHERE `album_content`.language_id = ? AND `album_content`.album_id = ?', array($language['language_id'], $photoalbum['album_id'])); ?>
										
										<?php if($sub_active[0]['sub_active'] == 1): ?>
											
											<?php if(permission('photoalbums', 'edit', $language['language_id'])): ?>
											
												<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/photoalbums/edit/<?=$photoalbum['album_id']?>/<?=$language['language_id'];?>">
													<span class="language sprite_<?=$language['code']?>"></span>
												</a>
											
											<?php endif; ?>
										
										<?php else: ?>
										
										<?php if(permission('photoalbums', 'edit', $language['language_id'])): ?>
											
											<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/photoalbums/edit/<?=$photoalbum['album_id']?>/<?=$language['language_id'];?>">
												<span class="language_grey sprite_<?=$language['code']?>"></span>
											</a>
											
											<?php endif; ?>
										
										<?php endif; ?>

									<?php endforeach; ?>
									
									</p>
								
								</td>
								
								<td class="order">
								
									<div class="spacer"></div>
									
									<p>
									
									<?php if(permission(CONTROLLER, 'edit')): ?>
									
										<?php if($i == 1 && $i != $count): ?>
										
										<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL;?>admin/photoalbums/order/down/<?=$photoalbum['order'];?>/<?=$photoalbum['album_id'];?>">
											<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
										</a>
										
										<?php endif; ?>
										
										<?php if($i != 1 && $i == $count): ?>
										
										<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL;?>admin/photoalbums/order/up/<?=$photoalbum['order'];?>/<?=$photoalbum['album_id'];?>">
											<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
										</a>
										
										<?php endif; ?>
										
										<?php if($i != 1 && $i != $count): ?>
										
										<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL;?>admin/photoalbums/order/up/<?=$photoalbum['order'];?>/<?=$photoalbum['album_id'];?>">
											<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
										</a>
										
										<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL;?>admin/photoalbums/order/down/<?=$photoalbum['order'];?>/<?=$photoalbum['album_id'];?>">
											<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
										</a>
										
										<?php endif; ?>
									
									<?php endif; ?>
									
									</p>
								
								</td>
								
								<td>
								
									<div class="spacer"></div>
									
									<p>
									
									<?php if($photoalbum['active'] == 1): ?>
									
									<input type="radio" name="active[<?=$photoalbum['album_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes')?>
									<input type="radio" name="active[<?=$photoalbum['album_id'];?>]" value="0"><?=$this->lang->line('no')?>
									
									<?php else: ?>
									
									<input type="radio" name="active[<?=$photoalbum['album_id'];?>]" value="1"><?=$this->lang->line('yes')?>
									<input type="radio" name="active[<?=$photoalbum['album_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no')?>
									
									<?php endif; ?>
									
									</p>
									
								</td>
								
								<td>
									<div class="spacer"></div>
									<?=(permission(CONTROLLER, 'delete') ? '<p><a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/photoalbums/delete/' . $photoalbum['album_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a></p>' : '');?>
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