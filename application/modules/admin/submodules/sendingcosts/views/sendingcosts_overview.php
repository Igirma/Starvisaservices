<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_sendingcosts');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-sendingcosts"></div>
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
								<p style="padding-left: 10px;"><?=$this->lang->line('country');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('value');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('discount_value');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('discount_type');?></p>
							</th>
							<th style="width: 12%;">
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
					
					<?php if(isset($data['sendingcosts'])): ?>
						
						<?php $x = 1; ?>
						
						<?php foreach($data['sendingcosts'] as $sendingcosts): ?>

						<tr>
							
							<td>
								<?=$x?>
								<?php $x++; ?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $sendingcosts['country_id'] ?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($sendingcosts['name'], 27);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td>
							
								<div class="spacer"></div>
								<p>
								<?=$sendingcosts['discount_value'];?>
								</p>
							</td>
							
							<td>
								<div class="spacer"></div>
								<p>
								<?=$sendingcosts['discount_top_value'];?>
								</p>
							</td>

							<td>
								<div class="spacer"></div>
								<p>
								<?=$sendingcosts['discount_description'];?>
								</p>
							</td>
							
							<td>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('sendingcosts', 'edit')): ?>
								
									<?php if($sendingcosts['staffel_active'] == 1): ?>
									
									<input type="radio" name="active[<?=$sendingcosts['staffel_id'];?>]" checked="checked" value="1">Ja
									<input type="radio" name="active[<?=$sendingcosts['staffel_id'];?>]" value="0">Nee
									
									<?php else: ?>
									
									<input type="radio" name="active[<?=$sendingcosts['staffel_id'];?>]" value="1">Ja
									<input type="radio" name="active[<?=$sendingcosts['staffel_id'];?>]" checked="checked" value="0">Nee
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('sendingcosts', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/sendingcosts/delete/' . $sendingcosts['staffel_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
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