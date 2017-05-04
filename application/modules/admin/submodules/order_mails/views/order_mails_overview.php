<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button hidden">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_order_mails');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-order_mails"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
							
							<th style="width: 3%;">
								Nr
							</th>
							<th class="text_left" style="width: 38%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('order_mails_title');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('edit_language');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php if(isset($data['order_mails'])): ?>
						
						<?php $x = 1; ?>
						
						<?php foreach($data['order_mails'] as $order_mails): ?>
						
						
						
						<tr>
							
							<td>
								<?=$x?>
								<?php $x++; ?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_mails['order_mails_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;">
										<?php echo ((isset($data['order_status']) && isset($data['order_status'][$order_mails['order_status_id']]))?$data['order_status'][$order_mails['order_status_id']]:"");?>
								</span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
												
								<?php foreach($data['languages'] as $language): ?>

									<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
									<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/order_mails/edit/<?=$order_mails['order_mails_id']?>/<?=$language['language_id'];?>">
										<span class="language sprite_<?=$language['code']?>"></span>
									</a>
									<?php endif; ?>

								<?php endforeach; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('order_mails', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/order_mails/delete/' . $order_mails['order_mails_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
								</p>
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